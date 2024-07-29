<?php

namespace App\Filament\Resources\DistributionResource\Pages;

use Filament\Actions;
use App\Models\Recipient;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\DistributionResource;

class EditDistribution extends EditRecord
{
    protected static string $resource = DistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $recipient = Recipient::find($data['recipient_id']);
        $data['district_id'] = $recipient->village->district_id;
        $data['village_id'] = $recipient->village_id;

        return $data;
    }
}
