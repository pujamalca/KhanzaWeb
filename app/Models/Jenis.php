<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
{
    protected $table = 'jenis';
    protected $primaryKey = 'kd_jenis';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kd_jenis',
        'nama',
    ];

    /**
     * Relationship: Jenis has many Databarang
     */
    public function databarang()
    {
        return $this->hasMany(Databarang::class, 'kd_jenis', 'kd_jenis');
    }

    /**
     * Get count of items in this category
     */
    public function getDatabarangCountAttribute()
    {
        return $this->databarang()->count();
    }
}
