<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemeriksaanRalanResource\Pages;
use App\Models\PemeriksaanRalan;
use App\Models\reg_periksa;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PemeriksaanRalanResource extends Resource
{
    protected static ?string $model = PemeriksaanRalan::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Pemeriksaan Pasien';

    protected static ?string $pluralLabel = 'Pemeriksaan Pasien';

    protected static ?string $label = 'Pemeriksaan Pasien';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Pasien')
                    ->schema([
                        Select::make('no_rawat')
                            ->label('No. Rawat - Pasien')
                            ->options(function () {
                                return reg_periksa::with(['pasien', 'poliklinik'])
                                    ->whereDate('tgl_registrasi', '>=', Carbon::now()->subDays(7))
                                    ->orderBy('tgl_registrasi', 'desc')
                                    ->limit(100)
                                    ->get()
                                    ->mapWithKeys(function ($reg) {
                                        $pasienName = $reg->pasien->nm_pasien ?? 'Unknown';
                                        $poliName = $reg->poliklinik->nm_poli ?? '-';
                                        return [
                                            $reg->no_rawat => "{$reg->no_rawat} - {$pasienName} ({$poliName})"
                                        ];
                                    });
                            })
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return reg_periksa::with(['pasien', 'poliklinik'])
                                    ->where('no_rawat', 'like', "%{$search}%")
                                    ->orWhereHas('pasien', function ($query) use ($search) {
                                        $query->where('nm_pasien', 'like', "%{$search}%")
                                              ->orWhere('no_rkm_medis', 'like', "%{$search}%");
                                    })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($reg) {
                                        $pasienName = $reg->pasien->nm_pasien ?? 'Unknown';
                                        $poliName = $reg->poliklinik->nm_poli ?? '-';
                                        return [
                                            $reg->no_rawat => "{$reg->no_rawat} - {$pasienName} ({$poliName})"
                                        ];
                                    });
                            })
                            ->required()
                            ->disabled(fn ($context) => $context === 'edit')
                            ->helperText('Pilih nomor rawat pasien')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tgl_perawatan')
                                    ->label('Tanggal Pemeriksaan')
                                    ->required()
                                    ->default(now())
                                    ->native(false),

                                TimePicker::make('jam_rawat')
                                    ->label('Jam Pemeriksaan')
                                    ->required()
                                    ->default(now()->format('H:i:s'))
                                    ->seconds(true),
                            ]),
                    ])
                    ->columns(2),

                Section::make('Tanda Vital')
                    ->description('Vital signs / Pemeriksaan fisik dasar')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('suhu_tubuh')
                                    ->label('Suhu Tubuh (°C)')
                                    ->numeric()
                                    ->step(0.1)
                                    ->placeholder('36.5')
                                    ->suffix('°C')
                                    ->minValue(30)
                                    ->maxValue(45),

                                TextInput::make('tensi')
                                    ->label('Tekanan Darah')
                                    ->placeholder('120/80')
                                    ->suffix('mmHg')
                                    ->helperText('Format: 120/80'),

                                TextInput::make('nadi')
                                    ->label('Nadi/HR')
                                    ->numeric()
                                    ->placeholder('80')
                                    ->suffix('bpm')
                                    ->minValue(40)
                                    ->maxValue(200),

                                TextInput::make('respirasi')
                                    ->label('Respirasi/RR')
                                    ->numeric()
                                    ->placeholder('20')
                                    ->suffix('x/menit')
                                    ->minValue(10)
                                    ->maxValue(60),
                            ]),

                        Grid::make(4)
                            ->schema([
                                TextInput::make('tinggi')
                                    ->label('Tinggi Badan (cm)')
                                    ->numeric()
                                    ->step(0.1)
                                    ->placeholder('170')
                                    ->suffix('cm')
                                    ->reactive()
                                    ->minValue(50)
                                    ->maxValue(250),

                                TextInput::make('berat')
                                    ->label('Berat Badan (kg)')
                                    ->numeric()
                                    ->step(0.1)
                                    ->placeholder('70')
                                    ->suffix('kg')
                                    ->reactive()
                                    ->minValue(10)
                                    ->maxValue(300),

                                Placeholder::make('bmi_display')
                                    ->label('BMI')
                                    ->content(function ($get) {
                                        $weight = (float) $get('berat');
                                        $height = (float) $get('tinggi');

                                        if (!$weight || !$height || $height <= 0) {
                                            return '-';
                                        }

                                        $heightInMeters = $height / 100;
                                        $bmi = $weight / ($heightInMeters * $heightInMeters);
                                        $bmiRounded = round($bmi, 2);

                                        $category = match(true) {
                                            $bmi < 18.5 => 'Underweight',
                                            $bmi < 25 => 'Normal',
                                            $bmi < 30 => 'Overweight',
                                            default => 'Obese'
                                        };

                                        return "{$bmiRounded} ({$category})";
                                    }),

                                TextInput::make('lingkar_perut')
                                    ->label('Lingkar Perut (cm)')
                                    ->numeric()
                                    ->step(0.1)
                                    ->placeholder('80')
                                    ->suffix('cm'),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('spo2')
                                    ->label('SpO2')
                                    ->numeric()
                                    ->placeholder('98')
                                    ->suffix('%')
                                    ->minValue(70)
                                    ->maxValue(100),

                                TextInput::make('gcs')
                                    ->label('GCS')
                                    ->placeholder('E4V5M6 (15)')
                                    ->helperText('Glasgow Coma Scale'),

                                TextInput::make('kesadaran')
                                    ->label('Kesadaran')
                                    ->placeholder('Composmentis')
                                    ->datalist([
                                        'Composmentis',
                                        'Apatis',
                                        'Somnolen',
                                        'Sopor',
                                        'Koma',
                                    ]),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('SOAP Notes')
                    ->description('Subjective, Objective, Assessment, Plan')
                    ->schema([
                        Textarea::make('keluhan')
                            ->label('Subjective: Keluhan Utama')
                            ->placeholder('Keluhan pasien, riwayat penyakit sekarang...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Keluhan yang disampaikan pasien'),

                        Textarea::make('pemeriksaan')
                            ->label('Objective: Pemeriksaan Fisik')
                            ->placeholder('Hasil pemeriksaan fisik, inspeksi, palpasi, perkusi, auskultasi...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Temuan objektif dari pemeriksaan fisik'),

                        Textarea::make('penilaian')
                            ->label('Assessment: Penilaian/Diagnosis')
                            ->placeholder('Diagnosis kerja, diagnosis banding...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Penilaian klinis dan diagnosis'),

                        Textarea::make('rtl')
                            ->label('Plan: Rencana Tindak Lanjut')
                            ->placeholder('Rencana pengobatan, tindakan, rujukan...')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Rencana tindak lanjut dan pengobatan'),

                        Grid::make(2)
                            ->schema([
                                Textarea::make('instruksi')
                                    ->label('Instruksi Medis')
                                    ->placeholder('Instruksi khusus untuk pasien...')
                                    ->rows(2),

                                Textarea::make('evaluasi')
                                    ->label('Evaluasi')
                                    ->placeholder('Evaluasi kondisi pasien...')
                                    ->rows(2),
                            ]),

                        Textarea::make('alergi')
                            ->label('Alergi')
                            ->placeholder('Alergi obat, makanan, dll...')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Metadata')
                    ->schema([
                        Select::make('nip')
                            ->label('Petugas/Perawat')
                            ->options(\App\Models\Petugas::pluck('nama', 'nip'))
                            ->searchable()
                            ->helperText('Petugas yang melakukan pemeriksaan'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('regPeriksa.pasien.nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('regPeriksa.pasien.no_rkm_medis')
                    ->label('No. RM')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('formatted_date_time')
                    ->label('Waktu Pemeriksaan')
                    ->sortable(['tgl_perawatan', 'jam_rawat'])
                    ->searchable(['tgl_perawatan']),

                TextColumn::make('keluhan')
                    ->label('Keluhan')
                    ->limit(30)
                    ->wrap()
                    ->searchable(),

                BadgeColumn::make('is_vital_signs_complete')
                    ->label('Vital Signs')
                    ->formatStateUsing(fn ($state) => $state ? 'Lengkap' : 'Belum')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),

                BadgeColumn::make('is_soap_complete')
                    ->label('SOAP Notes')
                    ->formatStateUsing(fn ($state) => $state ? 'Lengkap' : 'Belum')
                    ->color(fn ($state) => $state ? 'success' : 'warning'),

                TextColumn::make('bmi')
                    ->label('BMI')
                    ->formatStateUsing(fn ($state, $record) =>
                        $state ? "{$state} ({$record->bmi_category})" : '-'
                    )
                    ->badge()
                    ->color(fn ($record) => match($record->bmi_category) {
                        'Normal' => 'success',
                        'Underweight' => 'warning',
                        'Overweight' => 'warning',
                        'Obese' => 'danger',
                        default => 'gray'
                    })
                    ->toggleable(),

                TextColumn::make('petugas.nama')
                    ->label('Petugas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('regPeriksa.poliklinik.nm_poli')
                    ->label('Poli')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query) => $query->today()),

                Filter::make('recent')
                    ->label('7 Hari Terakhir')
                    ->query(fn (Builder $query) => $query->recent(7)),

                Filter::make('incomplete_soap')
                    ->label('SOAP Belum Lengkap')
                    ->query(fn (Builder $query) => $query->incompleteSoap()),

                SelectFilter::make('petugas')
                    ->label('Filter Petugas')
                    ->relationship('petugas', 'nama')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tgl_perawatan', 'desc');
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
            'index' => Pages\ListPemeriksaanRalans::route('/'),
            'create' => Pages\CreatePemeriksaanRalan::route('/create'),
            'view' => Pages\ViewPemeriksaanRalan::route('/{record}'),
            'edit' => Pages\EditPemeriksaanRalan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $incomplete = static::getModel()::incompleteSoap()->count();
        return $incomplete > 0 ? (string) $incomplete : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $incomplete = static::getModel()::incompleteSoap()->count();
        return $incomplete > 0 ? 'warning' : null;
    }
}
