<?php

namespace App\Filament\Resources;

use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\RawatJalanResource\Pages;
use App\Filament\Resources\RawatJalanResource\RelationManagers;
use App\Models\RawatJalan;
use App\Models\reg_periksa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\AppliesUserFilter; // 🔹 Tambahkan ini
use Filament\Actions\Action;
use Filament\Tables\Actions\Action as ActionsAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Carbon;

class RawatJalanResource extends Resource
{
    use AppliesUserFilter; // 🔹 Pastikan ini ada
    protected static ?string $model = reg_periksa::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

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
    protected static ?string $pluralLabel = 'Rawat Jalan'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = 'Rawat Jalan';

    // title menu akan berubah
    protected static ?string $navigationLabel = 'Rawat Jalan';

    public static function mutateFormDataBeforeCreate(array $data): array
        {
            // Pastikan jam_registrasi tidak null
            $datetime = isset($data['jam_registrasi']) ? Carbon::parse($data['jam_registrasi']) : now();
            $data['tgl_registrasi'] = $datetime->toDateString();
            $data['jam_reg'] = $datetime->format('H:i:s');
            unset($data['jam_registrasi']);

            // Pastikan no_reg tetap diisi jika tidak dikirim karena disabled
            if (empty($data['no_reg']) && !empty($data['auto_generate_no_reg'])) {
                $last = \App\Models\reg_periksa::whereDate('tgl_registrasi', $data['tgl_registrasi'] ?? now()->toDateString())
                    ->where('kd_poli', $data['kd_poli'] ?? 'IGDK')
                    ->max('no_reg');

                $data['no_reg'] = str_pad(((int)$last) + 1, 3, '0', STR_PAD_LEFT);
            }

            // no_rawat
            if (empty($data['no_rawat'])) {
                $countToday = \App\Models\reg_periksa::whereDate('tgl_registrasi', now()->toDateString())->count();
                $data['no_rawat'] = now()->format('Y/m/d') . '/' . str_pad($countToday + 1, 6, '0', STR_PAD_LEFT);
            }

                        // Cek status daftar (lama / baru)
            $data['stts_daftar'] = \App\Models\reg_periksa::where('no_rkm_medis', $data['no_rkm_medis'])->exists()
                ? 'Lama'
                : 'Baru';

            // Ambil dari poliklinik
            $poli = \App\Models\Poliklinik::where('kd_poli', $data['kd_poli'])->first();
            if ($poli) {
                $data['biaya_reg'] = $data['stts_daftar'] === 'Baru'
                    ? $poli->registrasi
                    : $poli->registrasilama;
            }

            // Ambil data dari pasien
            $pasien = \App\Models\Pasien::where('no_rkm_medis', $data['no_rkm_medis'])->first();
            if ($pasien) {
                $data['p_jawab'] = $pasien->namakeluarga ?? '-';
                $data['almt_pj'] = $pasien->alamat ?? '-';
                $data['hubunganpj'] = $pasien->keluarga ?? '-';
                $data['umurdaftar'] = $pasien->umur ?? '0';
                $data['sttsumur'] = $pasien->sttsumur ?? 'Th';
            }

            // Set default status lain
            $data['stts'] = 'Belum';
            $data['status_lanjut'] = 'Ralan';

            return $data;
        }

    
        public static function form(Form $form): Form
        {
            return $form
                ->schema([
                Forms\Components\Select::make('no_rkm_medis')
                    ->label('Nomor RM - Nama')
                    ->options(
                        \App\Models\Pasien::select('no_rkm_medis', 'nm_pasien')
                            ->limit(100)
                            ->get()
                            ->mapWithKeys(fn ($pasien) => [
                                $pasien->no_rkm_medis => "{$pasien->no_rkm_medis} - {$pasien->nm_pasien}"
                            ])
                    )
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) =>
                        \App\Models\Pasien::where('no_rkm_medis', 'like', "%{$search}%")
                            ->orWhere('nm_pasien', 'like', "%{$search}%")
                            ->limit(100)
                            ->get()
                            ->mapWithKeys(fn ($pasien) => [
                                $pasien->no_rkm_medis => "{$pasien->no_rkm_medis} - {$pasien->nm_pasien}"
                            ])
                    )
                    ->reactive()
                    ->afterStateUpdated(function ($set, $state) {
                        $pasien = \App\Models\Pasien::where('no_rkm_medis', $state)->first();
                        $set('p_jawab', $pasien->namakeluarga ?? '');
                        $set('almt_pj', $pasien->alamat ?? '');
                        $set('hubunganpj', $pasien->keluarga ?? '');
                
                        // 💡 Tambahkan ini untuk menentukan status daftar
                        $sudahPernah = \App\Models\reg_periksa::where('no_rkm_medis', $state)->exists();
                        $set('stts_daftar', $sudahPernah ? 'Lama' : 'Baru');
                         // **Tambahan: hitung dan set umur**
                        if ($pasien && $pasien->tgl_lahir) {
                            $birth = \Carbon\Carbon::parse($pasien->tgl_lahir);
                            $years = $birth->diffInYears(now());
                            if ($years >= 1) {
                                $set('umurdaftar', $years);
                                $set('sttsumur', 'Th');
                            } else if (($months = $birth->diffInMonths(now())) >= 1) {
                                $set('umurdaftar', $months);
                                $set('sttsumur', 'Bl');
                            } else {
                                $set('umurdaftar', $birth->diffInDays(now()));
                                $set('sttsumur', 'Hr');
                            }
                        }
                         // Cek status daftar (Lama/Baru secara umum)
                        $pernahDaftar = \App\Models\reg_periksa::where('no_rkm_medis', $state)->exists();
                        $set('stts_daftar', $pernahDaftar ? 'Lama' : 'Baru');
    
                        // Cek status poli (khusus ke poli yang sama, contoh: IGDK)
                        $pernahDiPoli = \App\Models\reg_periksa::where('no_rkm_medis', $state)
                            ->where('kd_poli', 'IGDK')
                            ->exists();
                        $set('status_poli', $pernahDiPoli ? 'Lama' : 'Baru');
                    })
                    ->required(),
                
    
    
                Forms\Components\Select::make('kd_poli')
                    ->label('Poliklinik')
                    ->options(\App\Models\Poliklinik::pluck('nm_poli', 'kd_poli'))
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($set, $state) {
                        if ($state) {
                            $last = \App\Models\reg_periksa::whereDate('tgl_registrasi', now()->toDateString())
                                ->where('kd_poli', $state)
                                ->max('no_reg');

                            $next = str_pad(((int)$last) + 1, 3, '0', STR_PAD_LEFT);
                            $set('no_reg', $next);
                        }
                    }),
                
    
                Forms\Components\Select::make('kd_dokter')
                    ->label('Dokter')
                    ->options(\App\Models\Dokter::pluck('nm_dokter', 'kd_dokter'))
                    ->searchable()
                    ->required(),
    
                Forms\Components\Select::make('kd_pj')
                    ->label('Cara Bayar')
                    ->options(\App\Models\Penjab::pluck('png_jawab', 'kd_pj'))
                    ->searchable()
                    ->required(),
                
                Forms\Components\TextInput::make('no_reg')
                    ->label('No. Reg.')
                    ->reactive()
                    ->default(function ($get) {
                        $kdPoli = $get('kd_poli');
                        if (!$kdPoli) return null;
                
                        $last = \App\Models\reg_periksa::whereDate('tgl_registrasi', now()->toDateString())
                            ->where('kd_poli', $kdPoli)
                            ->max('no_reg');
                
                        return str_pad(((int) $last) + 1, 3, '0', STR_PAD_LEFT);
                    })
                    ->required(),        
                
                
    
                Forms\Components\TextInput::make('no_rawat')
                    ->label('No. Rawat')
                    ->default(fn () => now()->format('Y/m/d') . '/' . str_pad(
                        (int) \App\Models\reg_periksa::whereDate('tgl_registrasi', now()->toDateString())->count() + 1,
                        6, '0', STR_PAD_LEFT
                    )) // Format YYYY/MM/DD/000XXX, berdasarkan jumlah rawat hari ini
                    ->required(),
    
                Forms\Components\DatePicker::make('tgl_registrasi')
                    ->label('Tanggal Registrasi')
                    ->default(now())
                    ->required(),
                
                Forms\Components\TimePicker::make('jam_reg')
                    ->label('Jam Registrasi')
                    ->seconds(true)
                    ->default(now())
                    ->required(),
                
                
                    
                Forms\Components\Hidden::make('p_jawab')
                    ->dehydrated(),
                Forms\Components\Hidden::make('almt_pj')
                    ->dehydrated(),
                Forms\Components\Hidden::make('hubunganpj')
                    ->dehydrated(),
                Forms\Components\Hidden::make('stts')
                    ->default('Belum')
                    ->dehydrated(),
                Forms\Components\Hidden::make('stts_daftar')
                    ->dehydrated(),
                Forms\Components\Hidden::make('status_lanjut')
                    ->default('Ralan')
                    ->dehydrated(),
                Forms\Components\Hidden::make('umurdaftar')
                    ->dehydrated(),
                Forms\Components\Hidden::make('sttsumur')
                    ->dehydrated(),
                Forms\Components\Hidden::make('status_bayar')
                    ->default('Belum Bayar')
                    ->dehydrated(),
                Forms\Components\Hidden::make('biaya_reg')
                    ->dehydrated(),
                
                ]);
        }

    public static function table(Table $table): Table
    {
        return $table
        ->query(static::applyEloquentQuery(reg_periksa::query(), 'reg_periksa'))
            ->columns([
                //
            TextColumn::make('no_reg')
                ->label('No Reg')
                ->sortable()
                ->searchable(),

            TextColumn::make('no_rkm_medis')
                ->label('No.RM')
                ->sortable()
                ->searchable(),

            TextColumn::make('no_rawat')
                ->label('No. Rawat')
                ->sortable()
                ->searchable(),

            TextColumn::make('pasien.nm_pasien')
                ->label('Nama Pasien')
                ->sortable()
                ->searchable(),

            TextColumn::make('tgl_registrasi')
                ->label('Waktu')
                ->sortable()
                ->formatStateUsing(fn ($state, $record) => \Carbon\Carbon::parse($state)->format('d-m-Y') . ' ' . $record->jam_reg),

            TextColumn::make('dokter.nm_dokter')
                ->label('Dokter')
                ->sortable()
                ->searchable(),

            TextColumn::make('poliklinik.nm_poli')
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

            TextColumn::make('penjab.nama_perusahaan')
                ->label('Kode PJ')
                ->sortable(),

            TextColumn::make('umurdaftar')
                ->label('Umur')
                ->sortable()
                ->formatStateUsing(fn ($state,$record) => "{$record->umurdaftar} {$record->sttsumur}"),

            TextColumn::make('status_bayar')
                ->label('Status Bayar')
                ->sortable(),

            TextColumn::make('status_poli')
                ->label('Status Poli')
                ->sortable(),
            ])
            ->filters([
                //
                DateRangeFilter::make('tgl_registrasi', 'Tanggal Registrasi'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                    ->url(fn (reg_periksa $record) => route(
                        'filament.superadmin.resources.ugds.edit',
                        ['record' => str_replace('/', '-', $record->no_rawat)] // Ganti "/" jadi "-"
                    )),

                    Tables\Actions\DeleteAction::make(),
                    Action::make('rawatInap')
                        ->label('Rawat Inap')
                        ->icon('heroicon-o-plus-circle') // tersedia default
                        ->color('success')
                        ->visible(fn ($record) => !$record->kamarInap) // hanya jika belum diranapkan
                        ->form([
                            Forms\Components\Select::make('kd_kamar')
                                ->label('Pilih Kamar')
                                ->relationship('kamar', 'nm_kamar')
                                ->searchable()
                                ->required(),
                            Forms\Components\TextInput::make('diagnosa_awal')
                                ->label('Diagnosa Awal')
                                ->required(),
                            Forms\Components\DateTimePicker::make('tgl_masuk')
                                ->label('Tanggal Masuk')
                                ->default(now())
                                ->required(),
                        ])
                        ->action(function (array $data, $record) {
                            \App\Models\kamar_inap::create([
                                'no_rawat' => $record->no_rawat,
                                'kd_kamar' => $data['kd_kamar'],
                                'trf_kamar' => \App\Models\Kamar::find($data['kd_kamar'])->trf_kamar ?? 0,
                                'diagnosa_awal' => $data['diagnosa_awal'],
                                'tgl_masuk' => $data['tgl_masuk'],
                                'jam_masuk' => now()->format('H:i:s'),
                                'stts_pulang' => '-', // default belum pulang
                                'stts_kamar' => 'ISI',
                                'ttl_biaya' => 0,
                            ]);
                        })
    ->requiresConfirmation()
                ])
                ->button()
                ->label('Menu'),
            ],position: ActionsPosition::BeforeColumns)
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
            'index' => Pages\ListRawatJalans::route('/'),
            'create' => Pages\CreateRawatJalan::route('/create'),
            'edit' => Pages\EditRawatJalan::route('/{record}/edit'),
        ];
    }
}
