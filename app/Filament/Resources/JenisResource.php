<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisResource\Pages;
use App\Models\Jenis;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class JenisResource extends Resource
{
    protected static ?string $model = Jenis::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Jenis Barang';

    protected static ?string $pluralLabel = 'Jenis Barang';

    protected static ?string $label = 'Jenis Barang';

    protected static ?int $navigationSort = 40;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('kd_jenis')
                            ->label('Kode Jenis')
                            ->required()
                            ->maxLength(4)
                            ->placeholder('Contoh: OBAT')
                            ->unique(ignoreRecord: true)
                            ->helperText('Maksimal 4 karakter')
                            ->columnSpan(1),

                        TextInput::make('nama')
                            ->label('Nama Jenis')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('Contoh: Obat')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kd_jenis')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode berhasil disalin')
                    ->weight('bold'),

                TextColumn::make('nama')
                    ->label('Nama Jenis')
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
            ->defaultSort('kd_jenis', 'asc');
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
            'index' => Pages\ListJenis::route('/'),
            'create' => Pages\CreateJenis::route('/create'),
            'edit' => Pages\EditJenis::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
