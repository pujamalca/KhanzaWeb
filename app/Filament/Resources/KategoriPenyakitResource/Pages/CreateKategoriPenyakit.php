<?php

namespace App\Filament\Resources\KategoriPenyakitResource\Pages;

use App\Filament\Resources\KategoriPenyakitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKategoriPenyakit extends CreateRecord
{
    protected static string $resource = KategoriPenyakitResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
