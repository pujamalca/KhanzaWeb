<?php

namespace App\Filament\Resources\DiagnosaPasienResource\Pages;

use App\Filament\Resources\DiagnosaPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDiagnosaPasien extends EditRecord
{
    protected static string $resource = DiagnosaPasienResource::class;

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
