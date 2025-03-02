<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log as FacadesLog;
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

    return route('filament.resources.berkas-pegawai.download', ['record' => $this->nik, 'filename' => basename($this->berkas)]);
}
    
     // Override delete() agar hanya menghapus berdasarkan nik + kode_berkas
     public function delete()
     {
         return static::where('nik', $this->nik)
             ->where('kode_berkas', $this->kode_berkas)
             ->delete();
     }
 
     // Override untuk Filament agar bisa menemukan record dengan nik + kode_berkas
     public function resolveRouteBinding($value, $field = null)
     {
         return static::where('nik', request()->route('record'))
             ->where('kode_berkas', request()->query('kode_berkas'))
             ->firstOrFail();
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
