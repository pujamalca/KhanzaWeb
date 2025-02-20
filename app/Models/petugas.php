<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class petugas extends Model
{
    protected $table = 'petugas';

    public $incrementing = false;

    protected $primaryKey = 'nip';

    public $timestamps = false;

    // Atur tipe data virtual primary key
    protected $keyType = 'string';

    protected $fillable = [
        'nip',
        'nama',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'gol_darah',
        'agama',
        'alamat',
        'no_telp',
        'stts_nikah',
        'kd_jbtn',
        'status',
    ];

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

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nik');
    }

    public function jabatan()
    {
        return $this->belongsTo(jabatan::class, 'kd_jbtn', 'kd_jbtn');
    }
}

