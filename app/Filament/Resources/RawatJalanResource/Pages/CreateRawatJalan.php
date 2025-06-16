<?php

namespace App\Filament\Resources\RawatJalanResource\Pages;

use App\Filament\Resources\RawatJalanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRawatJalan extends CreateRecord
{
    protected static string $resource = RawatJalanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // ✅ langsung ke list
    }
}
