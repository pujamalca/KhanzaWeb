<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class berkas_pegawai extends Model
{
    //
    //
    protected $table = 'berkas_pegawai';
      // Primary key tidak di-increment otomatis
    public $incrementing = false;
    protected $primaryKey = 'nik'; // Tidak ada primary key

    public $timestamps = false;

    protected $fillable = [
        'nik',
        'tgl_uploud',
        'tgl_berakhir',
        'kode_berkas',
        'berkas',
    ];

    public function getUrlAttribute()
    {
        if (!$this->berkas) {
            return null;
        }

        return route('pegawai.berkas', ['filename' => basename($this->berkas)]);
    }



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
