<?php

namespace App\Filament\Resources\ICD9Resource\Pages;

use App\Filament\Resources\ICD9Resource;
use Filament\Resources\Pages\CreateRecord;

class CreateICD9 extends CreateRecord
{
    protected static string $resource = ICD9Resource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
