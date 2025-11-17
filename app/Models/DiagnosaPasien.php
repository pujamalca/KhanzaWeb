<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DiagnosaPasien extends Model
{
    protected $table = 'diagnosa_pasien';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'kd_penyakit',
        'status',
        'prioritas',
        'status_penyakit',
        'kd_dokter',
        'tgl_diagnosis',
        'jam_diagnosis',
    ];

    protected $casts = [
        'tgl_diagnosis' => 'date',
    ];

    /**
     * Relationship: DiagnosaPasien belongs to reg_periksa
     */
    public function regPeriksa()
    {
        return $this->belongsTo(reg_periksa::class, 'no_rawat', 'no_rawat');
    }

    /**
     * Relationship: DiagnosaPasien belongs to Penyakit (ICD-10)
     */
    public function penyakit()
    {
        return $this->belongsTo(Penyakit::class, 'kd_penyakit', 'kd_penyakit');
    }

    /**
     * Relationship: DiagnosaPasien belongs to Dokter
     */
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    /**
     * Accessor: Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->prioritas) {
            '1' => 'Diagnosa Utama',
            '2' => 'Diagnosa Sekunder 1',
            '3' => 'Diagnosa Sekunder 2',
            '4' => 'Diagnosa Sekunder 3',
            default => 'Unknown'
        };
    }

    /**
     * Accessor: Check if this is primary diagnosis
     */
    public function getIsPrimaryAttribute(): bool
    {
        return $this->prioritas === '1';
    }

    /**
     * Accessor: Get formatted diagnosis datetime
     */
    public function getFormattedDateTimeAttribute(): string
    {
        if (!$this->tgl_diagnosis || !$this->jam_diagnosis) {
            return '-';
        }

        $date = Carbon::parse($this->tgl_diagnosis)->format('d M Y');
        $time = $this->jam_diagnosis;

        return "{$date} {$time}";
    }

    /**
     * Accessor: Get full diagnosis display
     */
    public function getFullDiagnosisAttribute(): string
    {
        $code = $this->kd_penyakit ?? '-';
        $name = $this->penyakit->nm_penyakit ?? 'Unknown';

        return "{$code} - {$name}";
    }

    /**
     * Scope: Search by patient or diagnosis
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('no_rawat', 'like', "%{$search}%")
              ->orWhere('kd_penyakit', 'like', "%{$search}%")
              ->orWhereHas('regPeriksa.pasien', function($q2) use ($search) {
                  $q2->where('nm_pasien', 'like', "%{$search}%");
              })
              ->orWhereHas('penyakit', function($q2) use ($search) {
                  $q2->where('nm_penyakit', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Scope: Filter by priority
     */
    public function scopePriority($query, $prioritas)
    {
        return $query->where('prioritas', $prioritas);
    }

    /**
     * Scope: Primary diagnoses only
     */
    public function scopePrimary($query)
    {
        return $query->where('prioritas', '1');
    }

    /**
     * Scope: Secondary diagnoses only
     */
    public function scopeSecondary($query)
    {
        return $query->whereIn('prioritas', ['2', '3', '4']);
    }

    /**
     * Scope: Filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tgl_diagnosis', [$startDate, $endDate]);
    }

    /**
     * Scope: Today's diagnoses
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tgl_diagnosis', Carbon::today());
    }

    /**
     * Scope: Recent diagnoses
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('tgl_diagnosis', '>=', Carbon::now()->subDays($days));
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
     * Scope: New cases only
     */
    public function scopeNewCases($query)
    {
        return $query->where('status_penyakit', 'Baru');
    }

    /**
     * Scope: Old/recurring cases
     */
    public function scopeOldCases($query)
    {
        return $query->where('status_penyakit', 'Lama');
    }

    /**
     * Get most common diagnoses
     */
    public static function getMostCommon($limit = 10)
    {
        return self::select('kd_penyakit', \DB::raw('COUNT(*) as count'))
            ->with('penyakit')
            ->groupBy('kd_penyakit')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();
    }
}
