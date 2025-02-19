<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class berkas_pegawai extends Model
{
    //
    //
    protected $table = 'berkas_pegawai';
      // Primary key tidak di-increment otomatis
    public $incrementing = false;
    protected $primaryKey = null; // Tidak ada primary key

    public $timestamps = false;

    protected $fillable = [
        'nik',
        'tgl_uploud',
        'kode_berkas',
        'berkas',
    ];

     // Relasi ke model Pegawai
     public function pegawai()
     {
         return $this->belongsTo(Pegawai::class, 'nik', 'nik');
     }
     // Relasi ke model Master Berkas
     public function master_berkas_pegawai()
     {
         return $this->belongsTo(master_berkas_pegawai::class, 'kode_berkas', 'kode');
     }
}
