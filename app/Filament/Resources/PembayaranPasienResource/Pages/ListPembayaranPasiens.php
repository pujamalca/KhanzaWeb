<?php

namespace App\Filament\Resources\PembayaranPasienResource\Pages;

use App\Filament\Resources\PembayaranPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPembayaranPasiens extends ListRecords
{
    protected static string $resource = PembayaranPasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),

            'today' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->today())
                ->badge(fn () => static::getModel()::today()->count()),

            'tunai' => Tab::make('Tunai')
                ->modifyQueryUsing(fn (Builder $query) => $query->byMethod('Tunai'))
                ->badge(fn () => static::getModel()::byMethod('Tunai')->recent(7)->count()),

            'transfer' => Tab::make('Transfer/Non-Tunai')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('metode_bayar', ['Transfer', 'Kartu Kredit', 'Kartu Debit']))
                ->badge(fn () => static::getModel()::whereIn('metode_bayar', ['Transfer', 'Kartu Kredit', 'Kartu Debit'])->recent(7)->count()),
        ];
    }
}
