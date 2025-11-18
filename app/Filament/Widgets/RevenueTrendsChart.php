<?php

namespace App\Filament\Widgets;

use App\Models\PembayaranPasien;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueTrendsChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pendapatan';

    protected static string $color = 'success';

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = 'daily';

    protected function getData(): array
    {
        $filterType = $this->filter;

        switch ($filterType) {
            case 'daily':
                return $this->getDailyData();
            case 'weekly':
                return $this->getWeeklyData();
            case 'monthly':
                return $this->getMonthlyData();
            default:
                return $this->getDailyData();
        }
    }

    protected function getDailyData(): array
    {
        $days = 30;
        $data = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M');

            $revenue = PembayaranPasien::whereDate('tgl_bayar', $date->toDateString())
                ->sum('jumlah_bayar');

            $data[] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan Harian',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getWeeklyData(): array
    {
        $weeks = 12;
        $data = [];
        $labels = [];

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();

            $labels[] = $startOfWeek->format('d M') . ' - ' . $endOfWeek->format('d M');

            $revenue = PembayaranPasien::whereBetween('tgl_bayar', [
                $startOfWeek->toDateString(),
                $endOfWeek->toDateString()
            ])->sum('jumlah_bayar');

            $data[] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan Mingguan',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getMonthlyData(): array
    {
        $months = 12;
        $data = [];
        $labels = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $revenue = PembayaranPasien::whereYear('tgl_bayar', $date->year)
                ->whereMonth('tgl_bayar', $date->month)
                ->sum('jumlah_bayar');

            $data[] = $revenue;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan Bulanan',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getFilters(): ?array
    {
        return [
            'daily' => '30 Hari Terakhir',
            'weekly' => '12 Minggu Terakhir',
            'monthly' => '12 Bulan Terakhir',
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => "function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }",
                    ],
                ],
            ],
        ];
    }
}
