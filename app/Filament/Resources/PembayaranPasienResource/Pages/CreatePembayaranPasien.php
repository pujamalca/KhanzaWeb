<?php

namespace App\Filament\Resources\PembayaranPasienResource\Pages;

use App\Filament\Resources\PembayaranPasienResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePembayaranPasien extends CreateRecord
{
    protected static string $resource = PembayaranPasienResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tgl_bayar'] = $data['tgl_bayar'] ?? now()->toDateString();
        $data['jam_bayar'] = $data['jam_bayar'] ?? now()->format('H:i:s');

        return $data;
    }
}
