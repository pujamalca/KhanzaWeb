<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PasienResource\Pages;
use App\Filament\Resources\PasienResource\RelationManagers;
use App\Models\Pasien;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class PasienResource extends Resource
{
    protected static ?string $model = Pasien::class;

    protected static ?string $navigationGroup = 'ERM';

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

     // Label jamak, ganti dengan singular jika perlu
     protected static ?string $pluralLabel = 'Pasien'; // Setel ke bentuk singular

     // Label seperti button new akan berubah
     protected static ?string $label = 'Pasien';
 
     // title menu akan berubah
     protected static ?string $navigationLabel = 'Pasien';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                TextInput::make('no_rkm_medis')
                        ->label('No. RM')
                        ->required()
                        ->default(function () {
                            $lastNumber = DB::table('pasien')
                                ->whereRaw('CHAR_LENGTH(no_rkm_medis) = 6')
                                ->select(DB::raw('MAX(CAST(no_rkm_medis AS UNSIGNED)) as max_no'))
                                ->value('max_no');
                        
                            $next = str_pad(((int)$lastNumber + 1), 6, '0', STR_PAD_LEFT);
                            return $next;
                        })
                        
                        ->numeric()
                        ->length(6),
                Forms\Components\TextInput::make('nm_pasien')
                    ->label('Nama Lengkap')
                    ->maxLength(40)
                    ->default(null)
                    ->placeholder('Nama Lengkap (Tanpa Gelar) Sesuai KTP'),
                Forms\Components\TextInput::make('no_ktp')
                    ->label('NIK')
                    ->maxLength(21)
                    ->default(null),
                Forms\Components\Select::make('jk')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->placeholder('Pilih Jenis Kelamin') // 🔸 default kosong
                    ->required(),
                Forms\Components\TextInput::make('tmp_lahir')
                    ->label('Tempat Lahir')
                    ->maxLength(15)
                    ->default(null)
                    ->placeholder('Tempat Lahir Sesuai KTP'),
                Forms\Components\DatePicker::make('tgl_lahir')
                ->label('Tanggal Lahir'),
                Forms\Components\TextInput::make('nm_ibu')
                    ->label('Nama Ibu Kandung')
                    ->required()
                    ->maxLength(40),
                Forms\Components\TextInput::make('alamat')
                    ->maxLength(200)
                    ->default(null),
                Forms\Components\TextInput::make('gol_darah'),
                Forms\Components\TextInput::make('pekerjaan')
                    ->maxLength(60)
                    ->default(null),
                Forms\Components\TextInput::make('stts_nikah'),
                Forms\Components\TextInput::make('agama')
                    ->maxLength(12)
                    ->default(null),
                Forms\Components\DatePicker::make('tgl_daftar'),
                Forms\Components\TextInput::make('no_tlp')
                    ->maxLength(40)
                    ->default(null),
                Forms\Components\TextInput::make('umur')
                    ->required()
                    ->maxLength(30),
                Forms\Components\TextInput::make('pnd')
                    ->required(),
                Forms\Components\TextInput::make('keluarga'),
                Forms\Components\TextInput::make('namakeluarga')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('kd_pj')
                    ->required()
                    ->maxLength(3),
                Forms\Components\TextInput::make('no_peserta')
                    ->maxLength(25)
                    ->default(null),
                Forms\Components\TextInput::make('kd_kel')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kd_kec')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('kd_kab')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('pekerjaanpj')
                    ->required()
                    ->maxLength(35),
                Forms\Components\TextInput::make('alamatpj')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('kelurahanpj')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('kecamatanpj')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('kabupatenpj')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('perusahaan_pasien')
                    ->required()
                    ->maxLength(8),
                Forms\Components\TextInput::make('suku_bangsa')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('bahasa_pasien')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cacat_fisik')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('nip')
                    ->required()
                    ->maxLength(30),
                Forms\Components\TextInput::make('kd_prop')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('propinsipj')
                    ->required()
                    ->maxLength(30),
                    ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_rkm_medis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nm_pasien')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_ktp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jk'),
                Tables\Columns\TextColumn::make('tmp_lahir')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nm_ibu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gol_darah'),
                Tables\Columns\TextColumn::make('pekerjaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stts_nikah'),
                Tables\Columns\TextColumn::make('agama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_daftar')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_tlp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('umur')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pnd'),
                Tables\Columns\TextColumn::make('keluarga'),
                Tables\Columns\TextColumn::make('namakeluarga')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kd_pj')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_peserta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kd_kel')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kd_kec')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kd_kab')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pekerjaanpj')
                    ->searchable(),
                Tables\Columns\TextColumn::make('alamatpj')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelurahanpj')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kecamatanpj')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kabupatenpj')
                    ->searchable(),
                Tables\Columns\TextColumn::make('perusahaan_pasien')
                    ->searchable(),
                Tables\Columns\TextColumn::make('suku_bangsa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bahasa_pasien')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cacat_fisik')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nip')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kd_prop')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('propinsipj')
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
            'index' => Pages\ListPasiens::route('/'),
            'create' => Pages\CreatePasien::route('/create'),
            'edit' => Pages\EditPasien::route('/{record}/edit'),
        ];
    }
}
