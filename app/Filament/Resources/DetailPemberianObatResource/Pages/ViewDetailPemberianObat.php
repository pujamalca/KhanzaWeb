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
            Actions\Action::make('printResep')
                ->label('Print Resep')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn () => route('print.resep', ['no_rawat' => $this->record->no_rawat]))
                ->openUrlInNewTab(),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
