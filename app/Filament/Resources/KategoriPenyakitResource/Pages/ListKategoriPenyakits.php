<?php

namespace App\Filament\Resources\KategoriPenyakitResource\Pages;

use App\Filament\Resources\KategoriPenyakitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKategoriPenyakits extends ListRecords
{
    protected static string $resource = KategoriPenyakitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
