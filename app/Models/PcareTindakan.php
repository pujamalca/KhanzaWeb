<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PcareTindakan extends Model
{
    protected $table = 'pcare_tindakan';

    protected $fillable = [
        'no_kunjungan',
        'no_rawat',
        'kd_tindakan',
        'nm_tindakan',
        'biaya',
        'keterangan',
        'results',
        'status_kirim',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(PcareKunjungan::class, 'no_kunjungan', 'no_kunjungan');
    }

    public function regPeriksa(): BelongsTo
    {
        return $this->belongsTo(reg_periksa::class, 'no_rawat', 'no_rawat');
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
