<?php

namespace App\Filament\Resources\DatabarangResource\Pages;

use App\Filament\Resources\DatabarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDatabarang extends EditRecord
{
    protected static string $resource = DatabarangResource::class;

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
