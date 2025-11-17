<?php

namespace App\Filament\Resources\PemeriksaanRalanResource\Pages;

use App\Filament\Resources\PemeriksaanRalanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPemeriksaanRalans extends ListRecords
{
    protected static string $resource = PemeriksaanRalanResource::class;

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

            'incomplete' => Tab::make('SOAP Belum Lengkap')
                ->modifyQueryUsing(fn (Builder $query) => $query->incompleteSoap())
                ->badge(fn () => static::getModel()::incompleteSoap()->count())
                ->badgeColor('warning'),
        ];
    }
}
