<?php

namespace App\Filament\Widgets;

use App\Models\DetailPemberianObat;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class TopMedicationsChart extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Obat Terlaris';

    protected static string $color = 'warning';

    protected int | string | array $columnSpan = 'full';

    public ?string $filter = '30';

    protected function getData(): array
    {
        $days = (int) $this->filter;
        $startDate = Carbon::now()->subDays($days);

        $topMedications = DetailPemberianObat::with('databarang')
            ->whereHas('regPeriksa', function ($query) use ($startDate) {
                $query->where('tgl_registrasi', '>=', $startDate);
            })
            ->selectRaw('kode_brng, SUM(jml) as total_qty, COUNT(*) as prescription_count')
            ->groupBy('kode_brng')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $labels = [];
        $quantities = [];
        $prescriptions = [];
        $colors = [];

        $colorPalette = [
            '#f59e0b', '#ef4444', '#10b981', '#3b82f6', '#8b5cf6',
            '#ec4899', '#14b8a6', '#f97316', '#06b6d4', '#84cc16'
        ];

        foreach ($topMedications as $index => $med) {
            $namaObat = $med->databarang
                ? substr($med->databarang->nama_brng, 0, 30)
                : $med->kode_brng;

            $labels[] = $namaObat;
            $quantities[] = $med->total_qty;
            $prescriptions[] = $med->prescription_count;
            $colors[] = $colorPalette[$index % count($colorPalette)];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Quantity',
                    'data' => $quantities,
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
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
