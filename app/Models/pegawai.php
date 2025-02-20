<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pegawai extends Model
{
    protected $table = 'pegawai';
    protected $primaryKey = 'id';
    public $incrementing = true; // Pastikan ini true
    protected $keyType = 'int'; // Pastikan ini int
    public $timestamps = false; // Jika tidak menggunakan timestamps

    protected $attributes = [
        'pengurang' => 0,
        'indek' => 0,
        'cuti_diambil' => 0,
        'dankes' => 0,
        'npwp' => '00',
    ];



    protected $casts = [
        'wajibmasuk' => 'string',
    ];




    // Kolom yang boleh diisi (mass assignment)
    protected $fillable = [
        'nik',
        'nama',
        'jk',
        'jbtn',
        'jnj_jabatan',
        'kode_kelompok',
        'kode_resiko',
        'kode_emergency',
        'departemen',
        'bidang',
        'stts_wp',
        'stts_kerja',
        'npwp',
        'pendidikan',
        'gapok',
        'tmp_lahir',
        'tgl_lahir',
        'alamat',
        'kota',
        'mulai_kerja',
        'ms_kerja',
        'indexins',
        'bpd',
        'rekening',
        'stts_aktif',
        'wajibmasuk',
        'pengurang',
        'indek',
        'mulai_kontrak',
        'cuti_diambil',
        'dankes',
        'photo',
        'no_ktp',
    ];


public static function getEnumValues($column, $table = 'pegawai')
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


    // Method untuk mendapatkan URL foto melalui route khusus
    public function getPhotoUrl()
    {
        return route('pegawai.photo', ['filename' => $this->photo]);
    }

    // Relasi ke tabel jnj_jabatan
    public function jnj_jabatan()
    {
        return $this->belongsTo(jnj_jabatan::class, 'jnj_jabatan', 'kode');
    }

    // Relasi ke tabel kelompok_jabatan
    public function kelompok_jabatan()
    {
        return $this->belongsTo(kelompok_jabatan::class, 'kelompok_jabatan', 'kode_jabatan');
    }

    // Relasi ke tabel kode_resiko
    public function resiko_kerja()
    {
        return $this->belongsTo(resiko_kerja::class, 'resiko_kerja', 'kode_resiko');
    }

    // Relasi ke tabel kode_emergency
    public function emergency_index()
    {
        return $this->belongsTo(emergency_index::class, 'emergency_index', 'kode_emergency');
    }

    // Relasi ke tabel departemen
    public function departemen()
    {
        return $this->belongsTo(departemen::class, 'departemen', 'dep_id');
    }

    // Relasi ke tabel bidang
    public function bidang()
    {
        return $this->belongsTo(bidang::class, 'bidang', 'nama');
    }

    // Relasi ke tabel stts_wp
    public function stts_wp()
    {
        return $this->belongsTo(stts_wp::class, 'stts_wp', 'stts');
    }

    // Relasi ke tabel stts_kerja
    public function stts_kerja()
    {
        return $this->belongsTo(stts_kerja::class, 'stts_kerja', 'stts');
    }

    // Relasi ke tabel stts_kerja
    public function pendidikan()
    {
        return $this->belongsTo(pendidikan::class, 'pendidikan', 'tingkat');
    }

    // Relasi ke tabel stts_kerja
    public function bank()
    {
        return $this->belongsTo(bank::class, 'bank', 'namabank');
    }


public function dokter()
    {
        return $this->hasOne(Dokter::class, 'kd_dokter', 'nik');
    }

public function petugas()
    {
        return $this->hasOne(petugas::class, 'nip', 'nik');
    }

public function berkas_pegawai()
    {
        return $this->hasMany(berkas_pegawai::class, 'nik', 'nik');
    }


}
