<?php

namespace App\Filament\Resources;

use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\UgdResource\Pages;
use App\Filament\Resources\UgdResource\RelationManagers;
use App\Models\reg_periksa;
use App\Models\Ugd;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use App\Traits\AppliesUserFilter; // 🔹 Tambahkan ini
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Log;

class UgdResource extends Resource
{
    use AppliesUserFilter; // 🔹 Pastikan ini ada
    protected static ?string $model = reg_periksa::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

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
    protected static ?string $pluralLabel = 'UGD'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = 'UGD';

    // title menu akan berubah
    protected static ?string $navigationLabel = 'UGD';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('no_rkm_medis')
                ->label('Nomor Rekam Medis')
                ->options(\App\Models\Pasien::pluck('no_rkm_medis', 'no_rkm_medis')) // Ambil dari tabel pasien
                ->searchable()
                ->required(),

            Forms\Components\Select::make('kd_poli')
                ->label('Poliklinik')
                ->options(\App\Models\Poliklinik::pluck('nm_poli', 'kd_poli')) // Ambil dari tabel poliklinik
                ->searchable()
                ->required(),

            Forms\Components\Select::make('kd_dokter')
                ->label('Dokter')
                ->options(\App\Models\Dokter::pluck('nm_dokter', 'kd_dokter')) // Ambil dari tabel dokter
                ->searchable()
                ->required(),

            Forms\Components\Select::make('kd_pj')
                ->label('Penanggung Jawab')
                ->options(\App\Models\Penjab::pluck('png_jawab', 'kd_pj')) // Ambil dari tabel penjab
                ->searchable()
                ->required(),

            Forms\Components\DatePicker::make('tgl_registrasi')
                ->label('Tanggal Registrasi')
                ->required(),

            Forms\Components\TimePicker::make('jam_reg')
                ->label('Jam Registrasi')
                ->required(),

            Forms\Components\TextInput::make('p_jawab')
                ->label('Penanggung Jawab')
                ->required(),

            Forms\Components\Textarea::make('almt_pj')
                ->label('Alamat Penanggung Jawab')
                ->required(),

            Forms\Components\TextInput::make('hubunganpj')
                ->label('Hubungan Penanggung Jawab')
                ->required(),

            Forms\Components\Select::make('stts')
                ->label('Status')
                ->options([
                    'Belum' => 'Belum',
                    'Sudah' => 'Sudah',
                ])
                ->required(),

            Forms\Components\Select::make('stts_daftar')
                ->label('Status Daftar')
                ->options([
                    '-' => '-',
                    'L' => 'L',
                ])
                ->required(),

            Forms\Components\Select::make('status_lanjut')
                ->label('Status Lanjut')
                ->options([
                    'Ralan' => 'Ralan',
                ])
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(static::applyEloquentQuery(reg_periksa::query(), 'reg_periksa'))
        // ->whereDate('tgl_registrasi', now()->toDateString()) // Pastikan filter harian ada di sini
        ->searchable()
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
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListUgds::route('/'),
            // 'create' => Pages\CreateUgd::route('/create'),
            'edit' => Pages\EditUgd::route('/{record}/edit'),
        ];
    }

        public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('kd_poli', 'IGDK');
    }

}
