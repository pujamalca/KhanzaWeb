<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class dokter extends Model
{
    protected $table = 'dokter';

    public $incrementing = false;

    protected $primaryKey = 'kd_dokter';

    public $timestamps = false;

    // Atur tipe data virtual primary key
    protected $keyType = 'string';

    protected $fillable = [
        'kd_dokter',
        'nm_dokter',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'gol_drh',
        'agama',
        'almt_tgl',
        'no_telp',
        'stts_nikah',
        'kd_sps',
        'alumni',
        'no_ijn_praktek',
        'status',
    ];

    public static function getEnumValues($column, $table = 'dokter')
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

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'kd_dokter', 'nik');
    }

    /**
     * Relasi ke tabel Spesialis berdasarkan kd_sps.
     */
    public function spesialis()
    {
        return $this->belongsTo(spesialis::class, 'kd_sps', 'kd_sps');
    }
}
