<?php

namespace App\Filament\Resources\PcarePendaftaranResource\Pages;

use App\Filament\Resources\PcarePendaftaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListPcarePendaftarans extends ListRecords
{
    protected static string $resource = PcarePendaftaranResource::class;

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
            'belum_kirim' => Tab::make('Belum Kirim')
                ->modifyQueryUsing(fn ($query) => $query->where('status_kirim', 'Belum'))
                ->badge(fn () => \App\Models\PcarePendaftaran::where('status_kirim', 'Belum')->count())
                ->badgeColor('warning'),
            'sudah_kirim' => Tab::make('Sudah Kirim')
                ->modifyQueryUsing(fn ($query) => $query->where('status_kirim', 'Sudah'))
                ->badge(fn () => \App\Models\PcarePendaftaran::where('status_kirim', 'Sudah')->count())
                ->badgeColor('success'),
            'gagal' => Tab::make('Gagal')
                ->modifyQueryUsing(fn ($query) => $query->where('status_kirim', 'Gagal'))
                ->badge(fn () => \App\Models\PcarePendaftaran::where('status_kirim', 'Gagal')->count())
                ->badgeColor('danger'),
            'today' => Tab::make('Hari Ini')
                ->modifyQueryUsing(fn ($query) => $query->where('tgl_daftar', date('Y-m-d')))
                ->badge(fn () => \App\Models\PcarePendaftaran::where('tgl_daftar', date('Y-m-d'))->count()),
        ];
    }
}
