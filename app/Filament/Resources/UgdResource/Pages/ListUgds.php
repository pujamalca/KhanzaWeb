<?php

namespace App\Filament\Resources\UgdResource\Pages;

use App\Filament\Resources\UgdResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUgds extends ListRecords
{
    protected static string $resource = UgdResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];

    }
}
