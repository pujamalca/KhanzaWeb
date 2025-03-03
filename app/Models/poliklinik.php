<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class poliklinik extends Model
{
    //

    use HasFactory;

    protected $table = 'poliklinik'; // Nama tabel di database
    protected $primaryKey = 'kd_poli'; // Primary key tabel
    public $incrementing = false; // Primary key bukan auto-increment
    public $timestamps = false; // Tidak ada created_at dan updated_at

    protected $fillable = [
        'kd_poli',
        'nm_poli',
        'registrasi',
        'registrasilama',
        'status',
    ];

    /**
     * Relasi ke tabel RegPeriksa
     */
    public function reg_periksa()
    {
        return $this->hasMany(reg_periksa::class, 'kd_poli', 'kd_poli');
    }

    /**
     * Scope untuk hanya poliklinik yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', '1');
    }
}
