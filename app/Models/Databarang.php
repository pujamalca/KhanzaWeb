<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Databarang extends Model
{
    protected $table = 'databarang';
    protected $primaryKey = 'kode_brng';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_brng',
        'nama_brng',
        'kd_sat',
        'letak',
        'dasar',
        'h_beli',
        'ralan',
        'kelas1',
        'kelas2',
        'kelas3',
        'utama',
        'vip',
        'vvip',
        'beliluar',
        'jualbebas',
        'stok',
        'stok_minimum',
        'kapasitas',
        'kd_jenis',
        'isi',
        'expire',
        'status',
    ];

    protected $casts = [
        'dasar' => 'decimal:2',
        'h_beli' => 'decimal:2',
        'ralan' => 'decimal:2',
        'kelas1' => 'decimal:2',
        'kelas2' => 'decimal:2',
        'kelas3' => 'decimal:2',
        'utama' => 'decimal:2',
        'vip' => 'decimal:2',
        'vvip' => 'decimal:2',
        'beliluar' => 'decimal:2',
        'jualbebas' => 'decimal:2',
        'stok' => 'decimal:2',
        'kapasitas' => 'decimal:2',
        'expire' => 'date',
    ];

    /**
     * Relationship: Databarang belongs to Kodesatuan
     */
    public function satuan()
    {
        return $this->belongsTo(Kodesatuan::class, 'kd_sat', 'kode_sat');
    }

    /**
     * Relationship: Databarang belongs to Jenis
     */
    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'kd_jenis', 'kd_jenis');
    }

    /**
     * Relationship: Databarang has many DetailPemberianObat
     */
    public function detailPemberianObat()
    {
        return $this->hasMany(DetailPemberianObat::class, 'kode_brng', 'kode_brng');
    }

    /**
     * Relationship: Databarang has many ResepDokter
     */
    public function resepDokter()
    {
        return $this->hasMany(ResepDokter::class, 'kode_brng', 'kode_brng');
    }

    /**
     * Relationship: Databarang has many DetailPemberianObat
     */
    public function detailPemberianObat()
    {
        return $this->hasMany(DetailPemberianObat::class, 'kode_brng', 'kode_brng');
    }

    /**
     * Scope: Active items only
     */
    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }

    /**
     * Scope: Low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stok < stok_minimum');
    }

    /**
     * Scope: Expired or near expiry
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expire')
                     ->where('expire', '<=', Carbon::now()->addDays($days));
    }

    /**
     * Scope: Search by code or name
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('kode_brng', 'like', "%{$search}%")
              ->orWhere('nama_brng', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Filter by jenis
     */
    public function scopeJenis($query, $kd_jenis)
    {
        if ($kd_jenis) {
            return $query->where('kd_jenis', $kd_jenis);
        }
        return $query;
    }

    /**
     * Check if stock is low
     */
    public function isLowStock(): bool
    {
        return $this->stok < $this->stok_minimum;
    }

    /**
     * Check if item is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expire) {
            return false;
        }
        return Carbon::parse($this->expire)->isPast();
    }

    /**
     * Check if item is expiring soon
     */
    public function isExpiringSoon($days = 30): bool
    {
        if (!$this->expire) {
            return false;
        }
        return Carbon::parse($this->expire)->isBefore(Carbon::now()->addDays($days));
    }
}
