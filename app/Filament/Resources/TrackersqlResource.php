<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrackersqlResource\Pages;
use App\Filament\Resources\TrackersqlResource\RelationManagers;
use App\Models\Trackersql;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrackersqlResource extends Resource
{
    protected static ?string $model = Trackersql::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-end-on-rectangle';

    protected static ?string $navigationGroup = 'Admin';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(1) // Menentukan 3 kolom dalam satu baris
                ->schema([
                    Forms\Components\Textarea::make('sqle')
                        ->label('Keterangan')
                        ->required()
                        ->rows(12)  // Membatasi menjadi satu baris
                        ->extraAttributes([
                            'style' => 'height: 300px; resize: auto;'  // Menyesuaikan tinggi dan menonaktifkan resize
                        ])
                        ->disabled(), // Menonaktifkan input jika hanya view

                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal Login')
                        ->required()
                        ->disabled(), // Menonaktifkan input jika hanya view

                    Forms\Components\TextInput::make('usere')
                        ->label('User')
                        ->required()
                        ->disabled(), // Menonaktifkan input jika hanya view
                ]),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('tanggal')
                ->label('Tanggal Login')
                ->searchable(isIndividual:True)
                ->formatStateUsing(function ($state) {
                    return \Carbon\Carbon::parse($state)->format('d F Y');
                })
                ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('usere')
                ->label('User')
                ->searchable(isIndividual:True)
                ->sortable()
                ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('sqle')
                ->label('Keterangan')
                ->sortable()
                ->searchable(isIndividual:True)
                ->wrap() // Menggunakan properti wrap() jika tersedia atau kustomisasi
                ->extraAttributes(['style' => ' white-space: normal; word-wrap: break-word;']),

            ])
            ->defaultSort('tanggal', 'desc')
            ->filters([
                //
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

    public function scopeFilterByDateRange($query, $startDate, $endDate)
    {
        $startDateFormatted = \Carbon\Carbon::createFromFormat('d F Y', $startDate)->startOfDay();
        $endDateFormatted = \Carbon\Carbon::createFromFormat('d F Y', $endDate)->endOfDay();

        return $query->whereBetween('tanggal', [$startDateFormatted, $endDateFormatted]);
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
            'index' => Pages\ListTrackersqls::route('/'),
            // 'create' => Pages\CreateTrackersql::route('/create'),
            // 'edit' => Pages\EditTrackersql::route('/{record}/edit'),
        ];
    }
}
