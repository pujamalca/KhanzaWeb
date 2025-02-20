<?php

namespace App\Filament\Resources\MasterBerkasPegawaiResource\Pages;

use App\Filament\Resources\MasterBerkasPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMasterBerkasPegawais extends ListRecords
{
    protected static string $resource = MasterBerkasPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
