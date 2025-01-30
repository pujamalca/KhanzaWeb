<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class resiko_kerja extends Model
{
    //
    protected $table = 'resiko_kerja';
      // Primary key tidak di-increment otomatis
    public $incrementing = false;
    protected $primaryKey = 'kode_resiko'; // Primary key

    public $timestamps = false;

    protected $fillable = [
        'kode_resiko',
        'nama_resiko',
        'indek',
    ];

     // Relasi ke model Pegawai
     public function pegawai()
     {
         return $this->hasMany(Pegawai::class, 'resiko_kerja', 'kode_resiko');
     }
}
