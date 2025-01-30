<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class emergency_index extends Model
{
    //
    //
    protected $table = 'emergency_index';
      // Primary key tidak di-increment otomatis
    public $incrementing = false;
    protected $primaryKey = 'kode_emergency'; // Primary key

    public $timestamps = false;

    protected $fillable = [
        'kode_emergency',
        'nama_emergency',
        'indek',
    ];

     // Relasi ke model Pegawai
     public function pegawai()
     {
         return $this->hasMany(Pegawai::class, 'emergency_index', 'kode_emergency');
     }
}
