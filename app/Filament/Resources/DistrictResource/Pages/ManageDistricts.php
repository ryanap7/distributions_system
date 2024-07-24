<?php

namespace App\Filament\Resources\DistrictResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\DistrictResource;

class ManageDistricts extends ManageRecords
{
    protected static string $resource = DistrictResource::class;

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
