<?php

namespace App\Filament\Resources\DatabarangResource\Pages;

use App\Filament\Resources\DatabarangResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDatabarang extends CreateRecord
{
    protected static string $resource = DatabarangResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure status is set correctly
        $data['status'] = $data['status'] ?? '1';

        return $data;
    }
}
