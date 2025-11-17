<?php

namespace App\Filament\Resources\DiagnosaPasienResource\Pages;

use App\Filament\Resources\DiagnosaPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListDiagnosaPasiens extends ListRecords
{
    protected static string $resource = DiagnosaPasienResource::class;

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

            'primary' => Tab::make('Diagnosa Utama')
                ->modifyQueryUsing(fn (Builder $query) => $query->primary())
                ->badge(fn () => static::getModel()::primary()->count())
                ->badgeColor('danger'),

            'today' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->today())
                ->badge(fn () => static::getModel()::today()->count()),

            'new_cases' => Tab::make('Kasus Baru')
                ->modifyQueryUsing(fn (Builder $query) => $query->newCases())
                ->badge(fn () => static::getModel()::newCases()->count())
                ->badgeColor('success'),
        ];
    }
}
