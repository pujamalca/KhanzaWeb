<?php

namespace App\Filament\Resources\PenyakitResource\Pages;

use App\Filament\Resources\PenyakitResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePenyakit extends CreateRecord
{
    protected static string $resource = PenyakitResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
