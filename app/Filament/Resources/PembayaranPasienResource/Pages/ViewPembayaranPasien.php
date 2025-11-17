<?php

namespace App\Filament\Resources\PembayaranPasienResource\Pages;

use App\Filament\Resources\PembayaranPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPembayaranPasien extends ViewRecord
{
    protected static string $resource = PembayaranPasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
