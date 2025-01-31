<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Filament\Resources\PegawaiResource\RelationManagers;
use App\Models\bank;
use App\Models\bidang;
use App\Models\departemen;
use App\Models\emergency_index;
use App\Models\jnj_jabatan;
use App\Models\kelompok_jabatan;
use App\Models\Pegawai;
use App\Models\pendidikan;
use App\Models\resiko_kerja;
use App\Models\stts_kerja;
use App\Models\stts_wp;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'SDM';

    // Label jamak, ganti dengan singular jika perlu
    protected static ?string $pluralLabel = 'Pegawai'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = 'Pegawai';

    // title menu akan berubah
    protected static ?string $navigationLabel = 'Pegawai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nik')
                    ->label('NIK')
                    ->required()
                    ->maxLength(20),
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Lengkap (Tanpa Gelar) ya')
                    ->required()
                    ->maxLength(50),
                Forms\Components\Select::make('jk')
                    ->label('Jenis Kelamin')
                    ->options([
                        'pria' => 'Pria',
                        'wanita' => 'Wanita',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('jbtn')
                    ->label('Jabatan')
                    ->required()
                    ->maxLength(25),
                Forms\Components\Select::make('jnj_jabatan')
                    ->label('Jenjang Jabatan')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel jnj_jabatan
                        return jnj_jabatan::all()->pluck('nama', 'kode');
                    })
                    ->required() // Field ini wajib diisi
                    ->placeholder('Pilih Jabatan'),
                Forms\Components\select::make('kode_kelompok')
                    ->label('Kode Kelompok')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return kelompok_jabatan::all()->pluck('nama_kelompok', 'kode_kelompok');
                    })
                    ->required(),
                Forms\Components\select::make('kode_resiko')
                    ->label('Kode Resiko')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return resiko_kerja::all()->pluck('nama_resiko', 'kode_resiko');
                    })
                    ->required(),
                Forms\Components\select::make('kode_emergency')
                    ->label('Kode Emergency')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return emergency_index::all()->pluck('nama_emergency', 'kode_emergency');
                    })
                    ->required(),
                Forms\Components\select::make('dep_id')
                    ->label('Departemen')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return departemen::all()->pluck('nama', 'dep_id');
                    })
                    ->required(),
                Forms\Components\select::make('nama')
                    ->label('bidang')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return bidang::all()->pluck('nama', 'nama');
                    })
                    ->required(),
                Forms\Components\select::make('stts_wp')
                    ->label('Status Wajib Pajak')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return stts_wp::query()
                        ->select(DB::raw("CONCAT(stts, ' - ', ktg) as label, stts"))
                        ->pluck('label', 'stts');
                    })
                    ->required(),
                Forms\Components\select::make('stts_kerja')
                    ->label('Status Kerja')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return stts_kerja::query()
                        ->select(DB::raw("CONCAT(stts, ' - ', ktg) as label, stts"))
                        ->pluck('label', 'stts');
                    })
                    ->required(),
                TextInput::make('npwp')
                    ->label('NPWP')
                    ->numeric() // Hanya angka
                    ->minLength(16) // Minimal 16 digit
                    ->maxLength(16) // Maksimal 16 digit
                    ->rule('digits:16') // Pastikan tepat 16 digit
                    ->placeholder('Masukkan 16 digit NPWP'),
                // Di dalam form builder
                Select::make('tingkat')
                    ->label('Tingkat Pendidikan')
                    ->options(pendidikan::pluck('tingkat', 'tingkat')) // Ambil data tingkat
                    ->required()
                    ->live() // Aktifkan real-time update
                    ->afterStateUpdated(function ($set, $state) {
                        // Ambil nilai gapok1 dari database berdasarkan tingkat yang dipilih
                        $gapok1 = pendidikan::where('tingkat', $state)->value('gapok1') ?? 0;
                        $set('gapok1', $gapok1); // Set nilai gapok1 ke form
                    })
                    ->native(false),
                TextInput::make('gapok1')
                    ->label('Gaji Pokok')
                    ->live()
                    ->numeric()
                    ->required()
                    ->disabled(), // Non-editable
                Forms\Components\TextInput::make('tmp_lahir')
                    ->label('Tempat Lahir')
                    ->placeholder('Tempat Lahir')
                    ->required()
                    ->maxLength(20),
                Forms\Components\DatePicker::make('tgl_lahir')
                    ->required(),
                Forms\Components\TextInput::make('alamat')
                    ->maxLength(60),
                Forms\Components\TextInput::make('kota')
                    ->maxLength(20),
                Forms\Components\DatePicker::make('mulai_kerja')
                    ->required(),
                Forms\Components\Select::make('ms_kerja')
                    ->label('Masa Kerja')
                    ->options([
                        '<1' => '<1',
                        'PT' => 'PT',
                        'FT>1' => 'FT>1',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('indexins')
                    ->required()
                    ->maxLength(4),
                Forms\Components\select::make('bank')
                    ->label('Nama Bank')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return bank::all()->pluck('namabank', 'namabank');
                    })
                    ->required(),
                Forms\Components\TextInput::make('rekening')
                    ->required()
                    ->maxLength(25),
                Forms\Components\Select::make('stts_aktif')
                    ->label('Status Aktif')
                    ->options([
                        'AKTIF' => 'AKTIF',
                        'CUTI' => 'CUTI',
                        'KELUAR' => 'KELUAR',
                        'TENAGA LUAR' => 'TENAGA LUAR',
                        'NON AKTIF' => 'NON AKTIF',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('wajibmasuk')
                    ->label('Wajib AKtif')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('pengurang')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('indek')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('mulai_kontrak'),
                Forms\Components\TextInput::make('cuti_diambil')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('dankes')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('photo')
                    ->maxLength(500)
                    ->default(null),
                Forms\Components\TextInput::make('no_ktp')
                    ->required()
                    ->maxLength(20),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jk'),
                Tables\Columns\TextColumn::make('jbtn')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jnj_jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_kelompok')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_resiko')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_emergency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('departemen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bidang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stts_wp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stts_kerja')
                    ->searchable(),
                Tables\Columns\TextColumn::make('npwp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pendidikan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gapok')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tmp_lahir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mulai_kerja')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ms_kerja'),
                Tables\Columns\TextColumn::make('indexins')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bpd')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rekening')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stts_aktif'),
                Tables\Columns\TextColumn::make('wajibmasuk')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pengurang')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('indek')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mulai_kontrak')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cuti_diambil')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dankes')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('photo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_ktp')
                    ->searchable(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
