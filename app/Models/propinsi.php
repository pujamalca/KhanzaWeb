<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class propinsi extends Model
{
    //
    use HasFactory;

    protected $table = 'propinsi'; // Nama tabel di database
    protected $primaryKey = 'kd_prop'; // Primary key tabel
    public $incrementing = true; // Karena kd_prop adalah integer dengan auto increment
    public $timestamps = false; // Tidak memiliki created_at dan updated_at

    protected $fillable = [
        'kd_prop',
        'nm_prop',
    ];

    /**
     * Scope untuk mencari berdasarkan nama propinsi
     */
    public function scopeCaripropinsi($query, $nama)
    {
        return $query->where('nm_prop', 'LIKE', "%{$nama}%");
    }
}
