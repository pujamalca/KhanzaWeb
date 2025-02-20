<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class master_berkas_pegawai extends Model
{
    //
    protected $table = 'master_berkas_pegawai';
      // Primary key tidak di-increment otomatis
    public $incrementing = false;
    protected $primaryKey = 'kode'; // Primary key

    public $timestamps = false;

    protected $fillable = [
        'kode',
        'kategori',
        'nama_berkas',
        'no_urut',
    ];

    public static function getEnumValues($column, $table = 'master_berkas_pegawai')
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

    public static function generateNewKode()
        {
            // Ambil kode terakhir dari database
            $lastKode = DB::table('master_berkas_pegawai')->latest('kode')->value('kode');

            // Jika belum ada data, mulai dari MBP0001
            if (!$lastKode) {
                return 'MBP0001';
            }

            // Ambil angka dari kode terakhir (MBPXXXX -> XXXX)
            $lastNumber = (int) substr($lastKode, 3);

            // Tambah 1 ke angka terakhir
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            // Gabungkan dengan prefix MBP
            return 'MBP' . $newNumber;
        }

    public function berkas_pegawai()
    {
        return $this->hasMany(berkas_pegawai::class, 'kode_berkas', 'kode');
    }

}
