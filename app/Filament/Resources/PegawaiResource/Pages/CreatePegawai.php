<?php

namespace App\Filament\Resources\PegawaiResource\Pages;

use App\Filament\Resources\PegawaiResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePegawai extends CreateRecord
{
    protected static string $resource = PegawaiResource::class;

    protected function afterCreate(): void
    {
        // Tampilkan notifikasi
        Notification::make()
            ->title('Pegawai berhasil disimpan')
            ->success()
            ->send();

        // Redirect ke halaman list
        $this->redirect($this->getResource()::getUrl('index'));
    }

}
