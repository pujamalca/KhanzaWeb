<?php

namespace App\Filament\Resources\DetailPemberianObatResource\Pages;

use App\Filament\Resources\DetailPemberianObatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDetailPemberianObats extends ListRecords
{
    protected static string $resource = DetailPemberianObatResource::class;

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

            'recent' => Tab::make('7 Hari Terakhir')
                ->modifyQueryUsing(fn (Builder $query) => $query->recent(7))
                ->badge(fn () => static::getModel()::recent(7)->count()),
        ];
    }
}
