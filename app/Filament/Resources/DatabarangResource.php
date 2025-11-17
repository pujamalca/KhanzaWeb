<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DatabarangResource\Pages;
use App\Models\Databarang;
use App\Models\Kodesatuan;
use App\Models\Jenis;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class DatabarangResource extends Resource
{
    protected static ?string $model = Databarang::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-cube';


    protected static ?string $navigationLabel = 'Obat & Alkes';

    protected static ?string $pluralLabel = 'Obat & Alat Kesehatan';

    protected static ?string $label = 'Obat/Alkes';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Informasi Dasar')
                    ->schema([
                        TextInput::make('kode_brng')
                            ->label('Kode Barang')
                            ->required()
                            ->maxLength(15)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: OBT001')
                            ->columnSpan(1),

                        TextInput::make('nama_brng')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(2),

                        Select::make('kd_jenis')
                            ->label('Jenis')
                            ->options(Jenis::pluck('nama', 'kd_jenis'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Select::make('kd_sat')
                            ->label('Satuan')
                            ->options(Kodesatuan::pluck('satuan', 'kode_sat'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('letak')
                            ->label('Letak/Lokasi')
                            ->maxLength(50)
                            ->placeholder('Contoh: Rak A1')
                            ->columnSpan(1),
                    ])
                    ->columns(3),

                Section::make('Harga & Stok')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('h_beli')
                                    ->label('Harga Beli')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->required(),

                                TextInput::make('dasar')
                                    ->label('Harga Dasar')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),

                                TextInput::make('ralan')
                                    ->label('Harga Rawat Jalan')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->required(),
                            ]),

                        Grid::make(4)
                            ->schema([
                                TextInput::make('kelas1')
                                    ->label('Harga Kelas 1')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),

                                TextInput::make('kelas2')
                                    ->label('Harga Kelas 2')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),

                                TextInput::make('kelas3')
                                    ->label('Harga Kelas 3')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),

                                TextInput::make('utama')
                                    ->label('Harga Utama')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),
                            ]),

                        Grid::make(4)
                            ->schema([
                                TextInput::make('vip')
                                    ->label('Harga VIP')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),

                                TextInput::make('vvip')
                                    ->label('Harga VVIP')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),

                                TextInput::make('beliluar')
                                    ->label('Harga Beli Luar')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),

                                TextInput::make('jualbebas')
                                    ->label('Harga Jual Bebas')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),
                            ]),
                    ]),

                Section::make('Stok & Inventory')
                    ->schema([
                        TextInput::make('stok')
                            ->label('Stok Saat Ini')
                            ->numeric()
                            ->default(0)
                            ->suffix(fn ($get) => Kodesatuan::where('kode_sat', $get('kd_sat'))->value('satuan') ?? '')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('stok_minimum')
                            ->label('Stok Minimum')
                            ->numeric()
                            ->default(10)
                            ->helperText('Alert akan muncul jika stok di bawah nilai ini')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('kapasitas')
                            ->label('Kapasitas')
                            ->numeric()
                            ->default(1)
                            ->columnSpan(1),

                        TextInput::make('isi')
                            ->label('Isi')
                            ->numeric()
                            ->default(1)
                            ->helperText('Jumlah per kemasan')
                            ->columnSpan(1),

                        DatePicker::make('expire')
                            ->label('Tanggal Kadaluwarsa')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->columnSpan(1),

                        Toggle::make('status')
                            ->label('Status Aktif')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true)
                            ->inline(false)
                            ->formatStateUsing(fn ($state) => $state === '1')
                            ->dehydrateStateUsing(fn ($state) => $state ? '1' : '0')
                            ->columnSpan(1),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_brng')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('nama_brng')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(40),

                TextColumn::make('jenis.nama')
                    ->label('Jenis')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('satuan.satuan')
                    ->label('Satuan')
                    ->sortable(),

                TextColumn::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($record) => $record->isLowStock() ? 'danger' : 'success')
                    ->weight(fn ($record) => $record->isLowStock() ? 'bold' : 'normal')
                    ->icon(fn ($record) => $record->isLowStock() ? 'heroicon-o-exclamation-triangle' : null),

                TextColumn::make('stok_minimum')
                    ->label('Min')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('h_beli')
                    ->label('Harga Beli')
                    ->money('IDR')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ralan')
                    ->label('Harga Jual')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('expire')
                    ->label('Kadaluwarsa')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : ($record->isExpiringSoon(30) ? 'warning' : 'gray'))
                    ->icon(fn ($record) => $record->isExpired() ? 'heroicon-o-x-circle' : ($record->isExpiringSoon(30) ? 'heroicon-o-exclamation-triangle' : null)),

                IconColumn::make('status')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->getStateUsing(fn ($record) => $record->status === '1'),

                TextColumn::make('letak')
                    ->label('Letak')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kd_jenis')
                    ->label('Jenis')
                    ->options(Jenis::pluck('nama', 'kd_jenis'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ]),

                Filter::make('low_stock')
                    ->label('Stok Rendah')
                    ->query(fn (Builder $query): Builder => $query->lowStock())
                    ->toggle(),

                Filter::make('expiring_soon')
                    ->label('Hampir Kadaluwarsa')
                    ->query(fn (Builder $query): Builder => $query->expiringSoon(30))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama_brng', 'asc');
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
            'index' => Pages\ListDatabarangs::route('/'),
            'create' => Pages\CreateDatabarang::route('/create'),
            'edit' => Pages\EditDatabarang::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $lowStock = static::getModel()::lowStock()->count();
        return $lowStock > 0 ? (string) $lowStock : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $lowStock = static::getModel()::lowStock()->count();
        return $lowStock > 0 ? 'danger' : null;
    }
}
