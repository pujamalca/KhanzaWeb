<?php

namespace App\Filament\Resources\UgdResource\Pages;

use App\Filament\Resources\UgdResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUgd extends CreateRecord
{
    protected static string $resource = UgdResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index'); // ✅ langsung ke list
    }
}
