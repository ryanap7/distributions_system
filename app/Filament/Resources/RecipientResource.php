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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecipientResource extends Resource
{
    protected static ?string $model = Recipient::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Penerima Bantuan';

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
                    // ->disabled(fn (Get $get) => $get('district_id') === null)
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),
                Tables\Columns\TextColumn::make('district.name')
                    ->label('Kecamatan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('district_id')
                    ->label('Kecamatan')
                    ->preload()
                    ->searchable()
                    ->relationship('district', 'name'),
                SelectFilter::make('village_id')
                    ->label('Desa')
                    ->preload()
                    ->searchable()
                    ->relationship('village', 'name'),
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
