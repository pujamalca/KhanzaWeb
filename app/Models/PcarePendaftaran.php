<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PcarePendaftaran extends Model
{
    protected $table = 'pcare_pendaftaran';

    protected $primaryKey = 'no_rawat';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_rawat',
        'no_urut',
        'no_kunjungan',
        'tgl_daftar',
        'no_kartu',
        'nm_pasien',
        'jkel',
        'tgl_lahir',
        'no_telp',
        'kd_provider',
        'nm_provider',
        'kd_poli',
        'nm_poli',
        'kunjungan_sakit',
        'sistole',
        'diastole',
        'beratbadan',
        'tinggibadan',
        'resprate',
        'heartrate',
        'kdtkp',
        'status_kirim',
        'keterangan',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'beratbadan' => 'decimal:2',
        'tinggibadan' => 'decimal:2',
        'resprate' => 'decimal:2',
        'heartrate' => 'decimal:2',
    ];

    /**
     * Get the registration record
     */
    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(reg_periksa::class, 'no_rawat', 'no_rawat');
    }

    /**
     * Get the kunjungan record
     */
    public function kunjungan(): HasOne
    {
        return $this->hasOne(PcareKunjungan::class, 'no_kunjungan', 'no_kunjungan');
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
        return $query->where('tgl_daftar', $tanggal);
    }

    public function scopeByPoli($query, $kdPoli)
    {
        return $query->where('kd_poli', $kdPoli);
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
}
