<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class suku_bangsa extends Model
{
    //
    use HasFactory;

    protected $table = 'suku_bangsa'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key tabel
    public $incrementing = true; // Karena id adalah auto-increment
    public $timestamps = false; // Tidak memiliki created_at dan updated_at

    protected $fillable = [
        'nama_suku_bangsa',
    ];

    /**
     * Scope untuk mencari suku bangsa berdasarkan nama
     */
    public function scopeCariSukuBangsa($query, $nama)
    {
        return $query->where('nama_suku_bangsa', 'LIKE', "%{$nama}%");
    }
}
