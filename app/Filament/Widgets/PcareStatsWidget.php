<?php

namespace App\Filament\Widgets;

use App\Models\PcarePendaftaran;
use App\Models\PcareKunjungan;
use App\Models\PcareRujukan;
use App\Models\PcareActivityLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PcareStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $today = date('Y-m-d');

        // Pendaftaran stats
        $pendaftaranToday = PcarePendaftaran::where('tgl_daftar', $today)->count();
        $pendaftaranBelum = PcarePendaftaran::where('status_kirim', 'Belum')->count();
        $pendaftaranGagal = PcarePendaftaran::where('status_kirim', 'Gagal')->count();

        // Kunjungan stats
        $kunjunganToday = PcareKunjungan::where('tgl_kunjungan', $today)->count();
        $kunjunganBelum = PcareKunjungan::where('status_kirim', 'Belum')->count();

        // Rujukan stats
        $rujukanToday = PcareRujukan::where('tgl_rujukan', $today)->count();

        // Activity log stats
        $apiCallsToday = PcareActivityLog::whereDate('created_at', today())->count();
        $apiCallsFailed = PcareActivityLog::where('status', 'failed')
            ->whereDate('created_at', today())
            ->count();

        return [
            Stat::make('Pendaftaran Hari Ini', $pendaftaranToday)
                ->description($pendaftaranBelum > 0 ? "{$pendaftaranBelum} belum terkirim" : 'Semua terkirim')
                ->descriptionIcon($pendaftaranBelum > 0 ? 'heroicon-o-clock' : 'heroicon-o-check-circle')
                ->color($pendaftaranBelum > 0 ? 'warning' : 'success')
                ->chart([7, 4, 5, 9, 10, 15, $pendaftaranToday]),

            Stat::make('Kunjungan Hari Ini', $kunjunganToday)
                ->description($kunjunganBelum > 0 ? "{$kunjunganBelum} belum terkirim" : 'Semua terkirim')
                ->descriptionIcon($kunjunganBelum > 0 ? 'heroicon-o-clock' : 'heroicon-o-check-circle')
                ->color($kunjunganBelum > 0 ? 'warning' : 'success')
                ->chart([5, 3, 6, 8, 12, 10, $kunjunganToday]),

            Stat::make('Rujukan Hari Ini', $rujukanToday)
                ->description('Rujukan ke spesialis/RS')
                ->descriptionIcon('heroicon-o-arrow-top-right-on-square')
                ->color('danger')
                ->chart([1, 2, 1, 3, 2, 1, $rujukanToday]),

            Stat::make('API Calls Hari Ini', $apiCallsToday)
                ->description($apiCallsFailed > 0 ? "{$apiCallsFailed} gagal" : 'Semua sukses')
                ->descriptionIcon($apiCallsFailed > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color($apiCallsFailed > 0 ? 'danger' : 'info')
                ->chart([20, 15, 25, 30, 28, 22, $apiCallsToday]),

            Stat::make('Pendaftaran Gagal', $pendaftaranGagal)
                ->description('Perlu dicek dan dikirim ulang')
                ->descriptionIcon('heroicon-o-exclamation-triangle')
                ->color('danger')
                ->visible($pendaftaranGagal > 0),
        ];
    }
}
