<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class bangsal extends Model
{
    //
    protected $table = 'bangsal';
    protected $primaryKey = 'kd_bangsal';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kd_bangsal',
        'nm_bangsal',
        'status',
    ];

    public function kamar(): HasMany
    {
        return $this->hasMany(kamar::class, 'kd_bangsal', 'kd_bangsal');
    }
}
