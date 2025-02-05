<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class petugas extends Model
{
    protected $table = 'petugas';

    public $incrementing = false;

    protected $primaryKey = 'nip';

    public $timestamps = false;

    // Atur tipe data virtual primary key
    protected $keyType = 'string';

    protected $fillable = [
        'nip',
        'nama',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'gol_darah',
        'agama',
        'alamat',
        'no_telp',
        'stts_nikah',
        'kd_jbtn',
        'status',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
    }

    public function jabatan()
    {
        return $this->belongsTo(jabatan::class, 'kd_jbtn', 'kd_jbtn');
    }
}

