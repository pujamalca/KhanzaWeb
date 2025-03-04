<?php

namespace App\Filament\Resources\UgdResource\Pages;

use App\Filament\Resources\UgdResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUgd extends EditRecord
{
    protected static string $resource = UgdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
