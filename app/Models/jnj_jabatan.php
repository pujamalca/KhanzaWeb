<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class jnj_jabatan extends Model
{
    protected $table = 'jnj_jabatan';

    public $timestamps = false;

    protected $fillable = [
        'kode',
        'nama',
        'tnj',
        'indek',
    ];

     // Relasi ke model Pegawai
     public function pegawai()
     {
         return $this->hasMany(Pegawai::class, 'jnj_jabatan', 'kode');
     }
}
