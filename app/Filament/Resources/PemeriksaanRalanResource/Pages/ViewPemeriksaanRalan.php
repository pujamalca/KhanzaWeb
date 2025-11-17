<?php

namespace App\Filament\Resources\PemeriksaanRalanResource\Pages;

use App\Filament\Resources\PemeriksaanRalanResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class ViewPemeriksaanRalan extends ViewRecord
{
    protected static string $resource = PemeriksaanRalanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pasien')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('no_rawat')
                                    ->label('No. Rawat'),

                                TextEntry::make('regPeriksa.pasien.nm_pasien')
                                    ->label('Nama Pasien'),

                                TextEntry::make('regPeriksa.pasien.no_rkm_medis')
                                    ->label('No. RM'),

                                TextEntry::make('tgl_perawatan')
                                    ->label('Tanggal')
                                    ->date('d M Y'),

                                TextEntry::make('jam_rawat')
                                    ->label('Jam'),

                                TextEntry::make('regPeriksa.poliklinik.nm_poli')
                                    ->label('Poliklinik'),
                            ]),
                    ]),

                Section::make('Tanda Vital')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('suhu_tubuh')
                                    ->label('Suhu Tubuh')
                                    ->suffix(' °C'),

                                TextEntry::make('tensi')
                                    ->label('Tekanan Darah')
                                    ->suffix(' mmHg'),

                                TextEntry::make('nadi')
                                    ->label('Nadi')
                                    ->suffix(' bpm'),

                                TextEntry::make('respirasi')
                                    ->label('Respirasi')
                                    ->suffix(' x/menit'),
                            ]),

                        Grid::make(4)
                            ->schema([
                                TextEntry::make('tinggi')
                                    ->label('Tinggi Badan')
                                    ->suffix(' cm'),

                                TextEntry::make('berat')
                                    ->label('Berat Badan')
                                    ->suffix(' kg'),

                                TextEntry::make('bmi')
                                    ->label('BMI')
                                    ->formatStateUsing(fn ($state, $record) =>
                                        $state ? "{$state} ({$record->bmi_category})" : '-'
                                    )
                                    ->badge()
                                    ->color(fn ($record) => match($record->bmi_category) {
                                        'Normal' => 'success',
                                        'Underweight' => 'warning',
                                        'Overweight' => 'warning',
                                        'Obese' => 'danger',
                                        default => 'gray'
                                    }),

                                TextEntry::make('lingkar_perut')
                                    ->label('Lingkar Perut')
                                    ->suffix(' cm'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('spo2')
                                    ->label('SpO2')
                                    ->suffix(' %'),

                                TextEntry::make('gcs')
                                    ->label('GCS'),

                                TextEntry::make('kesadaran')
                                    ->label('Kesadaran'),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('SOAP Notes')
                    ->schema([
                        TextEntry::make('keluhan')
                            ->label('Subjective: Keluhan')
                            ->columnSpanFull(),

                        TextEntry::make('pemeriksaan')
                            ->label('Objective: Pemeriksaan')
                            ->columnSpanFull(),

                        TextEntry::make('penilaian')
                            ->label('Assessment: Penilaian')
                            ->columnSpanFull(),

                        TextEntry::make('rtl')
                            ->label('Plan: Rencana Tindak Lanjut')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('instruksi')
                                    ->label('Instruksi Medis'),

                                TextEntry::make('evaluasi')
                                    ->label('Evaluasi'),
                            ]),

                        TextEntry::make('alergi')
                            ->label('Alergi')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Metadata')
                    ->schema([
                        TextEntry::make('petugas.nama')
                            ->label('Petugas'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
