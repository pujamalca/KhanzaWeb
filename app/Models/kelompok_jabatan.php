<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kelompok_jabatan extends Model
{
    //
    protected $table = 'kelompok_jabatan';
      // Primary key tidak di-increment otomatis
    public $incrementing = false;
    protected $primaryKey = 'kode_kelompok'; // Primary key

    public $timestamps = false;

    protected $fillable = [
        'kode_kelompok',
        'nama_kelompok',
        'indek',
    ];

     // Relasi ke model Pegawai
     public function pegawai()
     {
         return $this->hasMany(Pegawai::class, 'kelompok_jabatan', 'kode_kelompok');
     }
}
