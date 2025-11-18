<?php

namespace App\Filament\Resources\PcareActivityLogResource\Pages;

use App\Filament\Resources\PcareActivityLogResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListPcareActivityLogs extends ListRecords
{
    protected static string $resource = PcareActivityLogResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'today' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn ($query) => $query->whereDate('created_at', today()))
                ->badge(fn () => \App\Models\PcareActivityLog::whereDate('created_at', today())->count()),
            'success' => Tab::make('Success')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'success'))
                ->badge(fn () => \App\Models\PcareActivityLog::where('status', 'success')->count())
                ->badgeColor('success'),
            'failed' => Tab::make('Failed')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'failed'))
                ->badge(fn () => \App\Models\PcareActivityLog::where('status', 'failed')->count())
                ->badgeColor('danger'),
        ];
    }
}
