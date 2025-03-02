<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BerkasPegawaiResource\Pages;
use App\Filament\Resources\BerkasPegawaiResource\RelationManagers;
use App\Models\berkas_pegawai;
use App\Models\master_berkas_pegawai;
use App\Models\Pegawai;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Filament\Panel;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Visibility;

class BerkasPegawaiResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = berkas_pegawai::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function getNavigationBadge(): ?string
        {
            return static::getModel()::count();
        }

    protected static ?string $navigationGroup = 'SDM';

    // Label jamak, ganti dengan singular jika perlu
    protected static ?string $pluralLabel = ' Berkas Pegawai'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = ' Berkas Pegawai';

    // title menu akan berubah
    protected static ?string $navigationLabel = ' Berkas Pegawai';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('nik')
                ->label('NIK Pegawai')
                ->placeholder('Pilih NIK')
                ->options(
                    Pegawai::when(
                        auth()->user()->can('view_master::berkas::pegawai'), 
                        fn ($query) => $query, 
                        fn ($query) => $query->where('nik', auth()->user()->username) 
                    )
                    ->pluck('nama', 'nik')
                    ->map(fn($nama, $nik) => "$nik - $nama")
                )
                ->searchable()
                ->dehydrated() // Pastikan nilai disertakan saat submit
                ->required()
                ->default(auth()->user()->username) // Set default sesuai username user
                ->afterStateHydrated(fn ($state, callable $set, $record) => 
                    $set('nik', $record?->nik ?? auth()->user()->username)
                    ),
            
                TextInput::make('nama')
                    ->label('Nama Pegawai')
                    ->disabled() // Nama tetap tidak bisa diubah
                    ->afterStateHydrated(fn ($state, callable $set, $record) => 
                        $set('nama', $record?->pegawai?->nama ?? Pegawai::where('nik', auth()->user()->username)->value('nama'))
                        ),                

                DatePicker::make('tgl_uploud')
                    ->label('Tanggal Upload')
                    ->default(Carbon::now()) // Set default tanggal hari ini
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d') // Format penyimpanan ke database
                    ->readOnly() // Tidak bisa diupdate saat edit
                    ->required(),

                Select::make('kategori')
                    ->label('Kategori')
                    ->placeholder('Pilih ini Dulu')
                    ->options(
                        master_berkas_pegawai::all()->pluck('kategori', 'kategori')  
                    )
                    ->live() // Memungkinkan update data secara real-time
                    ->required(),
                Select::make('kode_berkas')
                    ->label('Kode Berkas')
                    ->options(function (callable $get) {
                        $kategori = $get('kategori'); // Ambil kategori yang dipilih
                        if (!$kategori) {
                            return [];
                        }

                        return master_berkas_pegawai::where('kategori', $kategori)
                            ->orderBy('kode', 'asc')
                            ->pluck('nama_berkas', 'kode')
                            ->map(fn($nama_berkas, $kode) => "$kode - $nama_berkas");
                    })
                    ->required()
                    ->searchable()
                    ->live(),
                    FileUpload::make('berkas')
                    ->label('Berkas')
                    ->image()
                    ->visibility('private')
                    ->disk('pegawai') // Gunakan disk pegawai
                    ->directory('pages/berkaspegawai/photo') // Direktori penyimpanan
                    ->getUploadedFileNameForStorageUsing(fn ($file) => $file->hashName()) // Simpan dengan nama unik
                    ->deleteUploadedFileUsing(fn ($record) => Storage::disk('pegawai')->delete($record->berkas)) // Hapus otomatis
                    ->required(),
                
                                             
                Checkbox::make('set_tgl_berakhir')
                    ->label('Aktifkan Tanggal Berakhir Jika Berkas Ada Batas Waktu')
                    ->reactive(), // Agar langsung merespons saat dicentang

                DatePicker::make('tgl_berakhir')
                    ->label('Tanggal Berakhir')
                    ->placeholder('Pilih Tanggal')
                    ->native(false)
                    ->displayFormat('d/m/Y') // Format tampilan
                    ->visible(fn (callable $get) => $get('set_tgl_berakhir')) // Muncul hanya jika dicentang
                    ->required(fn (callable $get) => $get('set_tgl_berakhir')), // Wajib diisi jika dicentang
            ]);
    }

    public static function updatePegawaiData($nik, callable $set)
    {
        if (!$nik) {
            return;
        }

        $pegawai = Pegawai::where('nik', $nik)->first();
        if ($pegawai) {
            // Konversi jenis kelamin dari Pegawai ke format ENUM Dokter
            $jk = strtolower($pegawai->jk) === 'wanita' ? 'P' : 'L';
            $set('nama', $pegawai->nama);
            $set('jk', $jk);
            $set('tmp_lahir', $pegawai->tmp_lahir);
            $set('tgl_lahir', $pegawai->tgl_lahir);
            $set('alamat', $pegawai->alamat);
        }
    }

    

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Belum ada data')
            ->query(static::applyEloquentQuery(berkas_pegawai::query())) // Terapkan filter berdasarkan NIK
            ->columns([
                    Tables\Columns\TextColumn::make('nik')
                    ->sortable()
                    ->searchable()
                    ->label('NIK'),
                    Tables\Columns\TextColumn::make('tgl_uploud')
                    ->label('Tanggal Upload')
                    ->date(),
                    Tables\Columns\TextColumn::make('master_berkas_pegawai.kategori')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),
                    Tables\Columns\TextColumn::make('master_berkas_pegawai.nama_berkas')
                    ->label('Nama Berkas')
                    ->sortable()
                    ->searchable(),
                    Tables\Columns\TextColumn::make('berkas')
                    ->label('Download Berkas')
                    ->formatStateUsing(fn ($record) => $record->berkas ? '🔗 Download' : '-')
                    ->url(fn ($record) => route('filament.resources.berkas-pegawai.download', [
                        'record' => $record->nik,
                        'filename' => basename($record->berkas),
                    ]), true)
                    ->openUrlInNewTab(),
                    Tables\Columns\ImageColumn::make('berkas')
                        ->sortable()
                        ->getStateUsing(fn ($record) => $record->url) // Ambil dari model
                        ->label('Berkas Pegawai'),
                    


            ])
            ->defaultSort('tgl_uploud', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('download')
                    ->label('Berkas')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => route('filament.resources.berkas-pegawai.download', [
                        'record' => $record->nik,
                        'filename' => basename($record->berkas),
                    ]), true)
                    ->openUrlInNewTab()
                    ->visible(fn () => auth()->user()->can('download_berkas::pegawai')),
                DeleteAction::make()
                ->before(function ($record) {
                    return $record->delete();
                }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
 * Filter data hanya untuk user yang login, kecuali memiliki izin melihat semua.
 */
public static function applyEloquentQuery(Builder $query): Builder
{
    return $query->when(
        auth()->check() && !auth()->user()->can('view_master::berkas::pegawai'), // Jika user tidak punya izin lihat semua
        fn ($query) => $query->where('nik', auth()->user()->username) // Filter hanya NIK yang sama
    );
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
            'index' => Pages\ListBerkasPegawais::route('/'),
            'create' => Pages\CreateBerkasPegawai::route('/create'),
            'edit' => Pages\EditBerkasPegawai::route('/{record}/edit'),
        ];
    }

    public static function routes(Panel $panel): void
{
    parent::routes($panel);

    $panel->routes(function ($router) {
        $router->get('/berkas-pegawai/download/{record}/{filename}', function ($record, $filename) {
            $filePath = "pages/pegawai/photo/$filename";

            // Cek apakah file ada
            if (!Storage::disk('pegawai')->exists($filePath)) {
                abort(404, 'File tidak ditemukan');
            }

            // Kembalikan file sebagai response
            return response()->file(Storage::disk('pegawai')->path($filePath), [
                'Content-Disposition' => 'inline',
            ]);
        })->name('filament.resources.berkas-pegawai.download');
    });
}

public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
            'download'
        ];
    }

}
