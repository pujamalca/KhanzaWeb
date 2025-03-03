<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cacat_fisik extends Model
{
    //
    use HasFactory;

    protected $table = 'nama_cacat'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key tabel
    public $incrementing = true; // Karena id adalah auto-increment
    public $timestamps = false; // Tidak memiliki created_at dan updated_at

    protected $fillable = [
        'nama_cacat',
    ];

    /**
     * Scope untuk mencari suku bangsa berdasarkan nama
     */
    public function scopeCariCacatFisik($query, $nama)
    {
        return $query->where('nama_cacat', 'LIKE', "%{$nama}%");
    }
}

