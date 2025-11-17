<?php

namespace App\Filament\Resources\BillingPasienResource\Pages;

use App\Filament\Resources\BillingPasienResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBillingPasiens extends ListRecords
{
    protected static string $resource = BillingPasienResource::class;

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

            'unpaid' => Tab::make('Belum Lunas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status_bayar', '!=', 'Lunas'))
                ->badge(fn () => static::getModel()::where('status_bayar', '!=', 'Lunas')->count())
                ->badgeColor('danger'),

            'installment' => Tab::make('Cicilan')
                ->modifyQueryUsing(fn (Builder $query) => $query->installment())
                ->badge(fn () => static::getModel()::installment()->count())
                ->badgeColor('warning'),

            'paid' => Tab::make('Lunas')
                ->modifyQueryUsing(fn (Builder $query) => $query->paid())
                ->badge(fn () => static::getModel()::paid()->count())
                ->badgeColor('success'),
        ];
    }
}
