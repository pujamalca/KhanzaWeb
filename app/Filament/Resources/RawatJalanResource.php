<?php

namespace App\Filament\Resources;

use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\RawatJalanResource\Pages;
use App\Filament\Resources\RawatJalanResource\RelationManagers;
use App\Models\RawatJalan;
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
use Illuminate\Support\Carbon;

class RawatJalanResource extends Resource
{
    use AppliesUserFilter; // 🔹 Pastikan ini ada
    protected static ?string $model = reg_periksa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

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
    protected static ?string $pluralLabel = 'Rawat Jalan'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = 'Rawat Jalan';

    // title menu akan berubah
    protected static ?string $navigationLabel = 'Rawat Jalan';

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
        ->query(static::applyEloquentQuery(reg_periksa::query(), 'reg_periksa'))
            ->columns([
                //
            TextColumn::make('no_rkm_medis')
                ->label('No.RM')
                ->sortable()
                ->searchable(),

            TextColumn::make('no_rawat')
                ->label('No. Rawat')
                ->sortable()
                ->searchable(),

            TextColumn::make('pasien.nm_pasien')
                ->label('Nama Pasien')
                ->sortable()
                ->searchable(),

            TextColumn::make('tgl_registrasi')
                ->label('Waktu')
                ->sortable()
                ->formatStateUsing(fn ($state, $record) => \Carbon\Carbon::parse($state)->format('d-m-Y') . ' ' . $record->jam_reg),

            TextColumn::make('dokter.nm_dokter')
                ->label('Dokter')
                ->sortable()
                ->searchable(),

            TextColumn::make('poliklinik.nm_poli')
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
                DateRangeFilter::make('tgl_registrasi', 'Tanggal Registrasi'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRawatJalans::route('/'),
            'create' => Pages\CreateRawatJalan::route('/create'),
            'edit' => Pages\EditRawatJalan::route('/{record}/edit'),
        ];
    }
}
