<?php

namespace App\Filament\Resources\RawatJalanResource\Pages;

use App\Filament\Resources\RawatJalanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRawatJalan extends EditRecord
{
    protected static string $resource = RawatJalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
