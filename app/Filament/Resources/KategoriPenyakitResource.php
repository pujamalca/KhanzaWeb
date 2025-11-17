<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriPenyakitResource\Pages;
use App\Models\KategoriPenyakit;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class KategoriPenyakitResource extends Resource
{
    protected static ?string $model = KategoriPenyakit::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationLabel = 'Kategori Penyakit';

    protected static ?string $pluralLabel = 'Kategori Penyakit';

    protected static ?string $label = 'Kategori Penyakit';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('kd_ktg')
                            ->label('Kode Kategori')
                            ->required()
                            ->maxLength(5)
                            ->placeholder('Contoh: INF01')
                            ->unique(ignoreRecord: true)
                            ->helperText('Maksimal 5 karakter')
                            ->columnSpan(1),

                        TextInput::make('nm_kategori')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(30)
                            ->placeholder('Contoh: Penyakit Infeksi')
                            ->columnSpan(1),

                        Textarea::make('ciri_umum')
                            ->label('Ciri Umum')
                            ->maxLength(200)
                            ->rows(3)
                            ->placeholder('Deskripsi ciri umum kategori penyakit...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kd_ktg')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode berhasil disalin')
                    ->weight('bold'),

                TextColumn::make('nm_kategori')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('ciri_umum')
                    ->label('Ciri Umum')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('penyakit_count')
                    ->label('Jumlah Penyakit')
                    ->counts('penyakit')
                    ->badge()
                    ->color('info')
                    ->sortable(),
            ])
            ->filters([
                //
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
            ->defaultSort('kd_ktg', 'asc');
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
            'index' => Pages\ListKategoriPenyakits::route('/'),
            'create' => Pages\CreateKategoriPenyakit::route('/create'),
            'edit' => Pages\EditKategoriPenyakit::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
