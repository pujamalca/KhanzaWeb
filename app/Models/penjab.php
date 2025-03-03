<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class penjab extends Model
{
    //

    use HasFactory;

    protected $table = 'penjab'; // Nama tabel di database
    protected $primaryKey = 'kd_pj'; // Primary key tabel
    public $incrementing = false; // Primary key bukan auto-increment
    public $timestamps = false; // Tidak ada created_at dan updated_at

    protected $fillable = [
        'kd_pj',
        'png_jawab',
        'nama_perusahaan',
        'alamat_asuransi',
        'no_telp',
        'attn',
        'status',
    ];

    /**
     * Relasi ke tabel RegPeriksa
     */
    public function reg_periksa()
    {
        return $this->hasMany(reg_periksa::class, 'kd_pj', 'kd_pj');
    }

    /**
     * Scope untuk hanya penjamin yang aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', '1');
    }
}
