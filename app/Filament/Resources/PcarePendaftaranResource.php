<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PcarePendaftaranResource\Pages;
use App\Models\PcarePendaftaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class PcarePendaftaranResource extends Resource
{
    protected static ?string $model = PcarePendaftaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationLabel = 'Pendaftaran PCare';

    protected static ?string $navigationGroup = 'BPJS PCare';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pasien')
                    ->schema([
                        Forms\Components\TextInput::make('no_rawat')
                            ->label('No. Rawat')
                            ->required()
                            ->maxLength(17),

                        Forms\Components\TextInput::make('no_kartu')
                            ->label('No. Kartu BPJS')
                            ->required()
                            ->maxLength(25),

                        Forms\Components\TextInput::make('nm_pasien')
                            ->label('Nama Pasien')
                            ->required()
                            ->maxLength(100),

                        Forms\Components\Select::make('jkel')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required(),

                        Forms\Components\DatePicker::make('tgl_lahir')
                            ->label('Tanggal Lahir')
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('no_telp')
                            ->label('No. Telepon')
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),

                Forms\Components\Section::make('Data Pendaftaran PCare')
                    ->schema([
                        Forms\Components\TextInput::make('tgl_daftar')
                            ->label('Tanggal Daftar')
                            ->required()
                            ->default(date('Y-m-d')),

                        Forms\Components\TextInput::make('kd_provider')
                            ->label('Kode Provider')
                            ->required()
                            ->maxLength(20),

                        Forms\Components\TextInput::make('nm_provider')
                            ->label('Nama Provider')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('kd_poli')
                            ->label('Kode Poli PCare')
                            ->required()
                            ->maxLength(10),

                        Forms\Components\TextInput::make('nm_poli')
                            ->label('Nama Poli')
                            ->maxLength(50),

                        Forms\Components\Select::make('kunjungan_sakit')
                            ->label('Status Kunjungan')
                            ->options([
                                '0' => 'Sehat',
                                '1' => 'Sakit',
                            ])
                            ->default('1')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Tanda Vital')
                    ->schema([
                        Forms\Components\TextInput::make('beratbadan')
                            ->label('Berat Badan (kg)')
                            ->numeric()
                            ->step(0.01),

                        Forms\Components\TextInput::make('tinggibadan')
                            ->label('Tinggi Badan (cm)')
                            ->numeric()
                            ->step(0.01),

                        Forms\Components\TextInput::make('resprate')
                            ->label('Respiratory Rate')
                            ->numeric()
                            ->step(0.01),

                        Forms\Components\TextInput::make('heartrate')
                            ->label('Heart Rate')
                            ->numeric()
                            ->step(0.01),

                        Forms\Components\Select::make('sistole')
                            ->label('Sistole')
                            ->options([
                                '0' => 'Tidak',
                                '1' => 'Ya',
                            ])
                            ->default('0'),

                        Forms\Components\Select::make('diastole')
                            ->label('Diastole')
                            ->options([
                                '0' => 'Tidak',
                                '1' => 'Ya',
                            ])
                            ->default('0'),
                    ])->columns(3),

                Forms\Components\Section::make('Status Pengiriman')
                    ->schema([
                        Forms\Components\TextInput::make('no_urut')
                            ->label('No. Urut PCare')
                            ->maxLength(10)
                            ->disabled(),

                        Forms\Components\TextInput::make('no_kunjungan')
                            ->label('No. Kunjungan PCare')
                            ->maxLength(40)
                            ->disabled(),

                        Forms\Components\Select::make('status_kirim')
                            ->label('Status Kirim')
                            ->options([
                                'Belum' => 'Belum Kirim',
                                'Sudah' => 'Sudah Kirim',
                                'Gagal' => 'Gagal Kirim',
                            ])
                            ->default('Belum')
                            ->disabled(),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tgl_daftar')
                    ->label('Tgl Daftar')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('no_kartu')
                    ->label('No. Kartu')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nm_pasien')
                    ->label('Nama Pasien')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nm_poli')
                    ->label('Poli')
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('status_kirim')
                    ->label('Status')
                    ->colors([
                        'warning' => 'Belum',
                        'success' => 'Sudah',
                        'danger' => 'Gagal',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'Belum',
                        'heroicon-o-check-circle' => 'Sudah',
                        'heroicon-o-x-circle' => 'Gagal',
                    ]),

                Tables\Columns\TextColumn::make('no_kunjungan')
                    ->label('No. Kunjungan')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_kirim')
                    ->label('Status Kirim')
                    ->options([
                        'Belum' => 'Belum Kirim',
                        'Sudah' => 'Sudah Kirim',
                        'Gagal' => 'Gagal Kirim',
                    ]),

                Tables\Filters\Filter::make('tgl_daftar')
                    ->form([
                        Forms\Components\DatePicker::make('dari')
                            ->label('Dari Tanggal')
                            ->native(false),
                        Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari'], fn ($q, $date) => $q->where('tgl_daftar', '>=', $date))
                            ->when($data['sampai'], fn ($q, $date) => $q->where('tgl_daftar', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('kirim_pcare')
                    ->label('Kirim ke PCare')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (PcarePendaftaran $record) => $record->status_kirim !== 'Sudah')
                    ->action(function (PcarePendaftaran $record) {
                        // TODO: Implement kirim ke PCare logic
                        \Filament\Notifications\Notification::make()
                            ->title('Fitur dalam pengembangan')
                            ->body('Pengiriman ke PCare akan diimplementasikan')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\Action::make('hapus_pcare')
                    ->label('Hapus dari PCare')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (PcarePendaftaran $record) => $record->status_kirim === 'Sudah')
                    ->action(function (PcarePendaftaran $record) {
                        // TODO: Implement hapus dari PCare logic
                        \Filament\Notifications\Notification::make()
                            ->title('Fitur dalam pengembangan')
                            ->body('Penghapusan dari PCare akan diimplementasikan')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('kirim_bulk')
                        ->label('Kirim ke PCare (Bulk)')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            \Filament\Notifications\Notification::make()
                                ->title('Fitur dalam pengembangan')
                                ->body('Bulk kirim akan diimplementasikan')
                                ->warning()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Pasien')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('no_rawat')->label('No. Rawat'),
                                TextEntry::make('no_kartu')->label('No. Kartu BPJS'),
                                TextEntry::make('nm_pasien')->label('Nama Pasien'),
                                TextEntry::make('jkel')
                                    ->label('Jenis Kelamin')
                                    ->formatStateUsing(fn ($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                                TextEntry::make('tgl_lahir')
                                    ->label('Tanggal Lahir')
                                    ->date('d F Y'),
                                TextEntry::make('no_telp')->label('No. Telepon'),
                            ]),
                    ]),

                Section::make('Data Pendaftaran PCare')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('tgl_daftar')
                                    ->label('Tanggal Daftar')
                                    ->date('d F Y'),
                                TextEntry::make('kd_provider')->label('Kode Provider'),
                                TextEntry::make('nm_provider')->label('Nama Provider'),
                                TextEntry::make('kd_poli')->label('Kode Poli'),
                                TextEntry::make('nm_poli')->label('Nama Poli'),
                                TextEntry::make('kunjungan_sakit')
                                    ->label('Status Kunjungan')
                                    ->formatStateUsing(fn ($state) => $state === '1' ? 'Sakit' : 'Sehat')
                                    ->badge()
                                    ->color(fn ($state) => $state === '1' ? 'danger' : 'success'),
                            ]),
                    ]),

                Section::make('Tanda Vital')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextEntry::make('beratbadan')
                                    ->label('Berat Badan')
                                    ->suffix(' kg'),
                                TextEntry::make('tinggibadan')
                                    ->label('Tinggi Badan')
                                    ->suffix(' cm'),
                                TextEntry::make('resprate')
                                    ->label('Respiratory Rate'),
                                TextEntry::make('heartrate')
                                    ->label('Heart Rate'),
                            ]),
                    ]),

                Section::make('Status PCare')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('no_urut')->label('No. Urut PCare'),
                                TextEntry::make('no_kunjungan')->label('No. Kunjungan'),
                                TextEntry::make('status_kirim')
                                    ->label('Status Kirim')
                                    ->badge()
                                    ->color(fn ($state) => match($state) {
                                        'Sudah' => 'success',
                                        'Gagal' => 'danger',
                                        default => 'warning'
                                    }),
                                TextEntry::make('keterangan')
                                    ->label('Keterangan')
                                    ->columnSpanFull(),
                            ]),
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
            'index' => Pages\ListPcarePendaftarans::route('/'),
            'create' => Pages\CreatePcarePendaftaran::route('/create'),
            'view' => Pages\ViewPcarePendaftaran::route('/{record}'),
            'edit' => Pages\EditPcarePendaftaran::route('/{record}/edit'),
        ];
    }
}
