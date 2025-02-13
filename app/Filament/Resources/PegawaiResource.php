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
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                        return jnj_jabatan::all()->pluck('nama', 'kode',);
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('kode')
                            ->label('Kode jabatan')
                            ->required()
                            ->unique('jnj_jabatan', 'kode'),

                        Forms\Components\TextInput::make('nama')
                            ->label('Nama jabatan')
                            ->required(),

                        Forms\Components\KeyValue::make('list_jabatan')
                            ->label('Data jabatan')
                            ->default(
                                \App\Models\jnj_jabatan::orderBy('kode', 'asc') // Pastikan urutan ASC
                                    ->pluck('nama', 'kode')
                                    ->toArray()
                            )
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->required() // Field ini wajib diisi
                    ->placeholder('Pilih Jabatan'),
                Forms\Components\select::make('kode_kelompok')
                    ->label('Kode Kelompok')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return kelompok_jabatan::all()->pluck('nama_kelompok', 'kode_kelompok');
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('kode_kelompok')
                            ->label('Kode Kelompok')
                            ->required()
                            ->unique('jnj_jabatan', 'kode_kelompok'),

                        Forms\Components\TextInput::make('nama_kelompok')
                            ->label('Nama Kelompok')
                            ->required(),

                        Forms\Components\KeyValue::make('list_kelompok')
                            ->label('Data Kelompok')
                            ->default(
                                \App\Models\kelompok_jabatan::orderBy('kode_kelompok', 'asc') // Pastikan urutan ASC
                                    ->pluck('nama_kelompok', 'kode_kelompok')
                                    ->toArray()
                            )
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->required(),
                Forms\Components\select::make('kode_resiko')
                    ->label('Kode Resiko')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return resiko_kerja::all()->pluck('nama_resiko', 'kode_resiko');
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('kode_resiko')
                            ->label('Kode Resiko')
                            ->required()
                            ->unique('resiko_kerja', 'kode_resiko'),

                        Forms\Components\TextInput::make('nama_resiko')
                            ->label('Nama Resiko')
                            ->required(),

                        Forms\Components\KeyValue::make('list_resiko')
                            ->label('Data Resiko')
                            ->default(
                                \App\Models\resiko_kerja::orderBy('kode_resiko', 'asc') // Pastikan urutan ASC
                                    ->pluck('nama_resiko', 'kode_resiko')
                                    ->toArray()
                            )
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->required(),
                Forms\Components\select::make('kode_emergency')
                    ->label('Kode Emergency')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return emergency_index::all()->pluck('nama_emergency', 'kode_emergency');
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('kode_emergency')
                            ->label('Kode Emergency')
                            ->required()
                            ->unique('emergency_index', 'kode_emergency'),

                        Forms\Components\TextInput::make('nama_emergency')
                            ->label('Nama Emergency')
                            ->required(),

                        Forms\Components\KeyValue::make('list_emergency')
                            ->label('Data Emergency')
                            ->default(
                                \App\Models\emergency_index::orderBy('kode_emergency', 'asc') // Pastikan urutan ASC
                                    ->pluck('nama_emergency', 'kode_emergency')
                                    ->toArray()
                            )
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->required(),
                    Select::make('departemen')
                    ->label('Pilih Departemen')
                    ->options(Departemen::pluck('nama', 'dep_id')) // Pastikan nama kolom benar
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('dep_id')
                            ->label('Departemen ID')
                            ->required()
                            ->unique('departemen', 'dep_id'),

                        Forms\Components\TextInput::make('nama')
                            ->label('Nama ')
                            ->required(),

                        Forms\Components\KeyValue::make('list_emergency')
                            ->label('Data Departemen')
                            ->default(
                                \App\Models\departemen::orderBy('dep_id', 'asc') // Pastikan urutan ASC
                                    ->pluck('nama', 'dep_id')
                                    ->toArray()
                            )
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('indexins', $state)) // Set indexins sesuai departemen
                    ->required(),

                Hidden::make('indexins') // Menyembunyikan indexins, tapi tetap tersimpan
                    ->default(fn ($get) => $get('departemen')),
                Forms\Components\select::make('bidang')
                    ->label('Bidang')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return bidang::all()->pluck('nama', 'nama');
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')
                            ->label('Bidang')
                            ->required()
                            ->unique('bidang', 'nama'),

                        Forms\Components\KeyValue::make('list_bidang')
                            ->label('Data Bidang')
                            ->default(
                                \App\Models\bidang::orderBy('nama', 'asc') // Pastikan urutan ASC
                                    ->pluck('nama')
                                    ->toArray()
                            )
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->required(),
                Forms\Components\select::make('stts_wp')
                    ->label('Status Wajib Pajak')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return stts_wp::query()
                        ->select(DB::raw("CONCAT(stts, ' - ', ktg) as label, stts"))
                        ->pluck('label', 'stts');
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('stts')
                            ->label('Kode')
                            ->required()
                            ->unique('stts_wp', 'stts'),

                        Forms\Components\TextInput::make('ktg')
                            ->label('Nama ')
                            ->required(),

                        Forms\Components\KeyValue::make('list_emergency')
                            ->label('Data Departemen')
                            ->default(
                                \App\Models\stts_wp::orderBy('stts', 'asc') // Pastikan urutan ASC
                                    ->pluck('ktg', 'stts')
                                    ->toArray()
                            )
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->required(),
                Forms\Components\select::make('stts_kerja')
                    ->label('Status Kerja')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return stts_kerja::query()
                        ->select(DB::raw("CONCAT(stts, ' - ', ktg) as label, stts"))
                        ->pluck('label', 'stts');
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('stts')
                            ->label('Kode')
                            ->required()
                            ->unique('stts_kerja', 'stts'),

                        Forms\Components\TextInput::make('ktg')
                            ->label('Nama ')
                            ->required(),

                        Forms\Components\KeyValue::make('list_emergency')
                            ->label('Data Departemen')
                            ->default(
                                \App\Models\stts_kerja::orderBy('stts', 'asc') // Pastikan urutan ASC
                                    ->pluck('ktg', 'stts')
                                    ->toArray()
                            )
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->required(),
                    TextInput::make('npwp')
                    ->label('NPWP')// ✅ Perbaiki format validasi
                    ->live(),

                // Di dalam form builder
                Select::make('pendidikan')
                    ->label('Tingkat Pendidikan')
                    ->options(pendidikan::pluck('tingkat', 'tingkat')) // Ambil data tingkat
                    ->required()
                    ->live() // Aktifkan real-time update
                    ->afterStateUpdated(function ($set, $state) {
                        // Ambil nilai gapok1 dari database berdasarkan tingkat pendidikan yang dipilih
                        $gapok1 = pendidikan::where('tingkat', $state)->value('gapok1') ?? 0;

                        // Set nilai gapok1 ke dalam form
                        $set('gapok1', $gapok1);

                        // **Pastikan juga menyimpan ke gapok (di tabel pegawai)**
                        $set('gapok', $gapok1);
                    })
                    ->createOptionForm([
                        Forms\Components\TextInput::make('tingkat')
                            ->label('Tingkat')
                            ->required()
                            ->unique('pendidikan', 'tingkat'),
                        Forms\Components\TextInput::make('gapok1')
                            ->numeric()
                            ->label('Gapok ')
                            ->required(),
                        Forms\Components\TextInput::make('indek')
                            ->numeric()
                            ->label('Indek')
                            ->required(),
                        Forms\Components\TextInput::make('kenaikan')
                            ->numeric()
                            ->label('Kenaikan')
                            ->required(),
                        Forms\Components\TextInput::make('maksimal')
                            ->numeric()
                            ->label('Maksimal')
                            ->required(),

                        Forms\Components\KeyValue::make('list_pendidikan')
                            ->label('Data Pendidikan')
                            ->default(
                                \App\Models\Pendidikan::orderBy('tingkat', 'asc')
                                    ->get(['tingkat', 'gapok1', 'indek', 'kenaikan', 'maksimal'])
                                    ->mapWithKeys(function ($item) {
                                        return [$item->tingkat => json_encode([
                                            'gapok1' => $item->gapok1,
                                            'indek' => $item->indek,
                                            'kenaikan' => $item->kenaikan,
                                            'maksimal' => $item->maksimal,
                                        ])];
                                    })
                                    ->toArray()
                            )


                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->createOptionUsing(function ($data) {
                        return \App\Models\Pendidikan::create([
                            'tingkat' => $data['tingkat'],
                            'gapok1' => $data['gapok1'],
                            'indek' => $data['indek'] ?? 0, // Pastikan ada nilai default jika tidak diisi
                            'kenaikan' => $data['kenaikan'] ?? 0, // Pastikan ada nilai default jika tidak diisi
                            'maksimal' => $data['maksimal'] ?? 0, // Pastikan ada nilai default jika tidak diisi
                        ]);
                    })

                    ->native(false),

                TextInput::make('gapok1')
                    ->label('Gaji Pokok')
                    ->live()
                    ->numeric()
                    ->required()
                    ->disabled(), // Non-editable

                    // Field gapok untuk disimpan ke tabel pegawai
                Hidden::make('gapok'), // Hidden agar tidak bisa diubah manual, tapi tetap tersimpan ke DB

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
                Forms\Components\select::make('bpd')
                    ->label('Nama Bank')
                    ->options(function () {
                        // Ambil semua jabatan dari tabel
                        return bank::all()->pluck('namabank', 'namabank');
                    })
                    ->required(),
                Forms\Components\TextInput::make('rekening')
                    ->required()
                    ->numeric() // Hanya angka
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
                Select::make('wajibmasuk')
                    ->label('Pilih Opsi Wajib Masuk')
                    ->options([
                        '-' => '- .Wajib Masuk 1 Bulan - Hari Libur',
                        '-1' => '-1 .Wajib Masuk Kosong',
                        '-2' => '-2 .Wajib Masuk 1 Bulan - 4 Hari',
                        '-3' => '-3 .Wajib Masuk 1 Bulan - 2 Hari - Linas',
                        '-4' => '-4 .Wajib Masuk 1 Bulan - Hari Akhad',
                        '-5' => '-5 .Wajib Mengikuti Penjadwalan',
                    ])
                    ->required()
                    ->helperText('Isi dengan "-" jika ingin wajib masuk 1 bulan-hari libur, dan seterusnya.')
                    ->dehydrateStateUsing(fn ($state) => $state === '-' ? 0 : (int) $state), // Konversi "-" ke 0
                Forms\Components\DatePicker::make('mulai_kontrak'),
                FileUpload::make('photo')
                    ->label('Foto Pegawai')
                    ->image()
                    ->disk('pegawai_photo') // Gunakan disk yang dikonfigurasi di filesystems.php
                    ->directory('') // Hindari pembuatan folder tambahan
                    ->getUploadedFileNameForStorageUsing(fn ($file) => uniqid() . '.' . $file->getClientOriginalExtension()) // Hanya simpan nama file
                    ->storeFileNamesIn('photo') // Pastikan hanya menyimpan nama file di database
                    ->required(),
                Forms\Components\TextInput::make('no_ktp')
                    ->label('Nomor KTP')
                    ->required()
                    ->numeric() // Hanya angka
                    ->minLength(16) // Minimal 16 digit
                    ->maxLength(16) // Maksimal 16 digit
                    ->rule('digits:16') // Pastikan tepat 16 digit
                    ->placeholder('Masukkan 16 digit NO KTP')
                    ->mask('9999999999999999') // Menambahkan format mask 16 digit
                    ->live(), // Memastikan validasi berjalan secara real-time

            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jk')
                    ->label('Jenis Kelamin')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('jbtn')
                    ->label('Jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jnj_jabatan')
                    ->label('Jenjang Jabatan')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_kelompok')
                    ->label('Kode Kelompok')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_resiko')
                    ->label('Kode Resiko')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_emergency')
                    ->label('Kode Emergency')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('departemen')
                    ->label('Departemen')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('bidang')
                    ->label('Bidang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stts_wp')
                    ->label('Status Wajib Pajak')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('stts_kerja')
                    ->label('Status Kerja')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('npwp')
                    ->label('NPWP')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('pendidikan')
                    ->label('Pendidikan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gapok')
                    ->label('Gaji Pokok')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tmp_lahir')
                    ->label('Tempat Lahir')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->label('Tanggal Lahir')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kota')
                    ->label('Kota')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('mulai_kerja')
                    ->label('Mulai Kerja')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ms_kerja')
                    ->label('Masa Kerja')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('indexins')
                    ->label('Index')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('bpd')
                    ->label('Bank')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('rekening')
                    ->label('Rekening')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('stts_aktif')
                    ->label('Aktif')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('wajibmasuk')
                    ->label('Wajib Masuk')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pengurang')
                    ->label('Pengurang')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('indek')
                    ->label('Indek')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mulai_kontrak')
                    ->label('Mulai Kontrak')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cuti_diambil')
                    ->label('Cuti Diambil')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dankes')
                    ->label('Dankes')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('photo')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Foto Pegawai')
                    ->circular()
                    ->size(50)
                    ->url(fn ($record) => url('/webapps/pages/pegawai/photo/' . basename($record->photo))),

                Tables\Columns\TextColumn::make('no_ktp')
                    ->label('No KTP')
                    ->sortable()
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
            // 'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
