<?php

namespace App\Filament\Resources\PcarePendaftaranResource\Pages;

use App\Filament\Resources\PcarePendaftaranResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePcarePendaftaran extends CreateRecord
{
    protected static string $resource = PcarePendaftaranResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
