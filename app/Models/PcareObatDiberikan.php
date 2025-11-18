<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PcareObatDiberikan extends Model
{
    protected $table = 'pcare_obat_diberikan';

    protected $fillable = [
        'no_kunjungan',
        'no_rawat',
        'kd_obat',
        'nm_obat',
        'signa1',
        'signa2',
        'jml_obat',
        'jns_obat',
        'nmrckn',
        'aturan_pakai',
        'keterangan',
        'results',
        'status_kirim',
    ];

    protected $casts = [
        'signa1' => 'decimal:2',
        'signa2' => 'decimal:2',
        'jml_obat' => 'decimal:2',
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

    public function scopeRacikan($query)
    {
        return $query->where('jns_obat', '24');
    }

    public function scopeNonRacikan($query)
    {
        return $query->where('jns_obat', '23');
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

    public function getSignaLengkapAttribute(): string
    {
        return "{$this->signa1} x {$this->signa2}";
    }

    public function getJenisObatLabelAttribute(): string
    {
        return match($this->jns_obat) {
            '23' => 'Non Racikan',
            '24' => 'Racikan',
            default => 'Unknown'
        };
    }
}
