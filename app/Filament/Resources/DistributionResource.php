<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Village;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\District;
use Filament\Forms\Form;
use App\Models\Recipient;
use Filament\Tables\Table;
use Filament\Support\RawJs;
use App\Models\Distribution;
use Filament\Resources\Resource;
use App\Filament\Resources\DistributionResource\Pages;
use Filament\Tables\Filters\SelectFilter;

class DistributionResource extends Resource
{
    protected static ?string $model = Distribution::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $modelLabel = 'Distribusi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('district_id')
                    ->disabled()
                    ->label('Kecamatan')
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('village_id', null);
                        $set('recipient_id', null);
                    })
                    ->options(District::query()->pluck('name', 'id')),
                Forms\Components\Select::make('village_id')
                    ->disabled()
                    ->label('Desa')
                    ->searchable()
                    ->live()
                    ->preload()
                    ->afterStateUpdated(function (Set $set) {
                        $set('recipient_id', null);
                    })
                    ->options(function (Get $get) {
                        if ($get('district_id')  !== null) {
                            return  Village::query()->where('district_id', $get('district_id'))->pluck('name', 'id');
                        }

                        return [];
                    }),
                Forms\Components\Select::make('recipient_id')
                    ->disabled()
                    ->label('Penerima')
                    ->preload()
                    ->searchable()
                    ->options(function (Get $get) {
                        if ($get('village_id')  !== null) {
                            return  Recipient::where('village_id', $get('village_id'))->get()->pluck('name', 'id');
                        }

                        return [];
                    })
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->disabled()
                    ->native(false)
                    ->format('d-m-Y')
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->disabled()
                    ->required(),
                Forms\Components\TextInput::make('stage')
                    ->disabled()
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->columnSpanFull()
                    ->prefix('Rp. ')
                    ->mask(RawJs::make('$money($input, `,`)'))
                    ->stripCharacters('.')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('recipient.name')
                    ->searchable()
                    ->label('Nama Penerima')
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->dateTime('d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')->label('Tahun')->sortable(),
                Tables\Columns\TextColumn::make('stage')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->currency('IDR', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('district_id')
                    ->label('Kecamatan')
                    ->preload()
                    ->searchable()
                    ->options(District::query()->pluck('name', 'id'))
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereHas('recipient', function ($qu) use ($data) {
                                $qu->whereHas('village', function ($q) use ($data) {
                                    $q->where('district_id', $data['value']);
                                });
                            });
                        }
                    }),
                SelectFilter::make('village')
                    ->label('Desa')
                    ->preload()
                    ->searchable()
                    ->options(District::query()->pluck('name', 'id'))
                    ->query(function ($query, $data) {
                        if ($data['value']) {
                            $query->whereHas('recipient', function ($qu) use ($data) {
                                $qu->where('village_id', $data['value']);
                            });
                        }
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListDistributions::route('/'),
            'view' => Pages\ViewDistribution::route('/{record}'),
            'edit' => Pages\EditDistribution::route('/{record}/edit'),
        ];
    }
}
