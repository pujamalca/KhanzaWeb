<?php

namespace App\Filament\Resources\PemeriksaanRalanResource\Pages;

use App\Filament\Resources\PemeriksaanRalanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPemeriksaanRalan extends EditRecord
{
    protected static string $resource = PemeriksaanRalanResource::class;

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
