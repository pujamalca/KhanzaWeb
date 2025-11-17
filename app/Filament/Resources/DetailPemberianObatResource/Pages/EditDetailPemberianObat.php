<?php

namespace App\Filament\Resources/DetailPemberianObatResource\Pages;

use App\Filament\Resources\DetailPemberianObatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDetailPemberianObat extends EditRecord
{
    protected static string $resource = DetailPemberianObatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
