<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillingPasienResource\Pages;
use App\Models\BillingPasien;
use App\Models\reg_periksa;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Actions\Action;
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

class BillingPasienResource extends Resource
{
    protected static ?string $model = BillingPasien::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Billing & Tagihan';

    protected static ?string $pluralLabel = 'Billing & Tagihan';

    protected static ?string $label = 'Billing';

    protected static ?int $navigationSort = 8;

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
                                    ->whereDoesntHave('billing') // Only show patients without billing
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
                                    ->whereDoesntHave('billing')
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
                            ->disabled(fn ($context) => $context === 'edit')
                            ->columnSpanFull()
                            ->reactive()
                            ->afterStateUpdated(function ($set, $state, $context) {
                                if ($context === 'create' && $state) {
                                    $reg = reg_periksa::find($state);
                                    if ($reg) {
                                        // Auto-fill registration fee
                                        $set('biaya_registrasi', $reg->biaya_reg ?? 0);

                                        // Auto-calculate medication cost
                                        $biayaObat = $reg->detailPemberianObat()->sum('total');
                                        $set('biaya_obat', $biayaObat);

                                        // Set default examination fee
                                        $set('biaya_pemeriksaan', 50000);
                                    }
                                }
                            }),

                        DatePicker::make('tgl_billing')
                            ->label('Tanggal Billing')
                            ->required()
                            ->default(now())
                            ->native(false),
                    ]),

                Section::make('Rincian Biaya')
                    ->description('Breakdown of all costs')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('biaya_registrasi')
                                    ->label('Biaya Registrasi')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->helperText('From registration'),

                                TextInput::make('biaya_pemeriksaan')
                                    ->label('Biaya Pemeriksaan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(50000)
                                    ->reactive()
                                    ->helperText('Examination fee'),

                                TextInput::make('biaya_tindakan')
                                    ->label('Biaya Tindakan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->helperText('Procedure fees'),

                                TextInput::make('biaya_obat')
                                    ->label('Biaya Obat')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->helperText('Medication cost (auto-calculated)'),

                                TextInput::make('biaya_laboratorium')
                                    ->label('Biaya Laboratorium')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->helperText('Lab tests'),

                                TextInput::make('biaya_lainnya')
                                    ->label('Biaya Lainnya')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->helperText('Other charges'),
                            ]),

                        Placeholder::make('subtotal_display')
                            ->label('Subtotal')
                            ->content(function ($get) {
                                $subtotal = ($get('biaya_registrasi') ?? 0)
                                          + ($get('biaya_pemeriksaan') ?? 0)
                                          + ($get('biaya_tindakan') ?? 0)
                                          + ($get('biaya_obat') ?? 0)
                                          + ($get('biaya_laboratorium') ?? 0)
                                          + ($get('biaya_lainnya') ?? 0);

                                return 'Rp ' . number_format($subtotal, 0, ',', '.');
                            })
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('diskon')
                                    ->label('Diskon')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->helperText('Discount amount'),

                                TextInput::make('pajak')
                                    ->label('Pajak')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->reactive()
                                    ->helperText('Tax amount'),
                            ]),

                        Placeholder::make('total_display')
                            ->label('TOTAL TAGIHAN')
                            ->content(function ($get) {
                                $subtotal = ($get('biaya_registrasi') ?? 0)
                                          + ($get('biaya_pemeriksaan') ?? 0)
                                          + ($get('biaya_tindakan') ?? 0)
                                          + ($get('biaya_obat') ?? 0)
                                          + ($get('biaya_laboratorium') ?? 0)
                                          + ($get('biaya_lainnya') ?? 0);

                                $total = $subtotal - ($get('diskon') ?? 0) + ($get('pajak') ?? 0);

                                return 'Rp ' . number_format($total, 0, ',', '.');
                            })
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'text-2xl font-bold text-primary-600']),
                    ]),

                Section::make('Status Pembayaran')
                    ->schema([
                        Radio::make('status_bayar')
                            ->label('Status')
                            ->options([
                                'Belum Bayar' => 'Belum Bayar',
                                'Cicilan' => 'Cicilan/Sebagian',
                                'Lunas' => 'Lunas',
                            ])
                            ->default('Belum Bayar')
                            ->inline(),

                        Textarea::make('catatan')
                            ->label('Catatan')
                            ->rows(2)
                            ->placeholder('Catatan tambahan tentang billing...')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('regPeriksa.pasien.nm_pasien')
                    ->label('Pasien')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('tgl_billing')
                    ->label('Tgl Billing')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('total_biaya')
                    ->label('Total Tagihan')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('total_dibayar')
                    ->label('Dibayar')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('sisa_tagihan')
                    ->label('Sisa')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),

                BadgeColumn::make('status_bayar')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'Lunas' => 'success',
                        'Cicilan' => 'warning',
                        'Belum Bayar' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_percentage')
                    ->label('Progress')
                    ->suffix('%')
                    ->sortable(false)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_bayar')
                    ->label('Status')
                    ->options([
                        'Belum Bayar' => 'Belum Bayar',
                        'Cicilan' => 'Cicilan',
                        'Lunas' => 'Lunas',
                    ]),

                Filter::make('unpaid')
                    ->label('Belum Lunas')
                    ->query(fn (Builder $query) => $query->where('status_bayar', '!=', 'Lunas')),

                Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query) => $query->today()),

                Filter::make('overdue')
                    ->label('Jatuh Tempo (>30 hari)')
                    ->query(fn (Builder $query) => $query->overdue(30)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('addPayment')
                    ->label('Bayar')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->url(fn (BillingPasien $record) =>
                        route('filament.superadmin.resources.pembayaran-pasiens.create', ['billing_id' => $record->id])
                    )
                    ->visible(fn (BillingPasien $record) => $record->sisa_tagihan > 0),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('tgl_billing', 'desc');
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
            'index' => Pages\ListBillingPasiens::route('/'),
            'create' => Pages\CreateBillingPasien::route('/create'),
            'view' => Pages\ViewBillingPasien::route('/{record}'),
            'edit' => Pages\EditBillingPasien::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $unpaid = static::getModel()::where('status_bayar', '!=', 'Lunas')->count();
        return $unpaid > 0 ? (string) $unpaid : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $unpaid = static::getModel()::where('status_bayar', '!=', 'Lunas')->count();
        return $unpaid > 0 ? 'danger' : null;
    }
}
