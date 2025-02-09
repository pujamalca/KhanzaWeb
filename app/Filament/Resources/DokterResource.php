<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DokterResource\Pages;
use App\Filament\Resources\DokterResource\RelationManagers;
use App\Models\Dokter;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DokterResource extends Resource
{
    protected static ?string $model = Dokter::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'SDM';

    // Label jamak, ganti dengan singular jika perlu
    protected static ?string $pluralLabel = 'Dokter'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = 'Dokter';

    // title menu akan berubah
    protected static ?string $navigationLabel = 'Dokter';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kd_dokter')
                    ->label('NIK Pegawai')
                    ->placeholder('Ambil Dari Data Pegawai')
                    ->options(
                        Pegawai::all()->pluck('nama', 'nik')->map(fn($nama, $nik) => "$nik - $nama")
                    )
                    ->searchable()
                    ->live() // Memungkinkan update data secara real-time
                    ->afterStateUpdated(fn ($state, callable $set) => self::updateDokterData($state, $set)),


                TextInput::make('nm_dokter')
                    ->label('Nama Dokter')
                    ->disabled() // Tidak bisa diisi manual
                    ->dehydrated(), // Pastikan tetap tersimpan di DB

                TextInput::make('jk')
                    ->label('Jenis Kelamin')
                    ->disabled()
                    ->dehydrated(),
                TextInput::make('tgl_lahir')
                    ->label('Tanggal Lahir')
                    ->disabled()
                    ->dehydrated(),
                TextInput::make('tmp_lahir')
                    ->label('Tempat Lahir')
                    ->disabled()
                    ->dehydrated(),
                TextInput::make('almt_tgl')
                    ->label('Alamat')
                    ->disabled()
                    ->dehydrated(),
                Select::make('gol_drh')
                    ->label('Golongan Darah')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'O' => 'O',
                        'AB' => 'AB',
                        '-' => '-',
                    ])
                    ->searchable()
                    ->placeholder('Pilih Golongan Darah') // Jika ingin bisa dicari
                    ->required(),  // Jika kolom wajib diisi

                Select::make('agama')
                    ->label('Agama')
                    ->placeholder('Pilih Agama')
                    ->options([
                        'ISLAM' => 'ISLAM',
                        'KRISTEN' => 'KRISTEN',
                        'HINDU' => 'HINDU',
                        'BUDDHA' => 'BUDDHA',
                        'KONGHUCU' => 'KONGHUCU',
                        'LAINNYA' => 'LAINNYA',
                    ])
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('no_telp')
                    ->label('No HP (Utamakan WhatsApp)')
                    ->numeric()
                    ->tel()
                    ->maxLength(13)
                    ->default(null),
                Select::make('stts_nikah')
                    ->label('Status Pernikahan')
                    ->options([
                        'BELUM MENIKAH' => 'Belum Menikah',
                        'MENIKAH' => 'Menikah',
                        'JANDA' => 'Janda',
                        'DUDA' => 'Duda',
                        'JOMBLO' => 'Jomblo',
                    ])
                    ->searchable()
                    ->required(),

                Select::make('kd_sps')
                    ->label('Spesialis')
                    ->options(
                        \App\Models\Spesialis::pluck('nm_sps', 'kd_sps')
                    )
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('alumni')
                    ->maxLength(60)
                    ->default(null),
                Forms\Components\TextInput::make('no_ijn_praktek')
                    ->maxLength(120),
                Toggle::make('status')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default('1') // Default ke aktif
                    ->required(),


            ])
            ->columns(3);
    }

    public static function updateDokterData($nik, callable $set)
    {
        if (!$nik) {
            return;
        }

        $pegawai = Pegawai::where('nik', $nik)->first();
        if ($pegawai) {
            // Konversi jenis kelamin dari Pegawai ke format ENUM Dokter
            $jk = strtolower($pegawai->jk) === 'wanita' ? 'P' : 'L';
            $set('nm_dokter', $pegawai->nama);
            $set('jk', $jk);
            $set('tmp_lahir', $pegawai->tmp_lahir);
            $set('tgl_lahir', $pegawai->tgl_lahir);
            $set('almt_tgl', $pegawai->alamat);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kd_dokter')
                ->searchable()
                ->sortable()
                ->label('Kode Dokter'),
                Tables\Columns\TextColumn::make('nm_dokter')
                ->sortable()
                ->searchable()
                ->label('Nama Dokter'),

                // Konversi 'P' dan 'L' menjadi teks yang lebih informatif
                Tables\Columns\TextColumn::make('jk')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn ($state) => $state === 'P' ? 'Perempuan' : 'Laki-laki'),

                Tables\Columns\TextColumn::make('tmp_lahir')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true)
                ->label('Tempat Lahir'),
                Tables\Columns\TextColumn::make('tgl_lahir')
                ->label('Tanggal Lahir')
                ->date()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gol_drh')
                ->label('Golongan Darah'),
                Tables\Columns\TextColumn::make('agama')
                ->label('Agama')
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('almt_tgl')
                ->label('Alamat')
                ->searchable(),
                Tables\Columns\TextColumn::make('no_telp')
                ->label('No HP')
                ->searchable(),
                Tables\Columns\TextColumn::make('stts_nikah')
                ->label('Status')
                ->toggleable(isToggledHiddenByDefault: true),

                // Menampilkan nama spesialis di tabel, bukan kode
                Tables\Columns\TextColumn::make('spesialis.nm_sps')
                    ->label('Spesialis')
                    ->searchable(),

                Tables\Columns\TextColumn::make('alumni')
                ->label('Alumni')
                ->searchable(),
                Tables\Columns\TextColumn::make('no_ijn_praktek')
                ->label('SIP')
                ->searchable(),

                // Menampilkan status sebagai ikon centang dan X
                BooleanColumn::make('status')
                    ->label('Status')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
               
            ])
            ->defaultSort('nm_dokter', 'asc')
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
            'index' => Pages\ListDokters::route('/'),
            'create' => Pages\CreateDokter::route('/create'),
            'edit' => Pages\EditDokter::route('/{record}/edit'),
        ];
    }
}
