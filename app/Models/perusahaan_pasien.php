<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class perusahaan_pasien extends Model
{
    //
    use HasFactory;

    protected $table = 'perusahaan_pasien'; // Nama tabel di database
    protected $primaryKey = 'kode_perusahaan'; // Primary key tabel
    public $incrementing = false; // Karena primary key bukan integer auto-increment
    public $timestamps = false; // Tidak memiliki created_at dan updated_at

    protected $fillable = [
        'kode_perusahaan',
        'nama_perusahaan',
        'alamat',
        'kota',
        'no_telp',
    ];

    /**
     * Scope untuk mencari perusahaan berdasarkan nama
     */
    public function scopeCariPerusahaan($query, $nama)
    {
        return $query->where('nama_perusahaan', 'LIKE', "%{$nama}%");
    }
}
