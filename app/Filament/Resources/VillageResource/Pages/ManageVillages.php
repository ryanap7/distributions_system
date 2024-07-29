<?php

namespace App\Filament\Resources\VillageResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\VillageResource;
use Filament\Resources\Pages\ManageRecords;

class ManageVillages extends ManageRecords
{
    protected static string $resource = VillageResource::class;

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FourExtraLarge;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalWidth('md'),
        ];
    }
}
