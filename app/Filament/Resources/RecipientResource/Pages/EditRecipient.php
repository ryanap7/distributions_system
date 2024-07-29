<?php

namespace App\Filament\Resources\RecipientResource\Pages;

use App\Filament\Resources\RecipientResource;
use App\Models\Village;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecipient extends EditRecord
{
    protected static string $resource = RecipientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['district_id'] = Village::find($data['village_id'])->district_id;

        return $data;
    }
}
