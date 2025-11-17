<?php

namespace App\Filament\Widgets;

use App\Models\reg_periksa;
use App\Models\PemeriksaanRalan;
use App\Models\pasien;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Today's statistics
        $todayRegistrations = reg_periksa::whereDate('tgl_registrasi', Carbon::today())->count();
        $todayExaminations = PemeriksaanRalan::whereDate('tgl_perawatan', Carbon::today())->count();
        $incompleteSoap = PemeriksaanRalan::incompleteSoap()->count();

        // Week comparisons
        $lastWeekRegistrations = reg_periksa::whereDate('tgl_registrasi', Carbon::today()->subWeek())->count();
        $registrationChange = $lastWeekRegistrations > 0
            ? round((($todayRegistrations - $lastWeekRegistrations) / $lastWeekRegistrations) * 100, 1)
            : 0;

        // Total patients
        $totalPatients = pasien::count();

        // Pending examinations (registered today but no examination yet)
        $pendingExaminations = reg_periksa::whereDate('tgl_registrasi', Carbon::today())
            ->doesntHave('pemeriksaanRalan')
            ->count();

        return [
            Stat::make('Pasien Hari Ini', $todayRegistrations)
                ->description($registrationChange > 0 ? "+{$registrationChange}% dari minggu lalu" : "{$registrationChange}% dari minggu lalu")
                ->descriptionIcon($registrationChange > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($registrationChange > 0 ? 'success' : 'danger')
                ->chart([12, 15, 10, 18, 14, $todayRegistrations]),

            Stat::make('Pemeriksaan Selesai', $todayExaminations)
                ->description("Dari {$todayRegistrations} pasien hari ini")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->chart([8, 12, 10, 15, 11, $todayExaminations]),

            Stat::make('Pemeriksaan Pending', $pendingExaminations)
                ->description('Belum diperiksa hari ini')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($pendingExaminations > 0 ? 'warning' : 'success')
                ->url(route('filament.superadmin.resources.rawat-jalans.index')),

            Stat::make('SOAP Belum Lengkap', $incompleteSoap)
                ->description('Perlu dilengkapi')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($incompleteSoap > 0 ? 'danger' : 'success')
                ->url(route('filament.superadmin.resources.pemeriksaan-ralans.index')),

            Stat::make('Total Pasien Terdaftar', number_format($totalPatients, 0, ',', '.'))
                ->description('Seluruh database')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->url(route('filament.superadmin.resources.pasiens.index')),
        ];
    }
}
