<?php

namespace App\Filament\Resources\BillingPasienResource\Pages;

use App\Filament\Resources\BillingPasienResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBillingPasien extends CreateRecord
{
    protected static string $resource = BillingPasienResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tgl_billing'] = $data['tgl_billing'] ?? now()->toDateString();
        $data['created_by'] = auth()->user()->username ?? null;

        return $data;
    }
}
