<?php

namespace App\Filament\Resources\PembayaranPasienResource\Pages;

use App\Filament\Resources\PembayaranPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPembayaranPasien extends EditRecord
{
    protected static string $resource = PembayaranPasienResource::class;

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
