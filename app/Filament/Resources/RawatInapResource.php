<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RawatInapResource\Pages;
use App\Filament\Resources\RawatInapResource\RelationManagers;
use App\Models\ranap_gabung;
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
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\DB;

class RawatInapResource extends Resource
{
    use AppliesUserFilter; // 🔹 Pastikan ini ada
    protected static ?string $model = reg_periksa::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $utama = reg_periksa::query()
            ->leftJoin('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->where('kamar_inap.stts_pulang', '-')
            ->select([
                'reg_periksa.no_rawat as no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.status_lanjut',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.jam_keluar',
                DB::raw('NULL as bayi')
            ]);
    
        $bayi = reg_periksa::query()
            ->join('ranap_gabung', 'ranap_gabung.no_rawat2', '=', 'reg_periksa.no_rawat')
            ->leftJoin('kamar_inap', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->where('kamar_inap.stts_pulang', '-')
            ->select([
                'reg_periksa.no_rawat as no_rawat',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_pj',
                'reg_periksa.stts',
                'reg_periksa.status_lanjut',
                'kamar_inap.tgl_masuk',
                'kamar_inap.jam_masuk',
                'kamar_inap.tgl_keluar',
                'kamar_inap.jam_keluar',
                'ranap_gabung.no_rawat as bayi'
            ]);
    
        $union = $utama->unionAll($bayi);
    
        return reg_periksa::query()
            ->fromSub($union, 'rawat_inap')
            ->orderBy('no_rawat'); // ini pakai alias kolom yg dikenali
    }
    
    protected static ?string $recordTitleAttribute = 'no_rawat'; // untuk label
    public static function getRecordRouteKeyName(): string
    {
        return 'no_rawat'; // penting untuk URL /edit
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->columns([
                TextColumn::make('no_rkm_medis')
                ->label('No. RM')
                ->sortable()
                ->searchable(),

            TextColumn::make('no_rawat')
                ->label('No. Rawat')
                ->sortable()
                ->searchable(),

            TextColumn::make('bayi')
                ->label('Bayi Gabung')
                ->formatStateUsing(fn ($state) => $state ?? '-'),

            TextColumn::make('nm_pasien')
                ->label('Nama Pasien')
                ->sortable()
                ->searchable(),

            TextColumn::make('tgl_masuk')
                ->label('Tanggal Masuk')
                ->sortable()
                ->formatStateUsing(fn ($state, $record) => $state && $record->jam_masuk ? \Carbon\Carbon::parse($state . ' ' . $record->jam_masuk)->format('d-m-Y H:i:s') : '-'),

            TextColumn::make('tgl_keluar')
                ->label('Tanggal Pulang')
                ->sortable()
                ->formatStateUsing(fn ($state, $record) => $state && $record->jam_keluar && $state !== '0000-00-00' ? \Carbon\Carbon::parse($state . ' ' . $record->jam_keluar)->format('d-m-Y H:i:s') : '-'),

            TextColumn::make('nm_dokter')
                ->label('Dokter')
                ->sortable()
                ->searchable(),

            TextColumn::make('nm_poli')
                ->label('Poli')
                ->sortable()
                ->searchable(),

            TextColumn::make('p_jawab')
                ->label('PJ')
                ->sortable()
                ->searchable(),

            TextColumn::make('almt_pj')
                ->label('Alamat PJ')
                ->sortable(),

            TextColumn::make('hubunganpj')
                ->label('Hubungan PJ')
                ->sortable(),

            TextColumn::make('stts')
                ->label('Status')
                ->sortable(),

            TextColumn::make('stts_daftar')
                ->label('Status Daftar')
                ->sortable(),

            TextColumn::make('status_lanjut')
                ->label('Status Lanjut')
                ->sortable(),

            TextColumn::make('kd_pj')
                ->label('Kode PJ')
                ->sortable(),

            TextColumn::make('umurdaftar')
                ->label('Umur')
                ->sortable()
                ->formatStateUsing(fn ($state, $record) => "{$record->umurdaftar} {$record->sttsumur}"),

            TextColumn::make('status_bayar')
                ->label('Status Bayar')
                ->sortable(),

            TextColumn::make('status_poli')
                ->label('Status Poli')
                ->sortable(),
            ])
            ->filters([          
            
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
