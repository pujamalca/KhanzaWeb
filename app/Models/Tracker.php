<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompositePrimaryKey;

class Tracker extends Model
{
    use HasCompositePrimaryKey;

    protected $table = 'tracker';

    // Tetapkan primary key menjadi custom string key
    protected $primaryKey = 'custom_key';

    public $incrementing = false;
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nip',
        'tgl_login',
        'jam_login',
    ];

    // Tambahkan atribut custom key
    protected $appends = ['custom_key'];

    public function getCustomKeyAttribute(): string
    {
        return (string) implode('_', [$this->nip, $this->tgl_login, $this->jam_login]);
    }

    public function getRouteKeyName()
    {
        return 'custom_key';
    }

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
