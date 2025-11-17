<?php

namespace App\Filament\Resources\DetailPemberianObatResource\Pages;

use App\Filament\Resources\DetailPemberianObatResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDetailPemberianObat extends ViewRecord
{
    protected static string $resource = DetailPemberianObatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
