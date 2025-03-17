<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ranap_gabung extends Model
{
    //
    protected $table = 'ranap_gabung';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    public $timestamps = false;

    public function reg_periksa()
    {
        return $this->belongsTo(reg_periksa::class, 'no_rawat', 'no_rawat');
    }

    public function pasien()
{
    return $this->belongsTo(\App\Models\reg_periksa::class, 'no_rawat2', 'no_rawat')
        ->with('pasien'); // agar nama bayi langsung tersedia
}
}
