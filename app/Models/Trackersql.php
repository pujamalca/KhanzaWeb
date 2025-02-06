<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trackersql extends Model
{
    protected $table = 'trackersql';

    // Gunakan custom virtual primary key
    protected $primaryKey = 'custom_key';

    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'tanggal',
        'sqle',
        'usere',
    ];

    // Tambahkan atribut custom key
    protected $appends = ['custom_key'];

    public function getCustomKeyAttribute(): string
    {
        return (string) implode('_', [$this->tanggal, $this->usere]);
    }

    public function getRouteKeyName()
    {
        return 'custom_key';
    }

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
