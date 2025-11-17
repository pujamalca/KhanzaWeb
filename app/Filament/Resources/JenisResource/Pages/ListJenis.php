<?php

namespace App\Filament\Resources\JenisResource\Pages;

use App\Filament\Resources\JenisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJenis extends ListRecords
{
    protected static string $resource = JenisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
