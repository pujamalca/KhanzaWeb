<?php

namespace App\Filament\Widgets;

use App\Models\reg_periksa;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Carbon\Carbon;

class PendingExaminationsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Pasien Menunggu Pemeriksaan (Hari Ini)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                reg_periksa::query()
                    ->with(['pasien', 'dokter', 'poliklinik'])
                    ->whereDate('tgl_registrasi', Carbon::today())
                    ->doesntHave('pemeriksaanRalan')
                    ->orderBy('jam_reg', 'asc')
            )
            ->columns([
                TextColumn::make('no_reg')
                    ->label('No. Reg')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('tgl_registrasi')
                    ->label('Waktu Daftar')
                    ->formatStateUsing(fn ($state, $record) =>
                        Carbon::parse($state)->format('d-m-Y') . ' ' . $record->jam_reg
                    )
                    ->sortable(),

                TextColumn::make('pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('pasien.no_rkm_medis')
                    ->label('No. RM')
                    ->searchable(),

                TextColumn::make('dokter.nm_dokter')
                    ->label('Dokter')
                    ->searchable(),

                TextColumn::make('poliklinik.nm_poli')
                    ->label('Poli')
                    ->badge()
                    ->color('info'),

                TextColumn::make('stts_daftar')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'success',
                        'Lama' => 'primary',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('createExamination')
                    ->label('Periksa')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->url(fn (reg_periksa $record) =>
                        route('filament.superadmin.resources.pemeriksaan-ralans.create', ['no_rawat' => $record->no_rawat])
                    ),
            ])
            ->emptyStateHeading('Tidak ada pasien menunggu')
            ->emptyStateDescription('Semua pasien hari ini sudah diperiksa.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
