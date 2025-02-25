<?php

namespace App\Filament\Resources\BerkasPegawaiResource\Pages;

use App\Filament\Resources\BerkasPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBerkasPegawais extends ListRecords
{
    protected static string $resource = BerkasPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
