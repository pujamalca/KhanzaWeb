<?php

namespace App\Filament\Filters;

use Filament\Forms;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;

class DateRangeFilter
{
    public static function make(string $columnName = 'tanggal', string $label = 'Rentang Tanggal'): Filter
    {
        return Filter::make($label)
            ->form([
                Forms\Components\DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->default(now()->toDateString()),

                Forms\Components\DatePicker::make('end_date')
                    ->label('Tanggal Akhir')
                    ->default(now()->toDateString()),
            ])
            ->query(function ($query, $data) use ($columnName) {
                $startDate = $data['start_date'] ?? now()->toDateString();
                $endDate = $data['end_date'] ?? now()->toDateString();

                return $query->whereBetween($columnName, [$startDate, $endDate]);
            });
    }
}
