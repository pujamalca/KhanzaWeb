<?php

namespace App\Filament\Resources\RawatInapResource\Pages;

use App\Filament\Resources\RawatInapResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRawatInaps extends ListRecords
{
    protected static string $resource = RawatInapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
