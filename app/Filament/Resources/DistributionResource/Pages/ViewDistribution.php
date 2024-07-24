<?php

namespace App\Filament\Resources\DistributionResource\Pages;

use Filament\Actions;
use App\Models\Recipient;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\DistributionResource;
use App\Models\Distribution;

class ViewDistribution extends ViewRecord
{
    protected static string $resource = DistributionResource::class;

    protected static string $view = 'filament.pages.distribution-view';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $distribution = Distribution::find($data['id']);

        if ($distribution) {
            $recipient = $distribution->recipient;
            $data['recipient_name'] = $recipient->name;
            $data['recipient_nik'] = $recipient->nik;
            $data['recipient_photo'] = $distribution->recipient_photo;
            $data['ktp_photo'] = $recipient->ktp_photo;
            $data['district_name'] = $recipient->village->district->name;
            $data['village_name'] = $recipient->village->name;
        }

        return $data;
    }
}
