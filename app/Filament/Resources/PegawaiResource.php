<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Filament\Resources\PegawaiResource\RelationManagers;
use App\Models\jnj_jabatan;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\TextInput::make('kode_kelompok')
                    ->required()
                    ->maxLength(3),
                Forms\Components\TextInput::make('kode_resiko')
                    ->required()
                    ->maxLength(3),
                Forms\Components\TextInput::make('kode_emergency')
                    ->required()
                    ->maxLength(3),
                Forms\Components\TextInput::make('departemen')
                    ->required()
                    ->maxLength(4),
                Forms\Components\TextInput::make('bidang')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('stts_wp')
                    ->required()
                    ->maxLength(5),
                Forms\Components\TextInput::make('stts_kerja')
                    ->required()
                    ->maxLength(3),
                Forms\Components\TextInput::make('npwp')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('pendidikan')
                    ->required()
                    ->maxLength(80),
                Forms\Components\TextInput::make('gapok')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('tmp_lahir')
                    ->required()
                    ->maxLength(20),
                Forms\Components\DatePicker::make('tgl_lahir')
                    ->required(),
                Forms\Components\TextInput::make('alamat')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('kota')
                    ->required()
                    ->maxLength(20),
                Forms\Components\DatePicker::make('mulai_kerja')
                    ->required(),
                Forms\Components\TextInput::make('ms_kerja')
                    ->required(),
                Forms\Components\TextInput::make('indexins')
                    ->required()
                    ->maxLength(4),
                Forms\Components\TextInput::make('bpd')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('rekening')
                    ->required()
                    ->maxLength(25),
                Forms\Components\TextInput::make('stts_aktif')
                    ->required(),
                Forms\Components\TextInput::make('wajibmasuk')
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
            ]);
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
