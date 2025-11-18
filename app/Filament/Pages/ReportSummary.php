<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\reg_periksa;
use App\Models\PemeriksaanRalan;
use App\Models\DiagnosaPasien;
use App\Models\DetailPemberianObat;
use App\Models\BillingPasien;
use App\Models\PembayaranPasien;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class ReportSummary extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.report-summary';

    protected static ?string $navigationLabel = 'Laporan Summary';

    protected static ?string $title = 'Laporan Summary Klinik';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationGroup = 'Reports';

    public ?array $data = [];

    public $startDate;
    public $endDate;
    public $reportType = 'daily';

    public function mount(): void
    {
        $this->startDate = Carbon::today()->toDateString();
        $this->endDate = Carbon::today()->toDateString();

        $this->form->fill([
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'report_type' => $this->reportType,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('report_type')
                    ->label('Tipe Laporan')
                    ->options([
                        'daily' => 'Harian',
                        'weekly' => 'Mingguan',
                        'monthly' => 'Bulanan',
                        'custom' => 'Custom Range',
                    ])
                    ->default('daily')
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state === 'daily') {
                            $set('start_date', Carbon::today()->toDateString());
                            $set('end_date', Carbon::today()->toDateString());
                        } elseif ($state === 'weekly') {
                            $set('start_date', Carbon::now()->startOfWeek()->toDateString());
                            $set('end_date', Carbon::now()->endOfWeek()->toDateString());
                        } elseif ($state === 'monthly') {
                            $set('start_date', Carbon::now()->startOfMonth()->toDateString());
                            $set('end_date', Carbon::now()->endOfMonth()->toDateString());
                        }
                    }),

                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->default(Carbon::today())
                    ->native(false),

                DatePicker::make('end_date')
                    ->label('Tanggal Akhir')
                    ->default(Carbon::today())
                    ->native(false),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function getReportData(): array
    {
        $startDate = $this->data['start_date'] ?? Carbon::today()->toDateString();
        $endDate = $this->data['end_date'] ?? Carbon::today()->toDateString();

        return [
            // Patient Statistics
            'total_registrations' => reg_periksa::whereBetween('tgl_registrasi', [$startDate, $endDate])->count(),
            'new_patients' => reg_periksa::whereBetween('tgl_registrasi', [$startDate, $endDate])
                ->where('status_lanjut', 'Baru')->count(),
            'old_patients' => reg_periksa::whereBetween('tgl_registrasi', [$startDate, $endDate])
                ->where('status_lanjut', 'Lama')->count(),

            // Examination Statistics
            'total_examinations' => PemeriksaanRalan::whereBetween('tgl_perawatan', [$startDate, $endDate])->count(),
            'complete_soap' => PemeriksaanRalan::whereBetween('tgl_perawatan', [$startDate, $endDate])
                ->whereNotNull('keluhan')
                ->whereNotNull('pemeriksaan')
                ->whereNotNull('penilaian')
                ->whereNotNull('rtl')
                ->count(),

            // Diagnosis Statistics
            'total_diagnoses' => DiagnosaPasien::whereBetween('tgl_diagnosa', [$startDate, $endDate])->count(),
            'primary_diagnoses' => DiagnosaPasien::whereBetween('tgl_diagnosa', [$startDate, $endDate])
                ->where('prioritas', '1')->count(),
            'new_cases' => DiagnosaPasien::whereBetween('tgl_diagnosa', [$startDate, $endDate])
                ->where('status_penyakit', 'Baru')->count(),

            // Prescription Statistics
            'total_prescriptions' => DetailPemberianObat::whereHas('regPeriksa', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tgl_registrasi', [$startDate, $endDate]);
            })->count(),
            'total_medication_qty' => DetailPemberianObat::whereHas('regPeriksa', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('tgl_registrasi', [$startDate, $endDate]);
            })->sum('jml'),

            // Financial Statistics
            'total_billings' => BillingPasien::whereBetween('tgl_billing', [$startDate, $endDate])->count(),
            'total_billed_amount' => BillingPasien::whereBetween('tgl_billing', [$startDate, $endDate])->sum('total_biaya'),
            'total_payments' => PembayaranPasien::whereBetween('tgl_bayar', [$startDate, $endDate])->count(),
            'total_revenue' => PembayaranPasien::whereBetween('tgl_bayar', [$startDate, $endDate])->sum('jumlah_bayar'),
            'cash_payments' => PembayaranPasien::whereBetween('tgl_bayar', [$startDate, $endDate])
                ->where('metode_bayar', 'Tunai')->sum('jumlah_bayar'),
            'non_cash_payments' => PembayaranPasien::whereBetween('tgl_bayar', [$startDate, $endDate])
                ->where('metode_bayar', '!=', 'Tunai')->sum('jumlah_bayar'),
            'outstanding_balance' => BillingPasien::where('status_bayar', '!=', 'Lunas')->sum('sisa_tagihan'),

            // Top Diagnoses
            'top_diagnoses' => DiagnosaPasien::with('penyakit')
                ->whereBetween('tgl_diagnosa', [$startDate, $endDate])
                ->selectRaw('kd_penyakit, COUNT(*) as total')
                ->groupBy('kd_penyakit')
                ->orderByDesc('total')
                ->limit(10)
                ->get(),

            // Top Medications
            'top_medications' => DetailPemberianObat::with('databarang')
                ->whereHas('regPeriksa', function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('tgl_registrasi', [$startDate, $endDate]);
                })
                ->selectRaw('kode_brng, SUM(jml) as total_qty')
                ->groupBy('kode_brng')
                ->orderByDesc('total_qty')
                ->limit(10)
                ->get(),
        ];
    }
}
