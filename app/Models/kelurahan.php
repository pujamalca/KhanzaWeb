<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kelurahan extends Model
{
    //
    use HasFactory;

    protected $table = 'kelurahan'; // Nama tabel di database
    protected $primaryKey = 'kd_kel'; // Primary key tabel
    public $incrementing = true; // Karena kd_kel adalah integer dengan auto increment
    public $timestamps = false; // Tidak memiliki created_at dan updated_at

    protected $fillable = [
        'kd_kel',
        'nm_kel',
    ];

    /**
     * Scope untuk mencari berdasarkan nama kelurahan
     */
    public function scopeCariKelurahan($query, $nama)
    {
        return $query->where('nm_kel', 'LIKE', "%{$nama}%");
    }
}
