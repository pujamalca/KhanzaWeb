<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PcareRujukan extends Model
{
    protected $table = 'pcare_rujukan';

    protected $primaryKey = 'no_rujukan';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_rujukan',
        'no_kunjungan',
        'no_rawat',
        'tgl_rujukan',
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
        'kd_diagnosa',
        'nm_diagnosa',
        'catatan',
        'results',
        'status_kirim',
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

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->where('tgl_rujukan', $tanggal);
    }

    public function scopeBySpesialis($query, $kdSpesialis)
    {
        return $query->where('kd_spesialis', $kdSpesialis);
    }

    public function scopeToday($query)
    {
        return $query->where('tgl_rujukan', date('Y-m-d'));
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

    public function getTujuanLengkapAttribute(): string
    {
        $parts = [];

        if ($this->nm_ppk) {
            $parts[] = $this->nm_ppk;
        }

        if ($this->nm_spesialis) {
            $parts[] = $this->nm_spesialis;
        }

        if ($this->nm_subspesialis) {
            $parts[] = "({$this->nm_subspesialis})";
        }

        return implode(' - ', $parts);
    }
}
