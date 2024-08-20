<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Village;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\VillageResource\Pages;
use App\Models\District;
use Filament\Tables\Filters\SelectFilter;

class VillageResource extends Resource
{
    protected static ?string $model = Village::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $modelLabel = 'Desa';

    protected static ?string $navigationGroup = 'Data Master';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Select::make('district_id')
                    ->searchable()
                    ->preload()
                    ->relationship('district', 'name')
                    ->label('Kecamatan')
                    ->required(),
                TextInput::make('name')
                    ->live()
                    ->lazy()
                    ->afterStateUpdated(fn(Set $set, string $state) => $set('slug', Str::slug($state)))
                    ->label('Nama Desa')->required()->maxLength(100),
                Hidden::make('slug')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('district.name')
                    ->label('Nama Kecamatan'),
                TextColumn::make('name')
                    ->label('Nama Desa')
                    ->searchable()
                    ->sortable()
            ])
            ->filters([
                SelectFilter::make('district_id')
                    ->label('Kecamatan')
                    ->preload()
                    ->searchable()
                    ->options(District::query()->pluck('name', 'id'))
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalWidth('md'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageVillages::route('/'),
        ];
    }
}
