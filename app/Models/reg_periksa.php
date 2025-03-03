<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class reg_periksa extends Model
{
    //

    use HasFactory;

    protected $table = 'reg_periksa';

    public $incrementing = false;

    protected $primaryKey = 'no_rawat';

    public $timestamps = false;

    // Atur tipe data virtual primary key
    protected $keyType = 'string';

    protected $fillable = [
        'no_reg',
        'no_rawat',
        'tgl_registrasi',
        'jam_reg',
        'kd_dokter',
        'no_rkm_medis',
        'kd_poli',
        'p_jawab',
        'almt_pj',
        'hubunganpj',
        'biaya_reg',
        'stts',
        'stts_daftar',
        'status_lanjut',
        'kd_pj',
        'umurdaftar',
        'sttsumur',
        'status_bayar',
        'status_poli',
    ];

    /**
     * Relasi ke tabel dokter
     */
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    /**
     * Relasi ke tabel pasien
     */
    public function pasien()
    {
        return $this->belongsTo(pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }

    /**
     * Relasi ke tabel poliklinik
     */
    public function poliklinik()
    {
        return $this->belongsTo(poliklinik::class, 'kd_poli', 'kd_poli');
    }

    /**
     * Relasi ke tabel penanggung jawab (penjab)
     */
    public function penjab()
    {
        return $this->belongsTo(penjab::class, 'kd_pj', 'kd_pj');
    }

    public static function getEnumValues($column, $table = 'petugas')
    {
        // Ambil informasi kolom dari database
        $result = DB::select("SHOW COLUMNS FROM `$table` WHERE Field = ?", [$column]);

        if (!isset($result[0]->Type)) {
            return []; // Jika tidak ditemukan, kembalikan array kosong
        }

        // Ambil tipe ENUM dari database
        preg_match('/^enum\((.*)\)$/', $result[0]->Type, $matches);
        if (!isset($matches[1])) {
            return []; // Jika bukan ENUM, kembalikan array kosong
        }

        // Parse nilai ENUM
        $enumValues = array_map(fn($value) => trim($value, "'"), explode(',', $matches[1]));

        // Kembalikan dalam format ['value' => 'Label']
        return array_combine($enumValues, array_map('ucwords', $enumValues));
    }
}
