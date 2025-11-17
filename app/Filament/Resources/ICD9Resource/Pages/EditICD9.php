<?php

namespace App\Filament\Resources\ICD9Resource\Pages;

use App\Filament\Resources\ICD9Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditICD9 extends EditRecord
{
    protected static string $resource = ICD9Resource::class;

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
