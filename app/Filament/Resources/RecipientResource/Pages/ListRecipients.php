<?php

namespace App\Filament\Resources\RecipientResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\RecipientResource;
use App\Imports\RecipientImport;

class ListRecipients extends ListRecords
{
    protected static string $resource = RecipientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('template')
                ->icon('heroicon-o-document-text')
                ->action(function () {
                    $path = public_path('template/penerima_bantuan.xlsx');

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
                        ->directory('import-recipient')
                        ->visibility('private')
                        ->required()
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel'
                        ])

                ])
                ->action(function ($data) {
                    Excel::import(new RecipientImport, $data['file'], 'public');

                    Notification::make()
                        ->title('Import')
                        ->body('Import berhasil dilakukan')
                        ->persistent()
                        ->send();
                }),
            Actions\CreateAction::make()->label('Tambah Penerima'),
        ];
    }
}
