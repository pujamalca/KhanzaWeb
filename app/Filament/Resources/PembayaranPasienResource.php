<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranPasienResource\Pages;
use App\Models\PembayaranPasien;
use App\Models\BillingPasien;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
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

class PembayaranPasienResource extends Resource
{
    protected static ?string $model = PembayaranPasien::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static ?string $pluralLabel = 'Pembayaran';

    protected static ?string $label = 'Pembayaran';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Billing')
                    ->schema([
                        Select::make('billing_id')
                            ->label('Billing - Pasien')
                            ->options(function ($get) {
                                // If billing_id provided in query, use it
                                $billingId = request()->query('billing_id');
                                if ($billingId) {
                                    $billing = BillingPasien::find($billingId);
                                    if ($billing) {
                                        $pasienName = $billing->regPeriksa->pasien->nm_pasien ?? 'Unknown';
                                        return [$billing->id => "#{$billing->id} - {$pasienName} (Sisa: Rp " . number_format($billing->sisa_tagihan, 0, ',', '.') . ")"];
                                    }
                                }

                                // Otherwise show unpaid billings
                                return BillingPasien::with('regPeriksa.pasien')
                                    ->where('status_bayar', '!=', 'Lunas')
                                    ->latest('tgl_billing')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(function ($billing) {
                                        $pasienName = $billing->regPeriksa->pasien->nm_pasien ?? 'Unknown';
                                        return [
                                            $billing->id => "#{$billing->id} - {$pasienName} (Sisa: Rp " . number_format($billing->sisa_tagihan, 0, ',', '.') . ")"
                                        ];
                                    });
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($set, $state) {
                                if ($state) {
                                    $billing = BillingPasien::find($state);
                                    if ($billing) {
                                        $set('no_rawat', $billing->no_rawat);
                                        $set('sisa_tagihan_display', $billing->sisa_tagihan);
                                    }
                                }
                            })
                            ->disabled(fn ($context) => $context === 'edit')
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('tgl_bayar')
                                    ->label('Tanggal Pembayaran')
                                    ->required()
                                    ->default(now())
                                    ->native(false),

                                TimePicker::make('jam_bayar')
                                    ->label('Jam')
                                    ->required()
                                    ->default(now()->format('H:i:s'))
                                    ->seconds(true),
                            ]),

                        Placeholder::make('sisa_tagihan_display')
                            ->label('Sisa Tagihan')
                            ->content(function ($get) {
                                $sisa = $get('sisa_tagihan_display') ?? 0;
                                return 'Rp ' . number_format($sisa, 0, ',', '.');
                            })
                            ->hidden(fn ($get) => !$get('billing_id')),
                    ])
                    ->columns(2),

                Section::make('Detail Pembayaran')
                    ->schema([
                        TextInput::make('jumlah_bayar')
                            ->label('Jumlah Bayar')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->helperText('Masukkan jumlah yang dibayarkan')
                            ->reactive(),

                        Select::make('metode_bayar')
                            ->label('Metode Pembayaran')
                            ->options([
                                'Tunai' => '💵 Tunai',
                                'Transfer' => '🏦 Transfer Bank',
                                'Kartu Kredit' => '💳 Kartu Kredit',
                                'Kartu Debit' => '💳 Kartu Debit',
                                'BPJS' => '🏥 BPJS',
                                'Asuransi' => '🛡️ Asuransi',
                                'Lainnya' => '📝 Lainnya',
                            ])
                            ->required()
                            ->default('Tunai')
                            ->reactive(),

                        TextInput::make('no_referensi')
                            ->label('No. Referensi/Transaksi')
                            ->placeholder('Nomor transaksi, nomor approval, dll')
                            ->helperText('Optional - untuk Transfer, Kartu, dll')
                            ->visible(fn ($get) => in_array($get('metode_bayar'), ['Transfer', 'Kartu Kredit', 'Kartu Debit'])),

                        Select::make('diterima_oleh')
                            ->label('Diterima Oleh')
                            ->options(\App\Models\Petugas::pluck('nama', 'nip'))
                            ->searchable()
                            ->helperText('Kasir/Petugas yang menerima'),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(2)
                            ->placeholder('Catatan tambahan...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Informasi')
                    ->schema([
                        Placeholder::make('sisa_setelah_bayar')
                            ->label('Sisa Setelah Pembayaran Ini')
                            ->content(function ($get) {
                                $sisaTagihan = $get('sisa_tagihan_display') ?? 0;
                                $jumlahBayar = $get('jumlah_bayar') ?? 0;
                                $sisaSetelah = max(0, $sisaTagihan - $jumlahBayar);

                                $status = $sisaSetelah > 0 ? '⚠️ Masih ada sisa' : '✓ Lunas';

                                return 'Rp ' . number_format($sisaSetelah, 0, ',', '.') . " ({$status})";
                            })
                            ->hidden(fn ($get) => !$get('billing_id') || !$get('jumlah_bayar')),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tgl_bayar')
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

                TextColumn::make('jumlah_bayar')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->weight('bold'),

                BadgeColumn::make('metode_bayar')
                    ->label('Metode')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'Tunai' => '💵 Tunai',
                        'Transfer' => '🏦 Transfer',
                        'Kartu Kredit' => '💳 Kredit',
                        'Kartu Debit' => '💳 Debit',
                        'BPJS' => '🏥 BPJS',
                        'Asuransi' => '🛡️ Asuransi',
                        default => $state
                    }),

                TextColumn::make('no_referensi')
                    ->label('Ref')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('petugas.nama')
                    ->label('Kasir')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('formatted_date_time')
                    ->label('Waktu')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('metode_bayar')
                    ->label('Metode')
                    ->options([
                        'Tunai' => 'Tunai',
                        'Transfer' => 'Transfer',
                        'Kartu Kredit' => 'Kartu Kredit',
                        'Kartu Debit' => 'Kartu Debit',
                        'BPJS' => 'BPJS',
                        'Asuransi' => 'Asuransi',
                        'Lainnya' => 'Lainnya',
                    ]),

                Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn (Builder $query) => $query->today()),

                Filter::make('recent')
                    ->label('7 Hari Terakhir')
                    ->query(fn (Builder $query) => $query->recent(7)),

                SelectFilter::make('petugas')
                    ->label('Filter Kasir')
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
            ->defaultSort('tgl_bayar', 'desc');
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
            'index' => Pages\ListPembayaranPasiens::route('/'),
            'create' => Pages\CreatePembayaranPasien::route('/create'),
            'view' => Pages\ViewPembayaranPasien::route('/{record}'),
            'edit' => Pages\EditPembayaranPasien::route('/{record}/edit'),
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
