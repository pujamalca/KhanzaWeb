<?php

namespace App\Filament\Widgets;

use App\Models\PemeriksaanRalan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;

class RecentExaminationsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Pemeriksaan Terbaru (7 Hari)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PemeriksaanRalan::query()
                    ->with(['regPeriksa.pasien', 'regPeriksa.poliklinik'])
                    ->recent(7)
                    ->orderBy('tgl_perawatan', 'desc')
                    ->orderBy('jam_rawat', 'desc')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('formatted_date_time')
                    ->label('Waktu')
                    ->sortable(['tgl_perawatan', 'jam_rawat']),

                TextColumn::make('regPeriksa.pasien.nm_pasien')
                    ->label('Pasien')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('regPeriksa.pasien.no_rkm_medis')
                    ->label('No. RM')
                    ->searchable(),

                TextColumn::make('keluhan')
                    ->label('Keluhan')
                    ->limit(40)
                    ->wrap(),

                BadgeColumn::make('is_soap_complete')
                    ->label('SOAP')
                    ->formatStateUsing(fn ($state) => $state ? 'Lengkap' : 'Belum')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),

                TextColumn::make('bmi')
                    ->label('BMI')
                    ->formatStateUsing(fn ($state) => $state ?? '-')
                    ->badge()
                    ->color(fn ($record) => match($record->bmi_category) {
                        'Normal' => 'success',
                        'Underweight' => 'warning',
                        'Overweight' => 'warning',
                        'Obese' => 'danger',
                        default => 'gray'
                    }),

                TextColumn::make('regPeriksa.poliklinik.nm_poli')
                    ->label('Poli')
                    ->badge()
                    ->color('info'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn (PemeriksaanRalan $record) =>
                        route('filament.superadmin.resources.pemeriksaan-ralans.view', ['record' => $record->no_rawat])
                    ),
            ]);
    }
}
