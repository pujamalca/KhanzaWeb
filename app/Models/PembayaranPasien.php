<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PembayaranPasien extends Model
{
    protected $table = 'pembayaran_pasien';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'billing_id',
        'no_rawat',
        'jumlah_bayar',
        'metode_bayar',
        'no_referensi',
        'tgl_bayar',
        'jam_bayar',
        'keterangan',
        'diterima_oleh',
    ];

    protected $casts = [
        'tgl_bayar' => 'date',
        'jumlah_bayar' => 'float',
        'created_at' => 'datetime',
    ];

    /**
     * Relationship: PembayaranPasien belongs to BillingPasien
     */
    public function billing()
    {
        return $this->belongsTo(BillingPasien::class, 'billing_id', 'id');
    }

    /**
     * Relationship: PembayaranPasien belongs to reg_periksa
     */
    public function regPeriksa()
    {
        return $this->belongsTo(reg_periksa::class, 'no_rawat', 'no_rawat');
    }

    /**
     * Relationship: PembayaranPasien belongs to Petugas (diterima_oleh)
     */
    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'diterima_oleh', 'nip');
    }

    /**
     * Accessor: Get formatted datetime
     */
    public function getFormattedDateTimeAttribute(): string
    {
        if (!$this->tgl_bayar || !$this->jam_bayar) {
            return '-';
        }

        $date = Carbon::parse($this->tgl_bayar)->format('d M Y');
        $time = $this->jam_bayar;

        return "{$date} {$time}";
    }

    /**
     * Accessor: Get payment method display
     */
    public function getMetodeDisplayAttribute(): string
    {
        return match($this->metode_bayar) {
            'Tunai' => '💵 Tunai',
            'Transfer' => '🏦 Transfer',
            'Kartu Kredit' => '💳 Kartu Kredit',
            'Kartu Debit' => '💳 Kartu Debit',
            'BPJS' => '🏥 BPJS',
            'Asuransi' => '🛡️ Asuransi',
            'Lainnya' => '📝 Lainnya',
            default => $this->metode_bayar
        };
    }

    /**
     * Scope: Search by patient or no_rawat
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('no_rawat', 'like', "%{$search}%")
              ->orWhere('no_referensi', 'like', "%{$search}%")
              ->orWhereHas('regPeriksa.pasien', function($q2) use ($search) {
                  $q2->where('nm_pasien', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Scope: Filter by payment method
     */
    public function scopeByMethod($query, $method)
    {
        return $query->where('metode_bayar', $method);
    }

    /**
     * Scope: Today's payments
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tgl_bayar', Carbon::today());
    }

    /**
     * Scope: Recent payments
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('tgl_bayar', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope: Date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tgl_bayar', [$startDate, $endDate]);
    }

    /**
     * Scope: By petugas (cashier)
     */
    public function scopeByPetugas($query, $nip)
    {
        return $query->where('diterima_oleh', $nip);
    }

    /**
     * Boot method to update billing after payment
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            if (!$model->tgl_bayar) {
                $model->tgl_bayar = Carbon::today();
            }
            if (!$model->jam_bayar) {
                $model->jam_bayar = Carbon::now()->format('H:i:s');
            }

            // Update billing status
            if ($model->billing) {
                $model->billing->updatePaymentStatus();
                $model->billing->save();
            }
        });

        static::deleted(function ($model) {
            // Update billing status after payment deletion
            if ($model->billing) {
                $model->billing->updatePaymentStatus();
                $model->billing->save();
            }
        });
    }

    /**
     * Get total payments by method
     */
    public static function getTotalByMethod($method, $startDate = null, $endDate = null)
    {
        $query = self::where('metode_bayar', $method);

        if ($startDate && $endDate) {
            $query->whereBetween('tgl_bayar', [$startDate, $endDate]);
        }

        return $query->sum('jumlah_bayar');
    }

    /**
     * Get today's total payments
     */
    public static function getTodayTotal()
    {
        return self::whereDate('tgl_bayar', Carbon::today())->sum('jumlah_bayar');
    }
}
