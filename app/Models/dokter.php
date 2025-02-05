<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class dokter extends Model
{
    protected $table = 'dokter';

    public $incrementing = false;

    protected $primaryKey = 'kd_dokter';

    public $timestamps = false;

    // Atur tipe data virtual primary key
    protected $keyType = 'string';

    protected $fillable = [
        'kd_dokter',
        'nm_dokter',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'gol_drh',
        'agama',
        'almt_tgl',
        'no_telp',
        'stts_nikah',
        'kd_sps',
        'alumni',
        'no_ijn_praktek',
        'status',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'kd_dokter', 'nik');
    }

    /**
     * Relasi ke tabel Spesialis berdasarkan kd_sps.
     */
    public function spesialis()
    {
        return $this->belongsTo(spesialis::class, 'kd_sps', 'kd_sps');
    }
}
