<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class kamar_inap extends Model
{
    //
    protected $table = 'kamar_inap';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'no_rawat',
        'kd_kamar',
        'trf_kamar',
        'diagnosa_awal',
        'diagnosa_akhir',
        'tgl_masuk',
        'jam_masuk',
        'tgl_keluar',
        'jam_keluar',
        'lama',
        'ttl_biaya',
        'stts_pulang',
    ];

    public function reg_periksa(): BelongsTo
    {
        return $this->belongsTo(reg_periksa::class, 'no_rawat', 'no_rawat');
    }

    public function kamar(): BelongsTo
    {
        return $this->belongsTo(kamar::class, 'kd_kamar', 'kd_kamar');
    }
}
