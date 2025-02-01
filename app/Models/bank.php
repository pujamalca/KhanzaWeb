<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bank extends Model
{
    protected $table = 'bank';
     // Primary key tidak di-increment otomatis
   public $incrementing = false;
   protected $primaryKey = 'namabank'; // Primary key

   public $timestamps = false;

   protected $fillable = [
       'namabank',
   ];

    // Relasi ke model Pegawai
    public function pegawai()
    {
        return $this->hasMany(pegawai::class, 'bank', 'namabank');
    }
}
