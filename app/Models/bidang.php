<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bidang extends Model
{
    //
     //
     protected $table = 'bidang';
     // Primary key tidak di-increment otomatis
   public $incrementing = false;
   protected $primaryKey = 'nama'; // Primary key

   public $timestamps = false;

   protected $fillable = [
       'nama',
   ];

    // Relasi ke model Pegawai
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'bidang', 'nama');
    }
}
