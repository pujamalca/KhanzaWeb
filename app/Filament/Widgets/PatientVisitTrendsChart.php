<?php

namespace App\Filament\Widgets;

use App\Models\reg_periksa;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class PatientVisitTrendsChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Kunjungan Pasien';

    protected static string $color = 'primary';

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '30';

    protected function getData(): array
    {
        $days = (int) $this->filter;
        $newPatients = [];
        $oldPatients = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d M');

            // Count new patients (first visit)
            $new = reg_periksa::whereDate('tgl_registrasi', $date->toDateString())
                ->where('status_lanjut', 'Baru')
                ->count();

            // Count old/return patients
            $old = reg_periksa::whereDate('tgl_registrasi', $date->toDateString())
                ->where('status_lanjut', 'Lama')
                ->count();

            $newPatients[] = $new;
            $oldPatients[] = $old;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pasien Baru',
                    'data' => $newPatients,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Pasien Lama',
                    'data' => $oldPatients,
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.2)',
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
            '7' => '7 Hari Terakhir',
            '30' => '30 Hari Terakhir',
            '90' => '90 Hari Terakhir',
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
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
