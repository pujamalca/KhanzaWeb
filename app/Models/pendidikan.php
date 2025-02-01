<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pendidikan extends Model
{
    //
    protected $table = 'pendidikan';
     // Primary key tidak di-increment otomatis
   public $incrementing = false;
   protected $primaryKey = 'tingkat'; // Primary key

   public $timestamps = false;

   protected $fillable = [
       'tingkat',
       'indek',
       'gapok1',
       'kenaikan',
       'maksimal',
   ];

    // Relasi ke model Pegawai
    public function pegawai()
    {
        return $this->hasMany(pegawai::class, 'pendidikan', 'tingkat');
    }
}
