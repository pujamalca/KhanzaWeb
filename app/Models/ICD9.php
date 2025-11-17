<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ICD9 extends Model
{
    protected $table = 'icd9';
    protected $primaryKey = 'kode';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode',
        'deskripsi_panjang',
        'deskripsi_pendek',
    ];

    /**
     * Relationship: ICD9 has many ProsedurPasien
     */
    public function prosedurPasien()
    {
        return $this->hasMany(ProsedurPasien::class, 'kode', 'kode');
    }

    /**
     * Scope: Search by code or description
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('kode', 'like', "%{$search}%")
              ->orWhere('deskripsi_panjang', 'like', "%{$search}%")
              ->orWhere('deskripsi_pendek', 'like', "%{$search}%");
        });
    }
}
