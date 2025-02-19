<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class master_berkas_pegawai extends Model
{
    //
    protected $table = 'master_berkas_pegawai';
      // Primary key tidak di-increment otomatis
    public $incrementing = false;
    protected $primaryKey = 'kode'; // Primary key

    public $timestamps = false;

    protected $fillable = [
        'kode',
        'kategori',
        'nama_berkas',
        'no_urut',
    ];

    public function berkas_pegawai()
    {
        return $this->hasMany(berkas_pegawai::class, 'kode_berkas', 'kode');
    }

}
