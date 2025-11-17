<?php

namespace App\Filament\Resources\KodesatuanResource\Pages;

use App\Filament\Resources\KodesatuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKodesatuans extends ListRecords
{
    protected static string $resource = KodesatuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
