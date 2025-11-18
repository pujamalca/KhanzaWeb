<?php

namespace App\Filament\Resources\PcarePendaftaranResource\Pages;

use App\Filament\Resources\PcarePendaftaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPcarePendaftaran extends ViewRecord
{
    protected static string $resource = PcarePendaftaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
