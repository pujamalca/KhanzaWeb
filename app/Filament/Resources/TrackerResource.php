<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrackerResource\Pages;
use App\Filament\Resources\TrackerResource\RelationManagers;
use App\Models\Tracker;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrackerResource extends Resource
{
    protected static ?string $model = Tracker::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Admin';

    // Label jamak, ganti dengan singular jika perlu
    protected static ?string $pluralLabel = 'Tracker'; // Setel ke bentuk singular

    // Label seperti button new akan berubah
    protected static ?string $label = 'Tracker';

    // title menu akan berubah
    protected static ?string $navigationLabel = 'Tracker';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                 //
                 Forms\Components\TextInput::make('nip')
                 ->label('NIP')
                 ->required()
                 ->disabled(), // Menonaktifkan input jika hanya view
             Forms\Components\DatePicker::make('tgl_login')
                 ->label('Tanggal Login')
                 ->required()
                 ->disabled(), // Menonaktifkan input jika hanya view
             Forms\Components\TimePicker::make('jam_login')
                 ->label('Jam Login')
                 ->required()
                 ->disabled(), // Menonaktifkan input jika hanya view
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('nip')
                ->label('NIP')
                ->sortable()
                ->searchable()
                ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('tgl_login')
                ->label('Tanggal Login')
                ->sortable()
                ->formatStateUsing(function ($state) {
                    return \Carbon\Carbon::parse($state)->format('d F Y');
                })
                ->alignment(Alignment::Center),


            Tables\Columns\TextColumn::make('jam_login')
                ->label('Jam Login')
                ->sortable()
                ->alignment(Alignment::Center),
            ])
            ->defaultSort('tgl_login', 'desc')

            ->filters([

                Tables\Filters\Filter::make('Rentang Tanggal')
                ->form([
                    Forms\Components\DatePicker::make('start_date')
                        ->label('Tanggal Mulai')
                        ->default(now()->toDateString()),
                    Forms\Components\DatePicker::make('end_date')
                        ->label('Tanggal Akhir')
                        ->default(now()->toDateString()),
                ])
                ->query(function ($query, $data) {
                    $startDate = $data['start_date'] ?? now()->toDateString();
                    $endDate = $data['end_date'] ?? now()->toDateString();

                    return $query->filterByDateRange($startDate, $endDate);
                }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getTableRecordKey($record): string
{
    return implode('_', [$record->nip, $record->tgl_login, $record->jam_login]);
}


    public function scopeFilterByDateRange($query, $startDate, $endDate)
    {
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d F Y', $startDate)->startOfDay();
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d F Y', $endDate)->endOfDay();

        return $query->whereBetween('tgl_login', [$startDateFormatted, $endDateFormatted]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrackers::route('/'),
        ];
    }
}
