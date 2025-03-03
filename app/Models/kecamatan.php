<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kecamatan extends Model
{
    //
    use HasFactory;

    protected $table = 'kecamatan'; // Nama tabel di database
    protected $primaryKey = 'kd_kec'; // Primary key tabel
    public $incrementing = true; // Karena kd_kec adalah integer dengan auto increment
    public $timestamps = false; // Tidak memiliki created_at dan updated_at

    protected $fillable = [
        'kd_kec',
        'nm_kec',
    ];

    /**
     * Scope untuk mencari berdasarkan nama kecamatan
     */
    public function scopeCariKecamatan($query, $nama)
    {
        return $query->where('nm_kec', 'LIKE', "%{$nama}%");
    }
}
