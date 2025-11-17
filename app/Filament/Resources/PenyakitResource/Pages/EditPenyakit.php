<?php

namespace App\Filament\Resources\PenyakitResource\Pages;

use App\Filament\Resources\PenyakitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenyakit extends EditRecord
{
    protected static string $resource = PenyakitResource::class;

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
