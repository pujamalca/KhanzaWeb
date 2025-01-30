<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class departemen extends Model
{
    //
    //
    protected $table = 'departemen';
      // Primary key tidak di-increment otomatis
    public $incrementing = false;
    protected $primaryKey = 'dep_id'; // Primary key

    public $timestamps = false;

    protected $fillable = [
        'dep_id',
        'nama',
    ];

     // Relasi ke model Pegawai
     public function pegawai()
     {
         return $this->hasMany(Pegawai::class, 'departemen', 'dep_id');
     }
}
