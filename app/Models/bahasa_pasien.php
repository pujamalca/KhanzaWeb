<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bahasa_pasien extends Model
{
    //
    use HasFactory;

    protected $table = 'nama_bahasa'; // Nama tabel di database
    protected $primaryKey = 'id'; // Primary key tabel
    public $incrementing = true; // Karena id adalah auto-increment
    public $timestamps = false; // Tidak memiliki created_at dan updated_at

    protected $fillable = [
        'nama_bahasa',
    ];

    /**
     * Scope untuk mencari suku bangsa berdasarkan nama
     */
    public function scopeCariBahasaPasien($query, $nama)
    {
        return $query->where('nama_bahasa', 'LIKE', "%{$nama}%");
    }
}
