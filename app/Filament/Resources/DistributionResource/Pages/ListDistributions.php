<?php

namespace App\Filament\Resources\DistributionResource\Pages;

use App\Filament\Resources\DistributionResource;
use App\Models\Distribution;
use App\Models\District;
use Barryvdh\DomPDF\Facade\Pdf;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Pages\ListRecords;

class ListDistributions extends ListRecords
{
    protected static string $resource = DistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadLaporan')
                ->label('Download Laporan')
                ->action(function (array $data) {
                    // Query dasar
                    $query = Distribution::with(['recipient', 'village.district']);

                    // Filter berdasarkan tanggal
                    if (!empty($data['start_date']) && !empty($data['end_date'])) {
                        $query->whereBetween('created_at', [$data['start_date'], $data['end_date']]);
                    }

                    // Filter berdasarkan kecamatan
                    if (!empty($data['district_id'])) {
                        $query->whereHas('village', function ($q) use ($data) {
                            $q->where('district_id', $data['district_id']);
                        });
                    }

                    // Ambil data sesuai query
                    $distributions = $query->get();

                    // Generate PDF
                    $pdf = Pdf::loadView('filament.pages.pdf', compact('distributions'));

                    $districtName = !empty($data['district_id']) ? District::find($data['district_id'])->name : 'SemuaKecamatan';
                    $startDate = !empty($data['start_date']) ? \Carbon\Carbon::parse($data['start_date'])->format('Ymd') : 'TanggalAwal';
                    $endDate = !empty($data['end_date']) ? \Carbon\Carbon::parse($data['end_date'])->format('Ymd') : 'TanggalAkhir';

                    // Format nama file
                    $filename = "Laporan-{$districtName}-{$startDate}-{$endDate}.pdf";

                    // Download PDF
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, $filename);
                })
                ->form([
                    Forms\Components\DatePicker::make('start_date')->label('Tanggal Mulai'),
                    Forms\Components\DatePicker::make('end_date')->label('Tanggal Selesai'),
                    Forms\Components\Select::make('district_id')
                        ->label('Kecamatan')
                        ->options(District::query()->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function ($set, $state) {
                            $set('village_id', null);
                        }),
                ]),
        ];
    }
}
