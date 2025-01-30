<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class stts_wp extends Model
{
    //
    protected $table = 'stts_wp';
     // Primary key tidak di-increment otomatis
   public $incrementing = false;
   protected $primaryKey = 'stts'; // Primary key

   public $timestamps = false;

   protected $fillable = [
       'stts',
       'ktg',
   ];

    // Relasi ke model Pegawai
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'stts_wp', 'stts');
    }
}
