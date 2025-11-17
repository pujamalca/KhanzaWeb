<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KodesatuanResource\Pages;
use App\Models\Kodesatuan;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class KodesatuanResource extends Resource
{
    protected static ?string $model = Kodesatuan::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Satuan';

    protected static ?string $pluralLabel = 'Satuan';

    protected static ?string $label = 'Satuan';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('kode_sat')
                            ->label('Kode Satuan')
                            ->required()
                            ->maxLength(4)
                            ->placeholder('Contoh: TAB')
                            ->unique(ignoreRecord: true)
                            ->helperText('Maksimal 4 karakter')
                            ->columnSpan(1),

                        TextInput::make('satuan')
                            ->label('Nama Satuan')
                            ->required()
                            ->maxLength(30)
                            ->placeholder('Contoh: Tablet')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode_sat')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode berhasil disalin')
                    ->weight('bold'),

                TextColumn::make('satuan')
                    ->label('Nama Satuan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('databarang_count')
                    ->label('Jumlah Item')
                    ->counts('databarang')
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
            ->defaultSort('kode_sat', 'asc');
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
            'index' => Pages\ListKodesatuans::route('/'),
            'create' => Pages\CreateKodesatuan::route('/create'),
            'edit' => Pages\EditKodesatuan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
