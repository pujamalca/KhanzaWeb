<?php

namespace App\Filament\Resources\ICD9Resource\Pages;

use App\Filament\Resources\ICD9Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListICD9s extends ListRecords
{
    protected static string $resource = ICD9Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
