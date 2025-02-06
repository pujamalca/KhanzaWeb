<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKey;

class Tracker extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'tracker';

    // Set primary key menjadi array
    protected $primaryKey = ['nip', 'tgl_login', 'jam_login'];

    public $incrementing = false;
    public $timestamps = false;
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
