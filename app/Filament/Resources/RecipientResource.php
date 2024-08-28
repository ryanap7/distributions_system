<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RecipientResource\Pages;
use App\Models\District;
use App\Models\Recipient;
use App\Models\Village;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RecipientResource extends Resource
{
    protected static ?string $model = Recipient::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $modelLabel = 'Penerima Bantuan';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Penerima')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nik')
                    ->label('NIK')
                    ->required()
                    ->unique('App\Models\Recipient', 'nik', ignoreRecord: true)
                    ->maxLength(16),
                Forms\Components\Select::make('district_id')
                    ->label('Kecamatan')
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('village_id', null);
                    })
                    ->required()
                    ->options(District::query()->pluck('name', 'id')),
                Forms\Components\Select::make('village_id')
                    ->label('Desa')
                    ->searchable()
                    ->live()
                    ->preload()
                    ->options(function (Get $get) {
                        if ($get('district_id')  !== null) {
                            return  Village::query()->where('district_id', $get('district_id'))->pluck('name', 'id');
                        }

                        return [];
                    })
                    ->required(),
                Forms\Components\FileUpload::make('ktp_photo')
                    ->label('Foto KTP')
                    ->columnSpanFull()
                    ->disk('public')
                    ->directory('ktp')
                    ->visibility('public')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(['name']),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(['nik']),
                Tables\Columns\TextColumn::make('district.name')
                    ->label('Kecamatan')
                    ->sortable()
                    ->searchable(['districts.name']),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->sortable()
                    ->searchable(['villages.name']),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('village_id')
                    ->form([
                        Forms\Components\Select::make('district_id')
                            ->label('Kecamatan')
                            ->options(District::query()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($set, $state) {
                                $set('village_id', null);
                            }),
                        Forms\Components\Select::make('village_id')
                            ->label('Desa')
                            ->options(function ($get) {
                                if (!$get('district_id')) {
                                    return [];
                                }
                                return Village::query()->where('district_id', $get('district_id'))->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->live(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['district_id'],
                                fn(Builder $query, $search): Builder => $query->when($search, function ($qq) use ($search) {
                                    $qq->whereHas('village', function ($q) use ($search) {
                                        $q->where('district_id', $search);
                                    });
                                }),
                            )
                            ->when(
                                $data['village_id'],
                                fn(Builder $query, $search): Builder => $query->where('village_id', $search),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['district_id'] ?? null) {

                            $district = District::find($data['district_id']);

                            $indicators['district_id'] = 'Kecamatan: ' .  $district->name;
                        }

                        if ($data['village_id'] ?? null) {
                            $village = Village::find($data['village_id']);

                            $indicators['village_id'] = 'Desa: ' . $village->name;
                        }

                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecipients::route('/'),
            'create' => Pages\CreateRecipient::route('/create'),
            'edit' => Pages\EditRecipient::route('/{record}/edit'),
        ];
    }
}
