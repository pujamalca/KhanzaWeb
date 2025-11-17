<?php

namespace App\Filament\Resources\KodesatuanResource\Pages;

use App\Filament\Resources\KodesatuanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKodesatuan extends CreateRecord
{
    protected static string $resource = KodesatuanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
