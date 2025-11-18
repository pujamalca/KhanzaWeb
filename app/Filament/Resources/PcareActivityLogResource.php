<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PcareActivityLogResource\Pages;
use App\Models\PcareActivityLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid;

class PcareActivityLogResource extends Resource
{
    protected static ?string $model = PcareActivityLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Activity Log';

    protected static ?string $navigationGroup = 'BPJS PCare';

    protected static ?int $navigationSort = 10;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('activity_type')
                    ->label('Aktivitas')
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi')
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'get' => 'info',
                        'insert', 'post' => 'success',
                        'update', 'put' => 'warning',
                        'delete' => 'danger',
                        default => 'gray'
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'success',
                        'danger' => 'failed',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'success',
                        'heroicon-o-x-circle' => 'failed',
                    ]),

                Tables\Columns\TextColumn::make('http_code')
                    ->label('HTTP Code')
                    ->badge()
                    ->color(fn ($state): string => match(true) {
                        $state >= 200 && $state < 300 => 'success',
                        $state >= 400 && $state < 500 => 'warning',
                        $state >= 500 => 'danger',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('no_rawat')
                    ->label('No. Rawat')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('no_kunjungan')
                    ->label('No. Kunjungan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user')
                    ->label('User')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('endpoint')
                    ->label('Endpoint')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),

                Tables\Filters\SelectFilter::make('activity_type')
                    ->label('Tipe Aktivitas')
                    ->options([
                        'get_peserta' => 'Get Peserta',
                        'insert_pendaftaran' => 'Insert Pendaftaran',
                        'delete_pendaftaran' => 'Delete Pendaftaran',
                        'insert_kunjungan' => 'Insert Kunjungan',
                        'update_kunjungan' => 'Update Kunjungan',
                        'insert_tindakan' => 'Insert Tindakan',
                        'insert_rujukan' => 'Insert Rujukan',
                    ]),

                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'get' => 'GET',
                        'post' => 'POST',
                        'put' => 'PUT',
                        'delete' => 'DELETE',
                    ]),

                Tables\Filters\Filter::make('created_at')
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
                            ->when($data['dari'], fn ($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['sampai'], fn ($q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Activity')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Waktu')
                                    ->dateTime('d F Y H:i:s'),
                                TextEntry::make('activity_type')
                                    ->label('Tipe Aktivitas')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('action')
                                    ->label('Aksi')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn ($state) => $state === 'success' ? 'success' : 'danger'),
                                TextEntry::make('http_code')
                                    ->label('HTTP Code')
                                    ->badge(),
                                TextEntry::make('user')
                                    ->label('User'),
                            ]),
                    ]),

                Section::make('Detail Request')
                    ->schema([
                        TextEntry::make('endpoint')
                            ->label('Endpoint')
                            ->columnSpanFull(),
                        TextEntry::make('no_rawat')
                            ->label('No. Rawat'),
                        TextEntry::make('no_kunjungan')
                            ->label('No. Kunjungan'),
                        TextEntry::make('request_data')
                            ->label('Request Data')
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state) => $state ? json_encode(json_decode($state), JSON_PRETTY_PRINT) : '-')
                            ->copyable(),
                    ]),

                Section::make('Response Data')
                    ->schema([
                        TextEntry::make('response_data')
                            ->label('Response')
                            ->columnSpanFull()
                            ->formatStateUsing(fn ($state) => $state ? json_encode(json_decode($state), JSON_PRETTY_PRINT) : '-')
                            ->copyable(),
                        TextEntry::make('error_message')
                            ->label('Error Message')
                            ->columnSpanFull()
                            ->visible(fn ($record) => !empty($record->error_message))
                            ->badge()
                            ->color('danger'),
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
            'index' => Pages\ListPcareActivityLogs::route('/'),
            'view' => Pages\ViewPcareActivityLog::route('/{record}'),
        ];
    }
}
