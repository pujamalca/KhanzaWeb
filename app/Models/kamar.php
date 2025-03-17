<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class kamar extends Model
{
    protected $table = 'kamar';
    protected $primaryKey = 'kd_kamar';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_kamar',
        'kd_bangsal',
        'trf_kamar',
        'status',
        'kelas',
        'statusdata',
    ];

    public function bangsal(): BelongsTo
    {
        return $this->belongsTo(bangsal::class, 'kd_bangsal', 'kd_bangsal');
    }

    public function kamar_inap(): HasMany
    {
        return $this->hasMany(kamar_inap::class, 'kd_kamar', 'kd_kamar');
    }
}
