<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RawatInapResource\Pages;
use App\Filament\Resources\RawatInapResource\RelationManagers;
use App\Models\ranap_gabung;
use App\Models\RawatInap;
use App\Models\reg_periksa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\AppliesUserFilter; // 🔹 Tambahkan ini
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\DB;

class RawatInapResource extends Resource
{
    use AppliesUserFilter; // 🔹 Pastikan ini ada
    protected static ?string $model = reg_periksa::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationBadge(): ?string
    {
        // Ambil query utama dari model
        $query = static::getEloquentQuery();

        // Terapkan filter yang sama seperti yang digunakan di table()
        $query = (new static())->applyFiltersToQuery($query);

        // Kembalikan jumlah data yang tampil setelah difilter
        return $query->count();
    }


    protected static ?string $navigationGroup = 'ERM';

    // protected static ?int $navigationSort = 0;

    // Label jamak, ganti dengan singular jika perlu
    protected static ?string $pluralLabel = 'Rawat Inap'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = 'Rawat Inap';

    // title menu akan berubah
    protected static ?string $navigationLabel = 'Rawat Inap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::customQuery())
            ->searchable()
            ->columns([
                TextColumn::make('no_rkm_medis')
                    ->label('No.RM')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('bayi')
                    ->label('Bayi Gabung')
                    ->formatStateUsing(fn($state, $record) => $record->bayi ?? '-'),

                TextColumn::make('pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tgl_registrasi')
                    ->label('Waktu')
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => \Carbon\Carbon::parse($state)->format('d-m-Y') . ' ' . $record->jam_reg),

                TextColumn::make('nm_dokter')
                    ->label('Dokter')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nm_poli')
                    ->label('Poli')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('p_jawab')
                    ->label('PJ')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('almt_pj')
                    ->label('Alamat PJ')
                    ->sortable(),

                TextColumn::make('hubunganpj')
                    ->label('Hubungan PJ')
                    ->sortable(),

                TextColumn::make('stts')
                    ->label('Status')
                    ->sortable(),

                TextColumn::make('stts_daftar')
                    ->label('Status Daftar')
                    ->sortable(),

                TextColumn::make('status_lanjut')
                    ->label('Status Lanjut')
                    ->sortable(),

                TextColumn::make('penjab.nama_perusahaan')
                    ->label('Kode PJ')
                    ->sortable(),

                TextColumn::make('umurdaftar')
                    ->label('Umur')
                    ->sortable()
                    ->formatStateUsing(fn ($state,$record) => "{$record->umurdaftar} {$record->sttsumur}"),

                TextColumn::make('status_bayar')
                    ->label('Status Bayar')
                    ->sortable(),

                TextColumn::make('status_poli')
                    ->label('Status Poli')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function customQuery(): Builder
    {
        $query1 = reg_periksa::query()
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'penjab.nama_perusahaan',
                'reg_periksa.p_jawab',
                'reg_periksa.almt_pj',
                'reg_periksa.hubunganpj',
                'reg_periksa.stts',
                'reg_periksa.stts_daftar',
                'reg_periksa.status_lanjut',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.status_bayar',
                'reg_periksa.status_poli',
                DB::raw('NULL as bayi')
            )
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->where('reg_periksa.status_lanjut', 'Ranap')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('kamar_inap')
                    ->whereColumn('kamar_inap.no_rawat', 'reg_periksa.no_rawat')
                    ->where('stts_pulang', '-');
            });

        $query2 = ranap_gabung::query()
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'penjab.nama_perusahaan',
                'reg_periksa.p_jawab',
                'reg_periksa.almt_pj',
                'reg_periksa.hubunganpj',
                'reg_periksa.stts',
                'reg_periksa.stts_daftar',
                'reg_periksa.status_lanjut',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'reg_periksa.status_bayar',
                'reg_periksa.status_poli',
                DB::raw("CONCAT('Gabung: ', reg_periksa.no_rawat) as bayi")
            )
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'ranap_gabung.no_rawat2')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->leftJoin('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->leftJoin('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj');

        $combinedQuery = reg_periksa::query()
            ->fromSub($query1->unionAll($query2), 'combined')
            ->orderBy('combined.no_rawat');

        return $combinedQuery;
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
            'index' => Pages\ListRawatInaps::route('/'),
            'create' => Pages\CreateRawatInap::route('/create'),
            'edit' => Pages\EditRawatInap::route('/{record}/edit'),
        ];
    }
}
