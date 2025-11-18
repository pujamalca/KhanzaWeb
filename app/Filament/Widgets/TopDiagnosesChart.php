<?php

namespace App\Filament\Widgets;

use App\Models\DiagnosaPasien;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class TopDiagnosesChart extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Diagnosa';

    protected static string $color = 'info';

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '30';

    protected function getData(): array
    {
        $days = (int) $this->filter;
        $startDate = Carbon::now()->subDays($days);

        $topDiagnoses = DiagnosaPasien::with('penyakit')
            ->where('tgl_diagnosa', '>=', $startDate)
            ->selectRaw('kd_penyakit, COUNT(*) as total')
            ->groupBy('kd_penyakit')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $labels = [];
        $data = [];
        $colors = [];

        $colorPalette = [
            '#e74c3c', '#3498db', '#2ecc71', '#f39c12', '#9b59b6',
            '#1abc9c', '#34495e', '#e67e22', '#95a5a6', '#d35400'
        ];

        foreach ($topDiagnoses as $index => $diagnosis) {
            $penyakit = $diagnosis->penyakit;
            $label = $penyakit
                ? "{$diagnosis->kd_penyakit} - " . substr($penyakit->nm_penyakit, 0, 30)
                : $diagnosis->kd_penyakit;

            $labels[] = $label;
            $data[] = $diagnosis->total;
            $colors[] = $colorPalette[$index % count($colorPalette)];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Kasus',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => $colors,
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            '7' => '7 Hari Terakhir',
            '30' => '30 Hari Terakhir',
            '90' => '90 Hari Terakhir',
            '365' => '1 Tahun Terakhir',
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
