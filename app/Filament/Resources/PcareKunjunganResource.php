<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PcareKunjunganResource\Pages;
use App\Models\PcareKunjungan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class PcareKunjunganResource extends Resource
{
    protected static ?string $model = PcareKunjungan::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Kunjungan PCare';

    protected static ?string $navigationGroup = 'BPJS PCare';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_kunjungan')
                    ->label('No. Kunjungan')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('tgl_kunjungan')
                    ->label('Tgl Kunjungan')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nm_poli')
                    ->label('Poli')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nm_diagnosa1')
                    ->label('Diagnosa Primer')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('nm_tkp')
                    ->label('Tindak Lanjut')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        '10' => 'danger',
                        '20', '40' => 'warning',
                        '50' => 'success',
                        default => 'gray'
                    }),

                Tables\Columns\BadgeColumn::make('status_kirim')
                    ->label('Status')
                    ->colors([
                        'warning' => 'Belum',
                        'success' => 'Sudah',
                        'danger' => 'Gagal',
                    ]),

                Tables\Columns\IconColumn::make('has_rujukan')
                    ->label('Rujukan')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(fn ($record) => !empty($record->tgl_rujuk)),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_kirim')
                    ->options([
                        'Belum' => 'Belum Kirim',
                        'Sudah' => 'Sudah Kirim',
                        'Gagal' => 'Gagal Kirim',
                    ]),

                Tables\Filters\Filter::make('with_rujukan')
                    ->label('Dengan Rujukan')
                    ->query(fn ($query) => $query->whereNotNull('tgl_rujuk')),

                Tables\Filters\Filter::make('tgl_kunjungan')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'], fn ($q, $date) => $q->where('tgl_kunjungan', '>=', $date))
                            ->when($data['sampai'], fn ($q, $date) => $q->where('tgl_kunjungan', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('kirim_pcare')
                    ->label('Kirim ke PCare')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (PcareKunjungan $record) => $record->status_kirim !== 'Sudah')
                    ->action(function (PcareKunjungan $record) {
                        \Filament\Notifications\Notification::make()
                            ->title('Fitur dalam pengembangan')
                            ->body('Pengiriman kunjungan ke PCare akan diimplementasikan')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tgl_kunjungan', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Kunjungan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('no_kunjungan')->label('No. Kunjungan'),
                                TextEntry::make('no_rawat')->label('No. Rawat'),
                                TextEntry::make('tgl_kunjungan')
                                    ->label('Tanggal Kunjungan')
                                    ->date('d F Y'),
                                TextEntry::make('no_kartu')->label('No. Kartu BPJS'),
                                TextEntry::make('nm_pasien')->label('Nama Pasien'),
                                TextEntry::make('nm_poli')->label('Poli'),
                            ]),
                    ]),

                Section::make('Tanda Vital')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('tekanan_darah')
                                    ->label('Tekanan Darah'),
                                TextEntry::make('beratbadan')
                                    ->label('Berat Badan')
                                    ->suffix(' kg'),
                                TextEntry::make('tinggibadan')
                                    ->label('Tinggi Badan')
                                    ->suffix(' cm'),
                                TextEntry::make('lingkarperut')
                                    ->label('Lingkar Perut')
                                    ->suffix(' cm'),
                                TextEntry::make('resprate')
                                    ->label('Respiratory Rate'),
                                TextEntry::make('heartrate')
                                    ->label('Heart Rate'),
                                TextEntry::make('kesadaran')
                                    ->label('Kesadaran'),
                            ]),
                    ]),

                Section::make('Keluhan & Pemeriksaan')
                    ->schema([
                        TextEntry::make('keluhan')
                            ->label('Keluhan (Subjective)')
                            ->columnSpanFull(),
                        TextEntry::make('pemeriksaan')
                            ->label('Pemeriksaan (Objective)')
                            ->columnSpanFull(),
                    ]),

                Section::make('Diagnosa')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('kd_diagnosa1')
                                    ->label('Kode Diagnosa Primer')
                                    ->badge()
                                    ->color('danger'),
                                TextEntry::make('nm_diagnosa1')
                                    ->label('Diagnosa Primer')
                                    ->columnSpanFull(),
                                TextEntry::make('kd_diagnosa2')
                                    ->label('Kode Diagnosa Sekunder')
                                    ->badge()
                                    ->color('warning'),
                                TextEntry::make('nm_diagnosa2')
                                    ->label('Diagnosa Sekunder')
                                    ->columnSpanFull(),
                                TextEntry::make('kd_diagnosa3')
                                    ->label('Kode Diagnosa Tersier')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('nm_diagnosa3')
                                    ->label('Diagnosa Tersier')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                Section::make('Terapi & Tindak Lanjut')
                    ->schema([
                        TextEntry::make('therapy')
                            ->label('Terapi')
                            ->columnSpanFull(),
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('tkp_label')
                                    ->label('Tindak Lanjut')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('tgl_pulang')
                                    ->label('Tanggal Pulang/Rujuk')
                                    ->date('d F Y'),
                                TextEntry::make('nm_status_pulang')
                                    ->label('Status Pulang'),
                            ]),
                    ]),

                Section::make('Rujukan')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('tgl_rujuk')
                                    ->label('Tanggal Rujukan')
                                    ->date('d F Y'),
                                TextEntry::make('nm_ppk')
                                    ->label('PPK Tujuan'),
                                TextEntry::make('nm_spesialis')
                                    ->label('Spesialis'),
                                TextEntry::make('nm_subspesialis')
                                    ->label('Subspesialis'),
                                TextEntry::make('nm_sarana')
                                    ->label('Sarana'),
                                TextEntry::make('nm_khusus')
                                    ->label('Rujukan Khusus'),
                                TextEntry::make('catatan')
                                    ->label('Catatan')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->visible(fn ($record) => !empty($record->tgl_rujuk)),

                Section::make('Status PCare')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('status_kirim')
                                    ->label('Status Kirim')
                                    ->badge()
                                    ->color(fn ($state) => match($state) {
                                        'Sudah' => 'success',
                                        'Gagal' => 'danger',
                                        default => 'warning'
                                    }),
                                TextEntry::make('keterangan')
                                    ->label('Keterangan')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPcareKunjungans::route('/'),
            'view' => Pages\ViewPcareKunjungan::route('/{record}'),
        ];
    }
}
