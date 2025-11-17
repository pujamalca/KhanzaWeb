<?php

namespace App\Filament\Resources\DatabarangResource\Pages;

use App\Filament\Resources\DatabarangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDatabarangs extends ListRecords
{
    protected static string $resource = DatabarangResource::class;

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
            'active' => Tab::make('Aktif')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', '1')),
            'low_stock' => Tab::make('Stok Rendah')
                ->modifyQueryUsing(fn (Builder $query) => $query->lowStock())
                ->badge(fn () => static::getModel()::lowStock()->count())
                ->badgeColor('danger'),
            'expiring' => Tab::make('Akan Kadaluwarsa')
                ->modifyQueryUsing(fn (Builder $query) => $query->expiringSoon(30))
                ->badge(fn () => static::getModel()::expiringSoon(30)->count())
                ->badgeColor('warning'),
        ];
    }
}
