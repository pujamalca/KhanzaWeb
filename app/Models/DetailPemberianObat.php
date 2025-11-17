<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DetailPemberianObat extends Model
{
    protected $table = 'detail_pemberian_obat';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'tgl_perawatan',
        'jam',
        'no_rawat',
        'kode_brng',
        'jml',
        'embalase',
        'tuslah',
        'total',
        'dosis',
        'frekuensi',
        'aturan_pakai',
        'catatan',
        'kd_dokter',
        'nip',
        'status',
    ];

    protected $casts = [
        'tgl_perawatan' => 'date',
        'jml' => 'float',
        'embalase' => 'float',
        'tuslah' => 'float',
        'total' => 'float',
    ];

    /**
     * Relationship: DetailPemberianObat belongs to reg_periksa
     */
    public function regPeriksa()
    {
        return $this->belongsTo(reg_periksa::class, 'no_rawat', 'no_rawat');
    }

    /**
     * Relationship: DetailPemberianObat belongs to Databarang
     */
    public function databarang()
    {
        return $this->belongsTo(Databarang::class, 'kode_brng', 'kode_brng');
    }

    /**
     * Relationship: DetailPemberianObat belongs to Dokter
     */
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    /**
     * Relationship: DetailPemberianObat belongs to Petugas
     */
    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'nip', 'nip');
    }

    /**
     * Accessor: Get formatted datetime
     */
    public function getFormattedDateTimeAttribute(): string
    {
        if (!$this->tgl_perawatan || !$this->jam) {
            return '-';
        }

        $date = Carbon::parse($this->tgl_perawatan)->format('d M Y');
        $time = $this->jam;

        return "{$date} {$time}";
    }

    /**
     * Accessor: Get formatted dosage instruction
     */
    public function getInstruksiLengkapAttribute(): string
    {
        $parts = [];

        if ($this->dosis) {
            $parts[] = "Dosis: {$this->dosis}";
        }

        if ($this->frekuensi) {
            $parts[] = "Frekuensi: {$this->frekuensi}";
        }

        if ($this->aturan_pakai) {
            $parts[] = "Aturan: {$this->aturan_pakai}";
        }

        return !empty($parts) ? implode(' | ', $parts) : '-';
    }

    /**
     * Accessor: Get medication name
     */
    public function getNamaObatAttribute(): string
    {
        return $this->databarang->nama_brng ?? 'Unknown';
    }

    /**
     * Accessor: Calculate subtotal (before embalase & tuslah)
     */
    public function getSubtotalAttribute(): float
    {
        $price = $this->databarang->ralan ?? 0; // Use outpatient price
        return $this->jml * $price;
    }

    /**
     * Scope: Search by patient, medication, or no_rawat
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('no_rawat', 'like', "%{$search}%")
              ->orWhere('kode_brng', 'like', "%{$search}%")
              ->orWhereHas('regPeriksa.pasien', function($q2) use ($search) {
                  $q2->where('nm_pasien', 'like', "%{$search}%");
              })
              ->orWhereHas('databarang', function($q2) use ($search) {
                  $q2->where('nama_brng', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tgl_perawatan', [$startDate, $endDate]);
    }

    /**
     * Scope: Today's prescriptions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tgl_perawatan', Carbon::today());
    }

    /**
     * Scope: Recent prescriptions
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('tgl_perawatan', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope: By status (Ralan/Ranap)
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: By doctor
     */
    public function scopeByDokter($query, $kd_dokter)
    {
        return $query->where('kd_dokter', $kd_dokter);
    }

    /**
     * Scope: By medication
     */
    public function scopeByMedication($query, $kode_brng)
    {
        return $query->where('kode_brng', $kode_brng);
    }

    /**
     * Get most prescribed medications
     */
    public static function getMostPrescribed($limit = 10)
    {
        return self::select('kode_brng', \DB::raw('SUM(jml) as total_qty'), \DB::raw('COUNT(*) as count'))
            ->with('databarang')
            ->groupBy('kode_brng')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }

    /**
     * Calculate total cost automatically
     */
    public function calculateTotal()
    {
        $price = $this->databarang->ralan ?? 0;
        $subtotal = $this->jml * $price;
        $this->total = $subtotal + $this->embalase + $this->tuslah;

        return $this->total;
    }

    /**
     * Boot method to auto-calculate total
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if ($model->databarang) {
                $price = $model->databarang->ralan ?? 0;
                $subtotal = $model->jml * $price;
                $model->total = $subtotal + $model->embalase + $model->tuslah;
            }
        });

        static::updating(function ($model) {
            if ($model->databarang) {
                $price = $model->databarang->ralan ?? 0;
                $subtotal = $model->jml * $price;
                $model->total = $subtotal + $model->embalase + $model->tuslah;
            }
        });
    }
}
