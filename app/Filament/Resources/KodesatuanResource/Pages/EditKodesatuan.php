<?php

namespace App\Filament\Resources\KodesatuanResource\Pages;

use App\Filament\Resources\KodesatuanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKodesatuan extends EditRecord
{
    protected static string $resource = KodesatuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
