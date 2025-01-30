<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pegawai extends Model
{
    protected $table = 'pegawai'; // Nama tabel
    protected $primaryKey = 'id'; // Primary key
    public $timestamps = false; // Menonaktifkan timestamps

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

    // Relasi ke tabel jnj_jabatan
    public function jnjJabatan()
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

}
