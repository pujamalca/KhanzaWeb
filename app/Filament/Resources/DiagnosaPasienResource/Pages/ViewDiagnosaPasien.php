<?php

namespace App\Filament\Resources\DiagnosaPasienResource\Pages;

use App\Filament\Resources\DiagnosaPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDiagnosaPasien extends ViewRecord
{
    protected static string $resource = DiagnosaPasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
