<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kabupaten extends Model
{
    //
    use HasFactory;

    protected $table = 'kabupaten'; // Nama tabel di database
    protected $primaryKey = 'kd_kab'; // Primary key tabel
    public $incrementing = true; // Karena kd_kab adalah integer dengan auto increment
    public $timestamps = false; // Tidak memiliki created_at dan updated_at

    protected $fillable = [
        'kd_kab',
        'nm_kab',
    ];

    /**
     * Scope untuk mencari berdasarkan nama kabupaten
     */
    public function scopeCarikabupaten($query, $nama)
    {
        return $query->where('nm_kab', 'LIKE', "%{$nama}%");
    }
}
