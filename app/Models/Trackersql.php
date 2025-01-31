<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trackersql extends Model
{
    protected $table = 'trackersql';


    // Primary key tidak di-increment otomatis
    public $incrementing = false;

    // protected $primaryKey = null;

    public $timestamps = false;

    // Atur tipe data virtual primary key
    protected $keyType = 'string';

    protected $fillable = [
        'tanggal',
        'sqle',
        'usere',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Scope untuk filter berdasarkan rentang tanggal
    public function scopeFilterByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [
            \Carbon\Carbon::parse($startDate)->startOfDay(),
            \Carbon\Carbon::parse($endDate)->endOfDay(),
        ]);
    }
}
