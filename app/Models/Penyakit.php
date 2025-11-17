<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penyakit extends Model
{
    protected $table = 'penyakit';
    protected $primaryKey = 'kd_penyakit';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kd_penyakit',
        'nm_penyakit',
        'ciri_ciri',
        'keterangan',
        'kd_ktg',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Relationship: Penyakit belongs to KategoriPenyakit
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriPenyakit::class, 'kd_ktg', 'kd_ktg');
    }

    /**
     * Relationship: Penyakit has many DiagnosaPasien
     */
    public function diagnosaPasien()
    {
        return $this->hasMany(DiagnosaPasien::class, 'kd_penyakit', 'kd_penyakit');
    }

    /**
     * Scope: Search by ICD-10 code or disease name
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('kd_penyakit', 'like', "%{$search}%")
              ->orWhere('nm_penyakit', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Filter by status
     */
    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope: Filter by category
     */
    public function scopeKategori($query, $kd_ktg)
    {
        if ($kd_ktg) {
            return $query->where('kd_ktg', $kd_ktg);
        }
        return $query;
    }
}
