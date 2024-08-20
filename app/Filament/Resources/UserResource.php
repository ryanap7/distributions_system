<?php

namespace App\Filament\Resources;

use App\Enums\Roles;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Village;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\District;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\UserResource\Pages;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $modelLabel = 'Akun Pengguna';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('phone_number')
                    ->label('Nomor Telepon')
                    ->maxLength(20),
                Forms\Components\Select::make('district_id')
                    ->label('Kecamatan')
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('village_id', null);
                    })
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
                    }),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->required()
                    ->revealable()
                    ->hiddenOn('edit')
                    ->maxLength(255),
                Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->required()
                    ->multiple()
                    ->preload()
                    ->relationship('roles', 'name')
                    ->maxItems(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Roles::SUPER_ADMIN => 'primary',
                        Roles::ADMIN => 'success',
                        Roles::PERANGKAT_DESA => 'warning',
                    }),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->sortable()
                    ->label('Nomor Telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('village.name')
                    ->label('Desa'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Dirubah pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('village_id')
                    ->label('Desa')
                    ->preload()
                    ->searchable()
                    ->options(Village::all()->pluck('name', 'id')),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('change_password')
                        ->label('Ubah Password')
                        ->requiresConfirmation()
                        ->icon('heroicon-o-key')
                        ->color('success')
                        ->hidden(function () {
                            /** @var \App\Models\User */
                            $user = Auth::user();
                            return !$user->hasRole([Roles::ADMIN, Roles::SUPER_ADMIN]);
                        })
                        ->form([
                            TextInput::make('password')
                                ->password()
                                ->revealable()
                                ->required()
                                ->minLength(6)
                                ->maxLength(30),
                            TextInput::make('password_confirmation')
                                ->label('Konfirmasi Password')
                                ->password()
                                ->revealable()
                                ->same('password')
                                ->required()
                                ->minLength(6)
                                ->maxLength(30),
                        ])->action(fn(Model $record, array $data) => static::changePassword($record, $data)),

                    Tables\Actions\EditAction::make()
                        ->mutateRecordDataUsing(function (array $data): array {
                            $data['district_id'] = Village::find($data['village_id'])?->district_id ?: null;

                            return $data;
                        }),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }

    public static function changePassword(Model $record, array $data): void
    {
        $password = bcrypt($data['password']);

        $record->password = $password;
        $record->save();

        Notification::make()
            ->success()
            ->title('Ubah Password')
            ->body('Password Berhasil diubah')
            ->send();
    }
}
