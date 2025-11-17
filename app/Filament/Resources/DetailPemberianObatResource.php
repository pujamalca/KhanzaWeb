<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetailPemberianObatResource\Pages;
use App\Models\DetailPemberianObat;
use App\Models\reg_periksa;
use App\Models\Databarang;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class DetailPemberianObatResource extends Resource
{
    protected static ?string $model = DetailPemberianObat::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-beaker';

    protected static ?string $navigationLabel = 'Resep Obat';

    protected static ?string $pluralLabel = 'Resep Obat';

    protected static ?string $label = 'Resep Obat';

    protected static ?int $navigationSort = 7;

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
                                        $query->where('nm_pasien', 'like', "%{$search}%");
                                    })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($reg) {
                                        $pasienName = $reg->pasien->nm_pasien ?? 'Unknown';
                                        return [
                                            $reg->no_rawat => "{$reg->no_rawat} - {$pasienName}"
                                        ];
                                    });
                            })
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tgl_perawatan')
                                    ->label('Tanggal Resep')
                                    ->required()
                                    ->default(now())
                                    ->native(false),

                                TimePicker::make('jam')
                                    ->label('Jam')
                                    ->required()
                                    ->default(now()->format('H:i:s'))
                                    ->seconds(true),
                            ]),
                    ])
                    ->columns(2),

                Section::make('Obat & Jumlah')
                    ->schema([
                        Select::make('kode_brng')
                            ->label('Obat')
                            ->options(function () {
                                return Databarang::active()
                                    ->where('kd_jenis', 'OBAT')
                                    ->limit(100)
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->kode_brng => "{$item->nama_brng} (Stok: {$item->stok} {$item->satuan->satuan ?? ''})"
                                        ];
                                    });
                            })
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return Databarang::active()
                                    ->where(function($q) use ($search) {
                                        $q->where('nama_brng', 'like', "%{$search}%")
                                          ->orWhere('kode_brng', 'like', "%{$search}%");
                                    })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        return [
                                            $item->kode_brng => "{$item->nama_brng} (Stok: {$item->stok})"
                                        ];
                                    });
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($set, $state) {
                                if ($state) {
                                    $item = Databarang::find($state);
                                    if ($item) {
                                        $set('harga_satuan', $item->ralan);
                                    }
                                }
                            })
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->schema([
                                TextInput::make('jml')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(0.1)
                                    ->reactive()
                                    ->helperText('Quantity'),

                                TextInput::make('embalase')
                                    ->label('Biaya Embalase')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('Rp')
                                    ->reactive(),

                                TextInput::make('tuslah')
                                    ->label('Biaya Tuslah')
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('Rp')
                                    ->reactive(),
                            ]),

                        Placeholder::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->hidden()
                            ->reactive(),

                        Placeholder::make('total_display')
                            ->label('Total Biaya')
                            ->content(function ($get) {
                                $jml = (float) ($get('jml') ?? 0);
                                $harga = (float) ($get('harga_satuan') ?? 0);
                                $embalase = (float) ($get('embalase') ?? 0);
                                $tuslah = (float) ($get('tuslah') ?? 0);

                                $subtotal = $jml * $harga;
                                $total = $subtotal + $embalase + $tuslah;

                                return 'Rp ' . number_format($total, 0, ',', '.');
                            })
                            ->helperText('Auto-calculated'),
                    ]),

                Section::make('Aturan Pakai')
                    ->description('Dosage & frequency instructions')
                    ->schema([
                        TextInput::make('dosis')
                            ->label('Dosis')
                            ->placeholder('500mg')
                            ->helperText('e.g., 500mg, 1 tablet, 5ml'),

                        TextInput::make('frekuensi')
                            ->label('Frekuensi')
                            ->placeholder('3x sehari')
                            ->datalist([
                                '1x sehari',
                                '2x sehari',
                                '3x sehari',
                                '4x sehari',
                                'Setiap 4 jam',
                                'Setiap 6 jam',
                                'Setiap 8 jam',
                            ])
                            ->helperText('How often'),

                        TextInput::make('aturan_pakai')
                            ->label('Aturan Pakai')
                            ->placeholder('Sesudah makan')
                            ->datalist([
                                'Sesudah makan',
                                'Sebelum makan',
                                'Saat makan',
                                'Sebelum tidur',
                                'Saat perlu',
                            ])
                            ->helperText('Special instructions'),

                        Textarea::make('catatan')
                            ->label('Catatan Tambahan')
                            ->rows(2)
                            ->placeholder('Instruksi khusus untuk pasien...')
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Metadata')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('kd_dokter')
                                    ->label('Dokter')
                                    ->options(\App\Models\Dokter::pluck('nm_dokter', 'kd_dokter'))
                                    ->searchable(),

                                Select::make('nip')
                                    ->label('Petugas')
                                    ->options(\App\Models\Petugas::pluck('nama', 'nip'))
                                    ->searchable(),

                                Radio::make('status')
                                    ->label('Tipe')
                                    ->options([
                                        'Ralan' => 'Rawat Jalan',
                                        'Ranap' => 'Rawat Inap',
                                    ])
                                    ->default('Ralan')
                                    ->inline(),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tgl_perawatan')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('regPeriksa.pasien.nm_pasien')
                    ->label('Pasien')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('databarang.nama_brng')
                    ->label('Obat')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('jml')
                    ->label('Qty')
                    ->numeric(0),

                TextColumn::make('instruksi_lengkap')
                    ->label('Instruksi')
                    ->limit(40)
                    ->wrap(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('dokter.nm_dokter')
                    ->label('Dokter')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query) => $query->today()),

                Filter::make('recent')
                    ->label('7 Hari Terakhir')
                    ->query(fn (Builder $query) => $query->recent(7)),

                SelectFilter::make('status')
                    ->options([
                        'Ralan' => 'Rawat Jalan',
                        'Ranap' => 'Rawat Inap',
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
            'index' => Pages\ListDetailPemberianObats::route('/'),
            'create' => Pages\CreateDetailPemberianObat::route('/create'),
            'view' => Pages\ViewDetailPemberianObat::route('/{record}'),
            'edit' => Pages\EditDetailPemberianObat::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $today = static::getModel()::today()->count();
        return $today > 0 ? (string) $today : null;
    }
}
