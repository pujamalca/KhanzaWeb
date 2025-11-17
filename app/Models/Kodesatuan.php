<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kodesatuan extends Model
{
    protected $table = 'kodesatuan';
    protected $primaryKey = 'kode_sat';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_sat',
        'satuan',
    ];

    /**
     * Relationship: Kodesatuan has many Databarang
     */
    public function databarang()
    {
        return $this->hasMany(Databarang::class, 'kd_sat', 'kode_sat');
    }
}
