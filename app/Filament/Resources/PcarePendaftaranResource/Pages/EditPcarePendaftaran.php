<?php

namespace App\Filament\Resources\PcarePendaftaranResource\Pages;

use App\Filament\Resources\PcarePendaftaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPcarePendaftaran extends EditRecord
{
    protected static string $resource = PcarePendaftaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
