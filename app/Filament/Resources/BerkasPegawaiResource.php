<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BerkasPegawaiResource\Pages;
use App\Filament\Resources\BerkasPegawaiResource\RelationManagers;
use App\Models\berkas_pegawai;
use App\Models\master_berkas_pegawai;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class BerkasPegawaiResource extends Resource
{
    protected static ?string $model = berkas_pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
        {
            return static::getModel()::count();
        }

    protected static ?string $navigationGroup = 'SDM';

    // Label jamak, ganti dengan singular jika perlu
    protected static ?string $pluralLabel = ' Berkas Pegawai'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = ' Berkas Pegawai';

    // title menu akan berubah
    protected static ?string $navigationLabel = ' Berkas Pegawai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('nik')
                    ->label('NIK Pegawai')
                    ->placeholder('Ambil Dari Data Pegawai')
                    ->options(
                        Pegawai::all()->pluck('nama', 'nik')->map(fn($nama, $nik) => "$nik - $nama")
                    )
                    ->searchable()
                    ->live() // Memungkinkan update data secara real-time
                    ->afterStateUpdated(fn ($state, callable $set) => self::updatePegawaiData($state, $set)),

                TextInput::make('nama')
                    ->label('Nama Pegawai')
                    ->disabled() // Tidak bisa diisi manual
                    ->dehydrated(), // Pastikan tetap tersimpan di DB
                DatePicker::make('tgl_upload')
                    ->label('Tanggal Upload')
                    ->default(Carbon::now()) // Set default tanggal hari ini
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d') // Format penyimpanan ke database
                    ->disabled() // Tidak bisa diupdate saat edit
                    ->required(),

                Select::make('kategori')
                    ->label('Kategori')
                    ->options(
                        master_berkas_pegawai::query()
                            ->distinct()
                            ->pluck('kategori', 'kategori')
                    )
                    ->required()
                    ->reactive(),
                Select::make('kode')
                    ->label('Kode Berkas')
                    ->options(function (callable $get) {
                        $kategori = $get('kategori'); // Ambil kategori yang dipilih
                        if (!$kategori) {
                            return [];
                        }

                        return master_berkas_pegawai::where('kategori', $kategori)
                            ->orderBy('kode', 'asc')
                            ->pluck('nama_berkas', 'kode')
                            ->map(fn($nama_berkas, $kode) => "$kode - $nama_berkas");
                    })
                    ->required()
                    ->disabled(fn (callable $get) => !$get('kategori'))
                    ->searchable()
                    ->required(),
                FileUpload::make('berkas')
                    ->label('Berkas')
                    ->image()
                    ->directory('pages/berkaspegawai/photo') // Simpan di Laravel storage (public/pages/pegawai/photo)
                    ->required()
                    ->label('berkas'),
                Checkbox::make('set_tgl_berakhir')
                    ->label('Aktifkan Tanggal Berakhir Jika Berkas Ada Batas Waktu')
                    ->reactive(), // Agar langsung merespons saat dicentang

                DatePicker::make('tgl_berakhir')
                    ->label('Tanggal Berakhir')
                    ->placeholder('Pilih Tanggal')
                    ->native(false)
                    ->displayFormat('d/m/Y') // Format tampilan
                    ->visible(fn (callable $get) => $get('set_tgl_berakhir')) // Muncul hanya jika dicentang
                    ->required(fn (callable $get) => $get('set_tgl_berakhir')), // Wajib diisi jika dicentang
            ]);
    }

    public static function updatePegawaiData($nik, callable $set)
    {
        if (!$nik) {
            return;
        }

        $pegawai = Pegawai::where('nik', $nik)->first();
        if ($pegawai) {
            // Konversi jenis kelamin dari Pegawai ke format ENUM Dokter
            $jk = strtolower($pegawai->jk) === 'wanita' ? 'P' : 'L';
            $set('nama', $pegawai->nama);
            $set('jk', $jk);
            $set('tmp_lahir', $pegawai->tmp_lahir);
            $set('tgl_lahir', $pegawai->tgl_lahir);
            $set('alamat', $pegawai->alamat);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    Tables\Columns\TextColumn::make('nik')->sortable()->searchable(),
                    Tables\Columns\TextColumn::make('tgl_upload')->date(),
                    Tables\Columns\TextColumn::make('kode_berkas')->sortable()->searchable(),
                    Tables\Columns\TextColumn::make('berkas')->limit(50),
            ])
            ->defaultSort('tgl_upload', 'desc')
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
            'index' => Pages\ListBerkasPegawais::route('/'),
            'create' => Pages\CreateBerkasPegawai::route('/create'),
            'edit' => Pages\EditBerkasPegawai::route('/{record}/edit'),
        ];
    }
}
