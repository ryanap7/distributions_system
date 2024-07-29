<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Imports\RecipientImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Filament\Resources\UserResource;
use App\Imports\UserAccountImport;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ManageRecords;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('template')
                ->icon('heroicon-o-document-text')
                ->action(function () {
                    $path = public_path('template/perangkat_desa.xlsx');

                    if (file_exists($path)) {
                        return response()->download($path);
                    } else {
                        return Notification::make()->danger()->title('Opppss...')->body('File tidak ditemukan')->send();
                    }
                }),
            Action::make('import')
                ->label('Import')
                ->icon('heroicon-o-arrow-up-tray')
                ->modalWidth('md')
                ->form([
                    FileUpload::make('file')
                        ->label('File')
                        ->disk('public')
                        ->directory('import-users')
                        ->visibility('private')
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel'
                        ])

                ])
                ->action(function ($data) {
                    Excel::import(new UserAccountImport, $data['file'], 'public');

                    Notification::make()
                        ->title('Import')
                        ->body('Import berhasil dilakukan')
                        ->persistent()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
