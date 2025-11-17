<?php

namespace App\Filament\Resources\KategoriPenyakitResource\Pages;

use App\Filament\Resources\KategoriPenyakitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKategoriPenyakit extends EditRecord
{
    protected static string $resource = KategoriPenyakitResource::class;

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
