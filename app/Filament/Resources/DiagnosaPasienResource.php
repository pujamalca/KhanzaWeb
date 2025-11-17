<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DiagnosaPasienResource\Pages;
use App\Models\DiagnosaPasien;
use App\Models\reg_periksa;
use App\Models\Penyakit;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Radio;
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

class DiagnosaPasienResource extends Resource
{
    protected static ?string $model = DiagnosaPasien::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Diagnosis Pasien';

    protected static ?string $pluralLabel = 'Diagnosis Pasien';

    protected static ?string $label = 'Diagnosis Pasien';

    protected static ?int $navigationSort = 6;

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
                                    ->whereDate('tgl_registrasi', '>=', Carbon::now()->subDays(30))
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
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tgl_diagnosis')
                                    ->label('Tanggal Diagnosis')
                                    ->required()
                                    ->default(now())
                                    ->native(false),

                                TimePicker::make('jam_diagnosis')
                                    ->label('Jam Diagnosis')
                                    ->required()
                                    ->default(now()->format('H:i:s'))
                                    ->seconds(true),
                            ]),
                    ])
                    ->columns(2),

                Section::make('Diagnosis (ICD-10)')
                    ->schema([
                        Select::make('kd_penyakit')
                            ->label('Kode ICD-10 - Penyakit')
                            ->options(function () {
                                return Penyakit::with('kategori')
                                    ->limit(100)
                                    ->get()
                                    ->mapWithKeys(function ($penyakit) {
                                        $kategori = $penyakit->kategori->nm_kategori ?? 'Uncategorized';
                                        return [
                                            $penyakit->kd_penyakit => "{$penyakit->kd_penyakit} - {$penyakit->nm_penyakit} ({$kategori})"
                                        ];
                                    });
                            })
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return Penyakit::where('kd_penyakit', 'like', "%{$search}%")
                                    ->orWhere('nm_penyakit', 'like', "%{$search}%")
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($penyakit) {
                                        return [
                                            $penyakit->kd_penyakit => "{$penyakit->kd_penyakit} - {$penyakit->nm_penyakit}"
                                        ];
                                    });
                            })
                            ->required()
                            ->helperText('Cari berdasarkan kode atau nama penyakit')
                            ->columnSpanFull(),

                        Radio::make('prioritas')
                            ->label('Prioritas Diagnosis')
                            ->options([
                                '1' => 'Diagnosa Utama (Primary)',
                                '2' => 'Diagnosa Sekunder 1',
                                '3' => 'Diagnosa Sekunder 2',
                                '4' => 'Diagnosa Sekunder 3',
                            ])
                            ->default('1')
                            ->required()
                            ->inline()
                            ->helperText('Diagnosa utama adalah diagnosa paling penting')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                Radio::make('status')
                                    ->label('Tipe Rawat')
                                    ->options([
                                        'Ralan' => 'Rawat Jalan',
                                        'Ranap' => 'Rawat Inap',
                                    ])
                                    ->default('Ralan')
                                    ->required()
                                    ->inline(),

                                Radio::make('status_penyakit')
                                    ->label('Status Penyakit')
                                    ->options([
                                        'Baru' => 'Kasus Baru',
                                        'Lama' => 'Kasus Lama/Berulang',
                                    ])
                                    ->default('Baru')
                                    ->required()
                                    ->inline(),
                            ]),
                    ]),

                Section::make('Metadata')
                    ->schema([
                        Select::make('kd_dokter')
                            ->label('Dokter')
                            ->options(\App\Models\Dokter::pluck('nm_dokter', 'kd_dokter'))
                            ->searchable()
                            ->helperText('Dokter yang mendiagnosis'),
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

                TextColumn::make('kd_penyakit')
                    ->label('Kode ICD-10')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('penyakit.nm_penyakit')
                    ->label('Penyakit')
                    ->searchable()
                    ->limit(40)
                    ->wrap(),

                BadgeColumn::make('priority_label')
                    ->label('Prioritas')
                    ->color(fn ($record) => match($record->prioritas) {
                        '1' => 'danger',
                        '2' => 'warning',
                        '3' => 'info',
                        '4' => 'gray',
                        default => 'gray'
                    })
                    ->sortable(['prioritas']),

                BadgeColumn::make('status')
                    ->label('Rawat')
                    ->color(fn (string $state): string => match ($state) {
                        'Ralan' => 'success',
                        'Ranap' => 'primary',
                        default => 'gray',
                    }),

                BadgeColumn::make('status_penyakit')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'success',
                        'Lama' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('formatted_date_time')
                    ->label('Waktu Diagnosis')
                    ->sortable(['tgl_diagnosis', 'jam_diagnosis']),

                TextColumn::make('dokter.nm_dokter')
                    ->label('Dokter')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('primary')
                    ->label('Diagnosa Utama')
                    ->query(fn (Builder $query) => $query->primary()),

                Filter::make('secondary')
                    ->label('Diagnosa Sekunder')
                    ->query(fn (Builder $query) => $query->secondary()),

                Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query) => $query->today()),

                Filter::make('new_cases')
                    ->label('Kasus Baru')
                    ->query(fn (Builder $query) => $query->newCases()),

                SelectFilter::make('status')
                    ->label('Tipe Rawat')
                    ->options([
                        'Ralan' => 'Rawat Jalan',
                        'Ranap' => 'Rawat Inap',
                    ]),

                SelectFilter::make('prioritas')
                    ->label('Prioritas')
                    ->options([
                        '1' => 'Diagnosa Utama',
                        '2' => 'Diagnosa Sekunder 1',
                        '3' => 'Diagnosa Sekunder 2',
                        '4' => 'Diagnosa Sekunder 3',
                    ]),

                SelectFilter::make('dokter')
                    ->label('Filter Dokter')
                    ->relationship('dokter', 'nm_dokter')
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
            ->defaultSort('tgl_diagnosis', 'desc');
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
            'index' => Pages\ListDiagnosaPasiens::route('/'),
            'create' => Pages\CreateDiagnosaPasien::route('/create'),
            'view' => Pages\ViewDiagnosaPasien::route('/{record}'),
            'edit' => Pages\EditDiagnosaPasien::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $today = static::getModel()::today()->count();
        return $today > 0 ? (string) $today : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
