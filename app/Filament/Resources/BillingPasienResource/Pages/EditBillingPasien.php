<?php

namespace App\Filament\Resources\BillingPasienResource\Pages;

use App\Filament\Resources\BillingPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBillingPasien extends EditRecord
{
    protected static string $resource = BillingPasienResource::class;

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
