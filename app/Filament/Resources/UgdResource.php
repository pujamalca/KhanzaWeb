<?php

namespace App\Filament\Resources;

use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\UgdResource\Pages;
use App\Filament\Resources\UgdResource\RelationManagers;
use App\Models\reg_periksa;
use App\Models\Ugd;
use App\Models\User;
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
use Filament\Actions\Modal\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\DB;
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
                Forms\Components\Checkbox::make('auto_generate_no_reg')
                ->label('Otomatis No. Reg')
                ->default(true)
                ->reactive(), // Agar langsung update saat diubah

            Forms\Components\TextInput::make('no_reg')
                ->label('No. Reg.')
                ->default(fn ($get) => $get('auto_generate_no_reg') ? str_pad(
                    (int) \App\Models\reg_periksa::whereDate('tgl_registrasi', now()->toDateString()) // Hanya data hari ini
                        ->where('kd_poli', $get('kd_poli') ?? 'IGDK') // Gunakan kd_poli dari form
                        ->max('no_reg') + 1, // Ambil no_reg terakhir dari poli yang sama
                    3, '0', STR_PAD_LEFT // Format tiga digit: 001, 002, 003
                ) : null) // Jika manual, biarkan kosong
                ->live() // Supaya otomatis diperbarui ketika kd_poli berubah
                ->disabled(fn ($get) => $get('auto_generate_no_reg') === true) // Bisa diedit jika checkbox mati
                ->required(),

            Forms\Components\TextInput::make('no_rawat')
                ->label('No. Rawat')
                ->default(fn () => now()->format('Y/m/d') . '/' . str_pad(
                    (int) \App\Models\reg_periksa::whereDate('tgl_registrasi', now()->toDateString())->count() + 1,
                    6, '0', STR_PAD_LEFT
                )) // Format YYYY/MM/DD/000XXX, berdasarkan jumlah rawat hari ini
                ->required(),

                Forms\Components\Select::make('no_rkm_medis')
                ->label('Nomor RM - Nama')
                ->options(
                    \App\Models\Pasien::select('no_rkm_medis', 'nm_pasien')
                        ->limit(100) // Batasi hanya 100 data pertama
                        ->get()
                        ->mapWithKeys(fn ($pasien) => [
                            $pasien->no_rkm_medis => "{$pasien->no_rkm_medis} - {$pasien->nm_pasien}"
                        ])
                )
                ->searchable()
                ->getSearchResultsUsing(fn (string $search) =>
                    \App\Models\Pasien::where('no_rkm_medis', 'like', "%{$search}%")
                        ->orWhere('nm_pasien', 'like', "%{$search}%")
                        ->limit(100)
                        ->get()
                        ->mapWithKeys(fn ($pasien) => [
                            $pasien->no_rkm_medis => "{$pasien->no_rkm_medis} - {$pasien->nm_pasien}"
                        ])
                )
                ->reactive() // 🔹 Reactively update p_jawab
                ->afterStateUpdated(fn ($set, $state) => $set('p_jawab',
                    \App\Models\Pasien::where('no_rkm_medis', $state)->value('namakeluarga') ?? ''
                ))
                ->required(),

            Forms\Components\TextInput::make('p_jawab')
                ->label('Penanggung Jawab')
                ->disabled() // Supaya tidak bisa diedit manual
                ->required(),

            Forms\Components\Select::make('kd_poli')
                ->label('Poliklinik')
                ->options(\App\Models\Poliklinik::where('kd_poli', 'like', '%IGD%')->pluck('nm_poli', 'kd_poli'))
                ->default('IGDK') // Default ke IGDK
                ->disabled(), // Tidak bisa diganti

            Forms\Components\Select::make('kd_dokter')
                ->label('Dokter')
                ->options(\App\Models\Dokter::pluck('nm_dokter', 'kd_dokter'))
                ->searchable()
                ->required(),

            Forms\Components\Select::make('kd_pj')
                ->label('Penanggung Jawab')
                ->options(\App\Models\Penjab::pluck('png_jawab', 'kd_pj'))
                ->searchable()
                ->required(),

            Forms\Components\DatePicker::make('tgl_registrasi')
                ->label('Tanggal Registrasi')
                ->default(now()->toDateString())
                ->required(),

            Forms\Components\TimePicker::make('jam_reg')
                ->label('Jam Registrasi')
                ->default(now()->format('H:i:s'))
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                    ->url(fn (reg_periksa $record) => route(
                        'filament.superadmin.resources.ugds.edit',
                        ['record' => str_replace('/', '-', $record->no_rawat)] // Ganti "/" jadi "-"
                    )),

                    Tables\Actions\DeleteAction::make(),
                ])
                ->button()
                ->label('Menu'),
            ],position: ActionsPosition::BeforeColumns)
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
            'create' => Pages\CreateUgd::route('/create'),
            'edit' => Pages\EditUgd::route('/{record}'),
        ];
    }

        public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('kd_poli', 'IGDK');
    }

}
