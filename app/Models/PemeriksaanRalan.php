<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PemeriksaanRalan extends Model
{
    protected $table = 'pemeriksaan_ralan';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'tgl_perawatan',
        'jam_rawat',
        'suhu_tubuh',
        'tensi',
        'nadi',
        'respirasi',
        'tinggi',
        'berat',
        'spo2',
        'gcs',
        'kesadaran',
        'keluhan',
        'pemeriksaan',
        'alergi',
        'lingkar_perut',
        'rtl',
        'penilaian',
        'instruksi',
        'evaluasi',
        'nip',
    ];

    protected $casts = [
        'tgl_perawatan' => 'date',
    ];

    /**
     * Relationship: PemeriksaanRalan belongs to reg_periksa
     */
    public function regPeriksa()
    {
        return $this->belongsTo(reg_periksa::class, 'no_rawat', 'no_rawat');
    }

    /**
     * Relationship: PemeriksaanRalan belongs to Petugas (via nip)
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
        if (!$this->tgl_perawatan || !$this->jam_rawat) {
            return '-';
        }

        $date = Carbon::parse($this->tgl_perawatan)->format('d M Y');
        $time = $this->jam_rawat;

        return "{$date} {$time}";
    }

    /**
     * Accessor: Calculate BMI (Body Mass Index)
     * BMI = Weight (kg) / (Height (m))^2
     */
    public function getBmiAttribute(): ?float
    {
        $weight = $this->berat ? (float) $this->berat : null;
        $height = $this->tinggi ? (float) $this->tinggi : null;

        if (!$weight || !$height || $height <= 0) {
            return null;
        }

        $heightInMeters = $height / 100; // Convert cm to meters
        $bmi = $weight / ($heightInMeters * $heightInMeters);

        return round($bmi, 2);
    }

    /**
     * Accessor: Get BMI Category
     */
    public function getBmiCategoryAttribute(): ?string
    {
        $bmi = $this->bmi;

        if ($bmi === null) {
            return null;
        }

        if ($bmi < 18.5) {
            return 'Underweight';
        } elseif ($bmi < 25) {
            return 'Normal';
        } elseif ($bmi < 30) {
            return 'Overweight';
        } else {
            return 'Obese';
        }
    }

    /**
     * Accessor: Check if vital signs are complete
     */
    public function getIsVitalSignsCompleteAttribute(): bool
    {
        return !empty($this->suhu_tubuh) &&
               !empty($this->tensi) &&
               !empty($this->nadi) &&
               !empty($this->respirasi);
    }

    /**
     * Accessor: Check if SOAP notes are complete
     */
    public function getIsSoapCompleteAttribute(): bool
    {
        return !empty($this->keluhan) &&        // Subjective
               !empty($this->pemeriksaan) &&     // Objective
               !empty($this->penilaian) &&       // Assessment
               !empty($this->rtl);               // Plan
    }

    /**
     * Scope: Search by patient name or no_rawat
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('no_rawat', 'like', "%{$search}%")
                     ->orWhereHas('regPeriksa.pasien', function($q) use ($search) {
                         $q->where('nm_pasien', 'like', "%{$search}%");
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
     * Scope: Today's examinations
     */
    public function scopeToday($query)
    {
        return $query->whereDate('tgl_perawatan', Carbon::today());
    }

    /**
     * Scope: Recent examinations (last 7 days)
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('tgl_perawatan', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Scope: Incomplete SOAP notes
     */
    public function scopeIncompleteSoap($query)
    {
        return $query->where(function($q) {
            $q->whereNull('keluhan')
              ->orWhereNull('pemeriksaan')
              ->orWhereNull('penilaian')
              ->orWhereNull('rtl')
              ->orWhere('keluhan', '')
              ->orWhere('pemeriksaan', '')
              ->orWhere('penilaian', '')
              ->orWhere('rtl', '');
        });
    }

    /**
     * Scope: By petugas/nurse
     */
    public function scopeByPetugas($query, $nip)
    {
        return $query->where('nip', $nip);
    }
}
