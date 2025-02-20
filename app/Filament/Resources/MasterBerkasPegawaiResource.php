<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MasterBerkasPegawaiResource\Pages;
use App\Filament\Resources\MasterBerkasPegawaiResource\RelationManagers;
use App\Models\master_berkas_pegawai;
use App\Models\MasterBerkasPegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MasterBerkasPegawaiResource extends Resource
{
    protected static ?string $model = master_berkas_pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'SDM';

    // Label jamak, ganti dengan singular jika perlu
    protected static ?string $pluralLabel = 'Master Berkas Pegawai'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = 'Master Berkas Pegawai';

    // title menu akan berubah
    protected static ?string $navigationLabel = 'Master Berkas Pegawai';

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
            ->columns([
                //
                Tables\Columns\TextColumn::make('kode')
                    ->searchable()
                    ->sortable()
                    ->label('Kode'),
                Tables\Columns\TextColumn::make('kategori')
                    ->sortable()
                    ->searchable()
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('nama_berkas')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Berkas'),
                Tables\Columns\TextColumn::make('no_urut')
                    ->sortable()
                    ->searchable()
                    ->label('No Urut'),
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
            'index' => Pages\ListMasterBerkasPegawais::route('/'),
            'create' => Pages\CreateMasterBerkasPegawai::route('/create'),
            'edit' => Pages\EditMasterBerkasPegawai::route('/{record}/edit'),
        ];
    }
}
