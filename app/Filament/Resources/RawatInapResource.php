<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RawatInapResource\Pages;
use App\Filament\Resources\RawatInapResource\RelationManagers;
use App\Models\RawatInap;
use App\Models\reg_periksa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\AppliesUserFilter; // 🔹 Tambahkan ini

class RawatInapResource extends Resource
{
    use AppliesUserFilter; // 🔹 Pastikan ini ada
    protected static ?string $model = reg_periksa::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationBadge(): ?string
    {
        // Ambil query utama dari model
        $query = static::getEloquentQuery();

        // Terapkan filter yang sama seperti yang digunakan di table()
        $query = (new static())->applyFiltersToQuery($query);

        // Kembalikan jumlah data yang tampil setelah difilter
        return $query->count();
    }


    protected static ?string $navigationGroup = 'ERM';

    // protected static ?int $navigationSort = 0;

    // Label jamak, ganti dengan singular jika perlu
    protected static ?string $pluralLabel = 'Rawat Inap'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = 'Rawat Inap';

    // title menu akan berubah
    protected static ?string $navigationLabel = 'Rawat Inap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->query(static::applyEloquentQuery(reg_periksa::query(), 'reg_periksa'))
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListRawatInaps::route('/'),
            'create' => Pages\CreateRawatInap::route('/create'),
            'edit' => Pages\EditRawatInap::route('/{record}/edit'),
        ];
    }
}
