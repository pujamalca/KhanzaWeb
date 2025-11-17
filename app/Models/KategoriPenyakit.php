<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPenyakit extends Model
{
    protected $table = 'kategori_penyakit';
    protected $primaryKey = 'kd_ktg';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kd_ktg',
        'nm_kategori',
        'ciri_umum',
    ];

    /**
     * Relationship: KategoriPenyakit has many Penyakit
     */
    public function penyakit()
    {
        return $this->hasMany(Penyakit::class, 'kd_ktg', 'kd_ktg');
    }

    /**
     * Get count of diseases in this category
     */
    public function getPenyakitCountAttribute()
    {
        return $this->penyakit()->count();
    }
}
