<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenyakitResource\Pages;
use App\Models\Penyakit;
use App\Models\KategoriPenyakit;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class PenyakitResource extends Resource
{
    protected static ?string $model = Penyakit::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';


    protected static ?string $navigationLabel = 'Penyakit (ICD-10)';

    protected static ?string $pluralLabel = 'Penyakit';

    protected static ?string $label = 'Penyakit';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Card::make()
                    ->schema([
                        TextInput::make('kd_penyakit')
                            ->label('Kode ICD-10')
                            ->required()
                            ->maxLength(10)
                            ->placeholder('Contoh: A00.0')
                            ->unique(ignoreRecord: true)
                            ->columnSpan(1),

                        Select::make('kd_ktg')
                            ->label('Kategori')
                            ->options(KategoriPenyakit::pluck('nm_kategori', 'kd_ktg'))
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),

                        TextInput::make('nm_penyakit')
                            ->label('Nama Penyakit')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('ciri_ciri')
                            ->label('Ciri-ciri')
                            ->maxLength(200)
                            ->rows(3)
                            ->columnSpanFull(),

                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->maxLength(200)
                            ->rows(3)
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'Ranap' => 'Rawat Inap',
                                'Ralan' => 'Rawat Jalan',
                                'Ranap Dan Ralan' => 'Rawat Inap & Jalan',
                            ])
                            ->default('Ranap Dan Ralan')
                            ->required()
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kd_penyakit')
                    ->label('Kode ICD-10')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Kode berhasil disalin')
                    ->weight('bold'),

                TextColumn::make('nm_penyakit')
                    ->label('Nama Penyakit')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(50),

                TextColumn::make('kategori.nm_kategori')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Ranap' => 'success',
                        'Ralan' => 'warning',
                        'Ranap Dan Ralan' => 'primary',
                        default => 'gray',
                    }),

                TextColumn::make('ciri_ciri')
                    ->label('Ciri-ciri')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('kd_ktg')
                    ->label('Kategori')
                    ->options(KategoriPenyakit::pluck('nm_kategori', 'kd_ktg'))
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'Ranap' => 'Rawat Inap',
                        'Ralan' => 'Rawat Jalan',
                        'Ranap Dan Ralan' => 'Rawat Inap & Jalan',
                    ]),
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
            ->defaultSort('kd_penyakit', 'asc');
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
            'index' => Pages\ListPenyakits::route('/'),
            'create' => Pages\CreatePenyakit::route('/create'),
            'edit' => Pages\EditPenyakit::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
