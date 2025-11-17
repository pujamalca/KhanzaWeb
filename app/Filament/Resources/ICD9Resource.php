<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ICD9Resource\Pages;
use App\Models\ICD9;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class ICD9Resource extends Resource
{
    protected static ?string $model = ICD9::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Prosedur (ICD-9)';

    protected static ?string $pluralLabel = 'Prosedur ICD-9';

    protected static ?string $label = 'Prosedur ICD-9';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('kode')
                            ->label('Kode ICD-9')
                            ->required()
                            ->maxLength(8)
                            ->placeholder('Contoh: 01.01')
                            ->unique(ignoreRecord: true)
                            ->helperText('Maksimal 8 karakter')
                            ->columnSpan(1),

                        TextInput::make('deskripsi_pendek')
                            ->label('Deskripsi Pendek')
                            ->required()
                            ->maxLength(40)
                            ->placeholder('Deskripsi singkat prosedur')
                            ->columnSpan(1),

                        Textarea::make('deskripsi_panjang')
                            ->label('Deskripsi Lengkap')
                            ->required()
                            ->maxLength(250)
                            ->rows(3)
                            ->placeholder('Deskripsi lengkap prosedur medis...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')
                    ->label('Kode ICD-9')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode berhasil disalin')
                    ->weight('bold'),

                TextColumn::make('deskripsi_pendek')
                    ->label('Deskripsi Pendek')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                TextColumn::make('deskripsi_panjang')
                    ->label('Deskripsi Lengkap')
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->defaultSort('kode', 'asc');
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
            'index' => Pages\ListICD9s::route('/'),
            'create' => Pages\CreateICD9::route('/create'),
            'edit' => Pages\EditICD9::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
