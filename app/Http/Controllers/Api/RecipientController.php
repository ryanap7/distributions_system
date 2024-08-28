<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipientController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $villageId = $request->query('village_id');

        if (!$villageId) {
            return response()->json([
                'message' => 'Parameter salah',
            ], 400);
        }

        $perPage = $request->query('per_page', 10);

        $recipients = Recipient::where('village_id', $villageId)->paginate($perPage);

        $paginationInfo = [
            'count' => $recipients->total(),
            'pages' => $recipients->lastPage(),
        ];

        $nextPageUrl = $recipients->appends($request->except('page'))->nextPageUrl();
        $prevPageUrl = $recipients->previousPageUrl();

        $response = [
            'message' => 'Success',
            'data' => $recipients->items(),
            'info' => [
                'count' => $paginationInfo['count'],
                'pages' => $paginationInfo['pages'],
                'next' => $nextPageUrl,
                'prev' => $prevPageUrl,
            ],
        ];

        return response()->json($response);
    }

    public function getAll(Request $request): JsonResponse
    {
        // Ambil parameter village_id dari request
        $villageId = $request->query('village_id');

        // Jika village_id ada, filter berdasarkan village_id, jika tidak ambil semua data
        $recipients = $villageId ? Recipient::where('village_id', $villageId)->get() : Recipient::all();

        $response = [
            'message' => 'Success',
            'data' => $recipients,
        ];

        return response()->json($response);
    }
}
