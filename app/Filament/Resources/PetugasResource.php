<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PetugasResource\Pages;
use App\Filament\Resources\PetugasResource\RelationManagers;
use App\Models\Pegawai;
use App\Models\Petugas;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PetugasResource extends Resource
{
    protected static ?string $model = Petugas::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'SDM';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('nip')
                    ->label('NIK Pegawai')
                    ->placeholder('Ambil Dari Data Pegawai')
                    ->options(
                        Pegawai::all()->pluck('nama', 'nik')->map(fn($nama, $nik) => "$nik - $nama")
                    )
                    ->searchable()
                    ->live() // Memungkinkan update data secara real-time
                    ->afterStateUpdated(fn ($state, callable $set) => self::updatePetugasData($state, $set)),

                TextInput::make('nama')
                    ->label('Nama Petugas')
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
                TextInput::make('alamat')
                    ->label('Alamat')
                    ->disabled()
                    ->dehydrated(),
                Select::make('gol_darah')
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
                    ->rule(['nullable', 'numeric', 'digits:13'])
                    ->mask('9999999999999')
                    ->tel()
                    ->maxLength(13),
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
                Select::make('kd_jbtn')
                    ->label('Jabatan')
                    ->options(
                        \App\Models\jabatan::orderBy('kd_jbtn', 'asc') // Pastikan urutan ASC
                            ->pluck('nm_jbtn', 'kd_jbtn')
                            ->map(fn($nm_jbtn, $kd_jbtn) => "$kd_jbtn - $nm_jbtn") // Format: S0001 - Nama Spesialis
                    )
                    ->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('kd_jbtn')
                            ->label('Kode jabatan')
                            ->required()
                            ->unique('jabatan', 'kd_jbtn'),

                        Forms\Components\TextInput::make('nm_jbtn')
                            ->label('Nama jabatan')
                            ->required(),

                        Forms\Components\KeyValue::make('list_jabatan')
                            ->label('Data jabatan')
                            ->default(
                                \App\Models\jabatan::orderBy('kd_jbtn', 'asc') // Pastikan urutan ASC
                                    ->pluck('nm_jbtn', 'kd_jbtn')
                                    ->toArray()
                            )
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->required(),
                Toggle::make('status')
                    ->label('Status')
                    ->onColor('success')
                    ->offColor('danger')
                    ->default('1') // Default ke aktif
                    ->required(),
            ]);
    }

    public static function updatePetugasData($nik, callable $set)
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
                Tables\Columns\TextColumn::make('nip')
                    ->searchable()
                    ->sortable()
                    ->label('NIP'),
                Tables\Columns\TextColumn::make('nama')
                    ->sortable()
                    ->searchable()
                    ->label('Nama'),
                Tables\Columns\TextColumn::make('jk')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn ($state) => $state === 'L' ? 'Laki-Laki' : ($state === 'P' ? 'Perempuan' : '-'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('tmp_lahir')
                    ->label('Tempat Lahir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->label('Tanggal Lahir')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gol_darah')
                    ->label('Golongan Darah'),
                Tables\Columns\TextColumn::make('agama')
                    ->label('Agama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stts_nikah')
                    ->label('Status')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jabatan.nm_jbtn')
                    ->label('Jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telp')
                    ->label('No HP')
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
            'index' => Pages\ListPetugas::route('/'),
            'create' => Pages\CreatePetugas::route('/create'),
            'edit' => Pages\EditPetugas::route('/{record}/edit'),
        ];
    }
}
