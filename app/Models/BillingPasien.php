<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BillingPasien extends Model
{
    protected $table = 'billing_pasien';
    protected $primaryKey = 'id';

    protected $fillable = [
        'no_rawat',
        'biaya_registrasi',
        'biaya_pemeriksaan',
        'biaya_tindakan',
        'biaya_obat',
        'biaya_laboratorium',
        'biaya_lainnya',
        'diskon',
        'pajak',
        'subtotal',
        'total_biaya',
        'total_dibayar',
        'sisa_tagihan',
        'status_bayar',
        'tgl_billing',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'tgl_billing' => 'date',
        'biaya_registrasi' => 'float',
        'biaya_pemeriksaan' => 'float',
        'biaya_tindakan' => 'float',
        'biaya_obat' => 'float',
        'biaya_laboratorium' => 'float',
        'biaya_lainnya' => 'float',
        'diskon' => 'float',
        'pajak' => 'float',
        'subtotal' => 'float',
        'total_biaya' => 'float',
        'total_dibayar' => 'float',
        'sisa_tagihan' => 'float',
    ];

    /**
     * Relationship: BillingPasien belongs to reg_periksa
     */
    public function regPeriksa()
    {
        return $this->belongsTo(reg_periksa::class, 'no_rawat', 'no_rawat');
    }

    /**
     * Relationship: BillingPasien has many PembayaranPasien
     */
    public function pembayaran()
    {
        return $this->hasMany(PembayaranPasien::class, 'billing_id', 'id');
    }

    /**
     * Accessor: Check if fully paid
     */
    public function getIsLunasAttribute(): bool
    {
        return $this->status_bayar === 'Lunas' || $this->sisa_tagihan <= 0;
    }

    /**
     * Accessor: Check if partially paid
     */
    public function getIsCicilanAttribute(): bool
    {
        return $this->total_dibayar > 0 && $this->sisa_tagihan > 0;
    }

    /**
     * Accessor: Get payment percentage
     */
    public function getPaymentPercentageAttribute(): float
    {
        if ($this->total_biaya <= 0) {
            return 0;
        }

        return round(($this->total_dibayar / $this->total_biaya) * 100, 2);
    }

    /**
     * Accessor: Get formatted status
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status_bayar) {
            'Lunas' => '✓ Lunas',
            'Cicilan' => '◐ Cicilan',
            'Belum Bayar' => '✗ Belum Bayar',
            default => $this->status_bayar
        };
    }

    /**
     * Calculate subtotal from all cost components
     */
    public function calculateSubtotal(): float
    {
        return $this->biaya_registrasi
             + $this->biaya_pemeriksaan
             + $this->biaya_tindakan
             + $this->biaya_obat
             + $this->biaya_laboratorium
             + $this->biaya_lainnya;
    }

    /**
     * Calculate total biaya (subtotal - diskon + pajak)
     */
    public function calculateTotal(): float
    {
        $subtotal = $this->calculateSubtotal();
        return $subtotal - $this->diskon + $this->pajak;
    }

    /**
     * Calculate remaining balance
     */
    public function calculateSisaTagihan(): float
    {
        return max(0, $this->total_biaya - $this->total_dibayar);
    }

    /**
     * Auto-calculate all costs from related records
     */
    public function autoCalculateFromServices(): void
    {
        $regPeriksa = $this->regPeriksa;

        if (!$regPeriksa) {
            return;
        }

        // Biaya registrasi
        $this->biaya_registrasi = $regPeriksa->biaya_reg ?? 0;

        // Biaya obat (from prescriptions)
        $this->biaya_obat = $regPeriksa->detailPemberianObat()
            ->sum('total') ?? 0;

        // Biaya pemeriksaan (can be set manually or from standard rates)
        // For now, we'll keep existing value or set default
        if (!$this->biaya_pemeriksaan) {
            $this->biaya_pemeriksaan = 50000; // Default examination fee
        }

        // Recalculate totals
        $this->subtotal = $this->calculateSubtotal();
        $this->total_biaya = $this->calculateTotal();
        $this->sisa_tagihan = $this->calculateSisaTagihan();
    }

    /**
     * Update payment status based on payments
     */
    public function updatePaymentStatus(): void
    {
        $this->total_dibayar = $this->pembayaran()->sum('jumlah_bayar');
        $this->sisa_tagihan = $this->calculateSisaTagihan();

        if ($this->sisa_tagihan <= 0) {
            $this->status_bayar = 'Lunas';
        } elseif ($this->total_dibayar > 0) {
            $this->status_bayar = 'Cicilan';
        } else {
            $this->status_bayar = 'Belum Bayar';
        }
    }

    /**
     * Scope: Search by patient or no_rawat
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('no_rawat', 'like', "%{$search}%")
              ->orWhereHas('regPeriksa.pasien', function($q2) use ($search) {
                  $q2->where('nm_pasien', 'like', "%{$search}%")
                     ->orWhere('no_rkm_medis', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Scope: Filter by payment status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_bayar', $status);
    }

    /**
     * Scope: Unpaid bills only
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status_bayar', 'Belum Bayar');
    }

    /**
     * Scope: Partially paid (installment)
     */
    public function scopeInstallment($query)
    {
        return $query->where('status_bayar', 'Cicilan');
    }

    /**
     * Scope: Fully paid
     */
    public function scopePaid($query)
    {
        return $query->where('status_bayar', 'Lunas');
    }

    /**
     * Scope: Overdue bills (unpaid for more than X days)
     */
    public function scopeOverdue($query, $days = 30)
    {
        return $query->where('status_bayar', '!=', 'Lunas')
                     ->where('tgl_billing', '<=', Carbon::now()->subDays($days));
    }

    /**
     * Scope: Today's bills
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tgl_billing', Carbon::today());
    }

    /**
     * Scope: Recent bills
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('tgl_billing', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope: Date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tgl_billing', [$startDate, $endDate]);
    }

    /**
     * Boot method for auto-calculations
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->tgl_billing) {
                $model->tgl_billing = Carbon::today();
            }

            // Auto-calculate from services if not manually set
            if ($model->no_rawat && $model->subtotal == 0) {
                $model->autoCalculateFromServices();
            }

            // Calculate totals
            $model->subtotal = $model->calculateSubtotal();
            $model->total_biaya = $model->calculateTotal();
            $model->sisa_tagihan = $model->calculateSisaTagihan();
        });

        static::updating(function ($model) {
            // Recalculate totals
            $model->subtotal = $model->calculateSubtotal();
            $model->total_biaya = $model->calculateTotal();
            $model->sisa_tagihan = $model->calculateSisaTagihan();
        });
    }

    /**
     * Get total revenue (sum of all paid amounts)
     */
    public static function getTotalRevenue($startDate = null, $endDate = null)
    {
        $query = self::query();

        if ($startDate && $endDate) {
            $query->whereBetween('tgl_billing', [$startDate, $endDate]);
        }

        return $query->sum('total_dibayar');
    }

    /**
     * Get outstanding balance (sum of unpaid amounts)
     */
    public static function getOutstandingBalance()
    {
        return self::where('status_bayar', '!=', 'Lunas')->sum('sisa_tagihan');
    }
}
