<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    protected $table = 'tracker';

    // Primary key tidak di-increment otomatis
    public $incrementing = false;

    public $timestamps = false;

    // Atur tipe data virtual primary key
    protected $keyType = 'string';

    protected $fillable = [
        'nip',
        'tgl_login',
        'jam_login',
    ];

    protected $casts = [
        'tgl_login' => 'date',
    ];

    // Scope untuk filter berdasarkan rentang tanggal
    public function scopeFilterByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tgl_login', [
            \Carbon\Carbon::parse($startDate)->startOfDay(),
            \Carbon\Carbon::parse($endDate)->endOfDay(),
        ]);
    }
}
