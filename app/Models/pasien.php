<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class pasien extends Model
{
    //
    use HasFactory;

    protected $table = 'pasien'; // Nama tabel sesuai database

    protected $primaryKey = 'no_rkm_medis'; // Primary key berdasarkan data tabel

    public $incrementing = false; // Karena `no_rkm_medis` bukan auto-increment

    protected $keyType = 'string'; // Primary key bertipe string

    public $timestamps = false; // Tidak menggunakan timestamps otomatis

    protected $fillable = [
        'no_rkm_medis',
        'nm_pasien',
        'no_ktp',
        'jk',
        'tmp_lahir',
        'tgl_lahir',
        'nm_ibu',
        'alamat',
        'gol_darah',
        'pekerjaan',
        'stts_nikah',
        'agama',
        'tgl_daftar',
        'no_tlp',
        'umur',
        'pnd',
        'keluarga',
        'namakeluarga',
        'kd_pj',
        'no_peserta',
        'kd_kel',
        'kd_kec',
        'kd_kab',
        'pekerjaanpj',
        'alamatpj',
        'kelurahanpj',
        'kecamatanpj',
        'kabupatenpj',
        'perusahaan_pasien',
        'suku_bangsa',
        'bahasa_pasien',
        'cacat_fisik',
        'email',
        'nip',
        'kd_prop',
        'propinsipj'
    ];

    /**
     * Relasi ke tabel `penjab` (Penanggung Jawab)
     */
    public function penjab()
    {
        return $this->belongsTo(penjab::class, 'kd_pj', 'kd_pj');
    }

    /**
     * Relasi ke tabel `kelurahan`
     */
    public function kelurahan()
    {
        return $this->belongsTo(kelurahan::class, 'kd_kel', 'kd_kel');
    }

    /**
     * Relasi ke tabel `kecamatan`
     */
    public function kecamatan()
    {
        return $this->belongsTo(kecamatan::class, 'kd_kec', 'kd_kec');
    }

    /**
     * Relasi ke tabel `kabupaten`
     */
    public function kabupaten()
    {
        return $this->belongsTo(kabupaten::class, 'kd_kab', 'kd_kab');
    }

    /**
     * Relasi ke tabel `perusahaan_pasien`
     */
    public function perusahaan()
    {
        return $this->belongsTo(perusahaan_pasien::class, 'perusahaan_pasien', 'kode_perusahaan');
    }

    /**
     * Relasi ke tabel `suku_bangsa`
     */
    public function sukuBangsa()
    {
        return $this->belongsTo(suku_bangsa::class, 'suku_bangsa', 'id');
    }

    /**
     * Relasi ke tabel `bahasa_pasien`
     */
    public function bahasa()
    {
        return $this->belongsTo(bahasa_pasien::class, 'bahasa_pasien', 'id');
    }

    /**
     * Relasi ke tabel `cacat_fisik`
     */
    public function cacatFisik()
    {
        return $this->belongsTo(cacat_fisik::class, 'cacat_fisik', 'id');
    }

    /**
     * Relasi ke tabel `propinsi`
     */
    public function propinsi()
    {
        return $this->belongsTo(propinsi::class, 'kd_prop', 'kd_prop');
    }

    /**
     * Relasi ke tabel `propinsi` untuk alamat penanggung jawab
     */
    public function propinsiPj()
    {
        return $this->belongsTo(Propinsi::class, 'propinsipj', 'kd_prop');
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
