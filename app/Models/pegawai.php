<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pegawai extends Model
{
    protected $table = 'pegawai'; // Nama tabel
    protected $primaryKey = 'id'; // Primary key
    public $timestamps = false; // Menonaktifkan timestamps
    // Primary key tidak di-increment otomatis
    public $incrementing = false;

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

}
