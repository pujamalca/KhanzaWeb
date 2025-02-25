<?php

namespace App\Filament\Resources\BerkasPegawaiResource\Pages;

use App\Filament\Resources\BerkasPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBerkasPegawai extends EditRecord
{
    protected static string $resource = BerkasPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
