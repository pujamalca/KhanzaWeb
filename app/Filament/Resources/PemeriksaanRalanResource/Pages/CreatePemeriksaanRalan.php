<?php

namespace App\Filament\Resources\PemeriksaanRalanResource\Pages;

use App\Filament\Resources\PemeriksaanRalanResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePemeriksaanRalan extends CreateRecord
{
    protected static string $resource = PemeriksaanRalanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure date and time are set
        $data['tgl_perawatan'] = $data['tgl_perawatan'] ?? now()->toDateString();
        $data['jam_rawat'] = $data['jam_rawat'] ?? now()->format('H:i:s');

        return $data;
    }
}
