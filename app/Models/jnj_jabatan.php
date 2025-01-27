<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class jnj_jabatan extends Model
{
    protected $table = 'jnj_jabatan';

    // // // Primary key virtual
    // protected $primaryKey = 'virtual_id';

    // Primary key tidak di-increment otomatis
    public $incrementing = false;

    public $timestamps = false;

    // // Atur tipe data virtual primary key
    // protected $keyType = 'string';

    protected $fillable = [
        'kode',
        'nama',
        'tnj',
        'indek',
    ];

     // Relasi ke model Pegawai
     public function pegawai()
     {
         return $this->hasMany(Pegawai::class, 'jnj_jabatan', 'kode');
     }

    // protected $casts = [
    //     'tgl_login' => 'date',
    // ];


    // // Scope untuk filter berdasarkan tanggal
    // // Scope untuk filter berdasarkan tanggal
    // public function scopeFilterByDate($query, $date)
    // {
    //     return $query->whereDate('tgl_login', $date);
    // }

    // // Scope untuk filter berdasarkan rentang tanggal
    // public function scopeFilterByDateRange($query, $startDate, $endDate)
    // {
    //     return $query->whereBetween('tgl_login', [
    //         \Carbon\Carbon::parse($startDate)->startOfDay(),
    //         \Carbon\Carbon::parse($endDate)->endOfDay(),
    //     ]);
    // }

    // // Buat accessor untuk primary key virtual
    // public function getVirtualIdAttribute()
    // {
    //     return $this->nip . '_' . $this->tgl_login;
    // }
}
