<?php

namespace App\Filament\Resources\DiagnosaPasienResource\Pages;

use App\Filament\Resources\DiagnosaPasienResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDiagnosaPasien extends CreateRecord
{
    protected static string $resource = DiagnosaPasienResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tgl_diagnosis'] = $data['tgl_diagnosis'] ?? now()->toDateString();
        $data['jam_diagnosis'] = $data['jam_diagnosis'] ?? now()->format('H:i:s');

        return $data;
    }
}
