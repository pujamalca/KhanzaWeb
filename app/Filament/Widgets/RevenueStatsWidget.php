<?php

namespace App\Filament\Widgets;

use App\Models\BillingPasien;
use App\Models\PembayaranPasien;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class RevenueStatsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected function getStats(): array
    {
        // Today's revenue
        $todayRevenue = PembayaranPasien::whereDate('tgl_bayar', Carbon::today())
            ->sum('jumlah_bayar');

        // This month's revenue
        $monthRevenue = PembayaranPasien::whereYear('tgl_bayar', Carbon::now()->year)
            ->whereMonth('tgl_bayar', Carbon::now()->month)
            ->sum('jumlah_bayar');

        // Last month revenue for comparison
        $lastMonthRevenue = PembayaranPasien::whereYear('tgl_bayar', Carbon::now()->subMonth()->year)
            ->whereMonth('tgl_bayar', Carbon::now()->subMonth()->month)
            ->sum('jumlah_bayar');

        $monthChange = $lastMonthRevenue > 0
            ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // Outstanding balance (piutang)
        $outstanding = BillingPasien::where('status_bayar', '!=', 'Lunas')
            ->sum('sisa_tagihan');

        // Unpaid bills count
        $unpaidCount = BillingPasien::where('status_bayar', '!=', 'Lunas')->count();

        // Today's payments count
        $todayPayments = PembayaranPasien::whereDate('tgl_bayar', Carbon::today())->count();

        return [
            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description("{$todayPayments} pembayaran")
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart([
                    PembayaranPasien::whereDate('tgl_bayar', Carbon::today()->subDays(6))->sum('jumlah_bayar') / 1000000,
                    PembayaranPasien::whereDate('tgl_bayar', Carbon::today()->subDays(5))->sum('jumlah_bayar') / 1000000,
                    PembayaranPasien::whereDate('tgl_bayar', Carbon::today()->subDays(4))->sum('jumlah_bayar') / 1000000,
                    PembayaranPasien::whereDate('tgl_bayar', Carbon::today()->subDays(3))->sum('jumlah_bayar') / 1000000,
                    PembayaranPasien::whereDate('tgl_bayar', Carbon::today()->subDays(2))->sum('jumlah_bayar') / 1000000,
                    PembayaranPasien::whereDate('tgl_bayar', Carbon::today()->subDays(1))->sum('jumlah_bayar') / 1000000,
                    $todayRevenue / 1000000,
                ]),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($monthRevenue, 0, ',', '.'))
                ->description($monthChange > 0 ? "+{$monthChange}% dari bulan lalu" : "{$monthChange}% dari bulan lalu")
                ->descriptionIcon($monthChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthChange > 0 ? 'success' : 'danger'),

            Stat::make('Piutang (Outstanding)', 'Rp ' . number_format($outstanding, 0, ',', '.'))
                ->description("{$unpaidCount} tagihan belum lunas")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($outstanding > 0 ? 'warning' : 'success')
                ->url(route('filament.superadmin.resources.billing-pasiens.index')),

            Stat::make('Tunai Hari Ini', 'Rp ' . number_format(
                PembayaranPasien::whereDate('tgl_bayar', Carbon::today())
                    ->where('metode_bayar', 'Tunai')
                    ->sum('jumlah_bayar'),
                0, ',', '.'
            ))
                ->description('Pembayaran tunai')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('info'),

            Stat::make('Non-Tunai Hari Ini', 'Rp ' . number_format(
                PembayaranPasien::whereDate('tgl_bayar', Carbon::today())
                    ->whereIn('metode_bayar', ['Transfer', 'Kartu Kredit', 'Kartu Debit'])
                    ->sum('jumlah_bayar'),
                0, ',', '.'
            ))
                ->description('Transfer & Kartu')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('primary'),
        ];
    }
}
