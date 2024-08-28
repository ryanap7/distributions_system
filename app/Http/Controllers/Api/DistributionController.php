<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\Distribution;
use App\Models\District;
use App\Models\Log;
use App\Models\Recipient;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DistributionController extends Controller
{
    public function getAll(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 20);

        $distributions = Distribution::with(['recipient.district', 'recipient.village'])->paginate($perPage);

        $paginationInfo = [
            'count' => $distributions->total(),
            'pages' => $distributions->lastPage(),
        ];

        $nextPageUrl = $distributions->appends($request->except('page'))->nextPageUrl();
        $prevPageUrl = $distributions->previousPageUrl();

        $response = [
            'message' => 'Success',
            'data' => $distributions->items(),
            'info' => [
                'count' => $paginationInfo['count'],
                'pages' => $paginationInfo['pages'],
                'next' => $nextPageUrl,
                'prev' => $prevPageUrl,
            ],
        ];

        return response()->json($response);
    }

    function create(Request $request): JsonResponse
    {
        $param = $request->only([
            'recipient_id',
            'date',
            'year',
            'stage',
            'recipient_photo',
            'ktp_photo',
            'amount',
            'notes',
        ]);

        $validator = Validator::make($param, [
            'recipient_id' => 'required|integer',
            'date' => 'required|date_format:d-m-Y',
            'stage' => 'required|string',
            'year' => 'required|integer|digits:4',
            'ktp_photo' => 'nullable|image|max:10280',
            'recipient_photo' => 'required|image|max:10280',
            'amount' => 'required|integer',
            'notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Bad Request',
                'errors' => $validator->errors(),
            ], Response::HTTP_BAD_REQUEST);
        }

        // Find recipient
        $recipient = Recipient::find($param['recipient_id']);
        if (!$recipient) {
            return response()->json([
                'message' => 'Data Penerima tidak ditemukan'
            ], Response::HTTP_NOT_FOUND);
        }

        $ktpPhotoPath = $recipient->ktp_photo;
        if ($request->hasFile('ktp_photo')) {
            $ktpPhotoPath = $request->file('ktp_photo')->store('/public/ktp');
            $recipient->ktp_photo = $ktpPhotoPath;
            $recipient->save();
        }

        // Handle recipient photo upload
        if ($request->hasFile('recipient_photo')) {
            $recipientPhotoPath = $request->file('recipient_photo')->store('/public/recipient');
        }

        // Convert stage to integer
        $stage = (int) $param['stage'];

        // Create distribution record
        $distribution = Distribution::create([
            'recipient_id' => $param['recipient_id'],
            'date' => $param['date'],
            'year' => $param['year'],
            'stage' => $stage,
            'recipient_photo' => $recipientPhotoPath,
            'amount' => $param['amount'],
            'notes' => $param['notes'],
        ]);

        // Save the log
        $logMessage = "Kamu baru saja mendistribusikan ke " . $recipient->name . " sebesar Rp. " . number_format($param['amount'], 0, ',', '.');
        Log::create([
            'user_id' => auth()->id(),
            'recipient_id' => $param['recipient_id'],
            'message' => $logMessage,
        ]);

        return response()->json([
            'message' => 'Success',
            'data' => $distribution
        ], Response::HTTP_CREATED);
    }

    public function show(Request $request): JsonResponse
    {
        $recipientId = $request->query('recipient_id');

        if (!$recipientId) {
            return response()->json([
                'message' => 'Parameter salah',
            ], 400);
        }

        $lastDistribution = Distribution::where('recipient_id', $recipientId)
            ->orderByDesc('created_at')
            ->first();

        if (!$lastDistribution) {
            return response()->json([
                'message' => 'Distribusi tidak ditemukan untuk penerima ini'
            ], 404);
        }

        $recipient = Recipient::find($recipientId);

        if (!$recipient) {
            return response()->json([
                'message' => 'Data penerima tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => [
                'last_distribution' => $lastDistribution,
                'recipient' => $recipient,
            ]
        ]);
    }

    /**
     * Count the number of recipients in a specific village.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function countRecipientsByVillageId(Request $request): JsonResponse
    {
        $villageId = $request->query('village_id');

        if (!$villageId) {
            return response()->json([
                'message' => 'Parameter village_id diperlukan',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validasi jika village_id adalah integer
        if (!is_numeric($villageId) || $villageId <= 0) {
            return response()->json([
                'message' => 'Parameter village_id harus berupa angka positif',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Menghitung jumlah penerima berdasarkan village_id
        $recipientCount = Recipient::where('village_id', $villageId)->count();

        if ($recipientCount === 0) {
            return response()->json([
                'message' => 'Tidak ada penerima ditemukan untuk village_id ini'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json([
            'message' => 'Success',
            'data' => [
                'recipient_count' => $recipientCount,
            ]
        ], Response::HTTP_OK);
    }

    public function generateReport(Request $request)
    {
        // Validate request parameters
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'district_id' => 'nullable|exists:districts,id',
        ]);

        // Query dasar
        $query = Distribution::with(['recipient', 'village.district']);

        // Filter berdasarkan tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Filter berdasarkan kecamatan
        if ($request->filled('district_id')) {
            $query->whereHas('village', function ($q) use ($request) {
                $q->where('district_id', $request->district_id);
            });
        }

        // Ambil data sesuai query
        $distributions = $query->get();

        // Generate PDF
        $pdf = Pdf::loadView('filament.pages.pdf', compact('distributions'));

        $districtName = $request->filled('district_id') ? District::find($request->district_id)->name : 'SemuaKecamatan';
        $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date)->format('Ymd') : 'TanggalAwal';
        $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date)->format('Ymd') : 'TanggalAkhir';

        // Format nama file
        $filename = "Laporan-{$districtName}-{$startDate}-{$endDate}.pdf";

        // Save PDF to storage
        $path = 'public/reports/' . $filename;
        Storage::put($path, $pdf->output());

        // Generate URL for the stored file
        $fileUrl = url('storage/reports/' . $filename);

        $response = [
            'message' => 'Success',
            'data' => [
                'url' => $fileUrl
            ],
        ];

        // Return URL for the PDF file
        return response()->json($response);
    }
}
