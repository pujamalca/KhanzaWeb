<?php

namespace App\Filament\Resources\PcareKunjunganResource\Pages;

use App\Filament\Resources\PcareKunjunganResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListPcareKunjungans extends ListRecords
{
    protected static string $resource = PcareKunjunganResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'today' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn ($query) => $query->where('tgl_kunjungan', date('Y-m-d')))
                ->badge(fn () => \App\Models\PcareKunjungan::where('tgl_kunjungan', date('Y-m-d'))->count()),
            'belum_kirim' => Tab::make('Belum Kirim')
                ->modifyQueryUsing(fn ($query) => $query->where('status_kirim', 'Belum'))
                ->badge(fn () => \App\Models\PcareKunjungan::where('status_kirim', 'Belum')->count())
                ->badgeColor('warning'),
            'sudah_kirim' => Tab::make('Sudah Kirim')
                ->modifyQueryUsing(fn ($query) => $query->where('status_kirim', 'Sudah'))
                ->badge(fn () => \App\Models\PcareKunjungan::where('status_kirim', 'Sudah')->count())
                ->badgeColor('success'),
            'with_rujukan' => Tab::make('Dengan Rujukan')
                ->modifyQueryUsing(fn ($query) => $query->whereNotNull('tgl_rujuk'))
                ->badge(fn () => \App\Models\PcareKunjungan::whereNotNull('tgl_rujuk')->count())
                ->badgeColor('danger'),
        ];
    }
}
