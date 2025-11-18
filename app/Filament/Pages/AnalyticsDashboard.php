<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AnalyticsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.pages.analytics-dashboard';

    protected static ?string $navigationLabel = 'Analytics & Laporan';

    protected static ?string $title = 'Analytics & Laporan';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationGroup = 'Reports';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverviewWidget::class,
            \App\Filament\Widgets\RevenueStatsWidget::class,
            \App\Filament\Widgets\RevenueTrendsChart::class,
            \App\Filament\Widgets\PatientVisitTrendsChart::class,
            \App\Filament\Widgets\TopDiagnosesChart::class,
            \App\Filament\Widgets\TopMedicationsChart::class,
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverviewWidget::class,
            \App\Filament\Widgets\RevenueStatsWidget::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\RevenueTrendsChart::class,
            \App\Filament\Widgets\PatientVisitTrendsChart::class,
            \App\Filament\Widgets\TopDiagnosesChart::class,
            \App\Filament\Widgets\TopMedicationsChart::class,
        ];
    }

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }

    protected function getFooterWidgetsColumns(): int | array
    {
        return 1;
    }
}
