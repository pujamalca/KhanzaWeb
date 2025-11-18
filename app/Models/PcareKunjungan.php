<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PcareKunjungan extends Model
{
    protected $table = 'pcare_kunjungan';

    protected $primaryKey = 'no_kunjungan';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_kunjungan',
        'no_rawat',
        'tgl_kunjungan',
        'kd_provider',
        'no_kartu',
        'nm_pasien',
        'tgl_daftar',
        'kd_poli',
        'nm_poli',
        'no_urut',
        'kunjungan_sakit',
        'sistole',
        'diastole',
        'beratbadan',
        'tinggibadan',
        'resprate',
        'heartrate',
        'lingkarperut',
        'keluhan',
        'pemeriksaan',
        'kesadaran',
        'kd_diagnosa1',
        'nm_diagnosa1',
        'kd_diagnosa2',
        'nm_diagnosa2',
        'kd_diagnosa3',
        'nm_diagnosa3',
        'therapy',
        'kd_tacc',
        'nm_tacc',
        'alasantacc',
        'kd_tkp',
        'nm_tkp',
        'tgl_pulang',
        'kd_dokter',
        'nm_dokter',
        'kd_status_pulang',
        'nm_status_pulang',
        'tgl_rujuk',
        'kd_ppk',
        'nm_ppk',
        'kd_spesialis',
        'nm_spesialis',
        'kd_subspesialis',
        'nm_subspesialis',
        'kd_sarana',
        'nm_sarana',
        'kd_khusus',
        'nm_khusus',
        'catatan',
        'status_kirim',
        'keterangan',
    ];

    protected $casts = [
        'sistole' => 'decimal:2',
        'diastole' => 'decimal:2',
        'beratbadan' => 'decimal:2',
        'tinggibadan' => 'decimal:2',
        'resprate' => 'decimal:2',
        'heartrate' => 'decimal:2',
        'lingkarperut' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(reg_periksa::class, 'no_rawat', 'no_rawat');
    }

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(PcarePendaftaran::class, 'no_kunjungan', 'no_kunjungan');
    }

    public function tindakan(): HasMany
    {
        return $this->hasMany(PcareTindakan::class, 'no_kunjungan', 'no_kunjungan');
    }

    public function obatDiberikan(): HasMany
    {
        return $this->hasMany(PcareObatDiberikan::class, 'no_kunjungan', 'no_kunjungan');
    }

    public function rujukan(): HasMany
    {
        return $this->hasMany(PcareRujukan::class, 'no_kunjungan', 'no_kunjungan');
    }

    /**
     * Scopes
     */
    public function scopeBelumKirim($query)
    {
        return $query->where('status_kirim', 'Belum');
    }

    public function scopeSudahKirim($query)
    {
        return $query->where('status_kirim', 'Sudah');
    }

    public function scopeGagalKirim($query)
    {
        return $query->where('status_kirim', 'Gagal');
    }

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->where('tgl_kunjungan', $tanggal);
    }

    public function scopeByPoli($query, $kdPoli)
    {
        return $query->where('kd_poli', $kdPoli);
    }

    public function scopeWithRujukan($query)
    {
        return $query->whereNotNull('tgl_rujuk');
    }

    public function scopeToday($query)
    {
        return $query->where('tgl_kunjungan', date('Y-m-d'));
    }

    /**
     * Accessors
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status_kirim) {
            'Sudah' => 'success',
            'Gagal' => 'danger',
            default => 'warning'
        };
    }

    public function getTekananDarahAttribute(): ?string
    {
        if ($this->sistole && $this->diastole) {
            return "{$this->sistole}/{$this->diastole} mmHg";
        }
        return null;
    }

    public function getTkpLabelAttribute(): string
    {
        return match($this->kd_tkp) {
            '10' => 'Rujuk RS',
            '20' => 'Rujuk Puskesmas',
            '40' => 'Rujuk Spesialis',
            '50' => 'Kembali Ke Faskes Perujuk',
            default => $this->nm_tkp ?? 'Unknown'
        };
    }

    /**
     * Check if has rujukan
     */
    public function hasRujukan(): bool
    {
        return !empty($this->tgl_rujuk);
    }

    /**
     * Get complete diagnosa list
     */
    public function getDiagnosaListAttribute(): array
    {
        $diagnosa = [];

        if ($this->kd_diagnosa1) {
            $diagnosa[] = [
                'kode' => $this->kd_diagnosa1,
                'nama' => $this->nm_diagnosa1,
                'prioritas' => 'Primer'
            ];
        }

        if ($this->kd_diagnosa2) {
            $diagnosa[] = [
                'kode' => $this->kd_diagnosa2,
                'nama' => $this->nm_diagnosa2,
                'prioritas' => 'Sekunder'
            ];
        }

        if ($this->kd_diagnosa3) {
            $diagnosa[] = [
                'kode' => $this->kd_diagnosa3,
                'nama' => $this->nm_diagnosa3,
                'prioritas' => 'Tersier'
            ];
        }

        return $diagnosa;
    }
}
