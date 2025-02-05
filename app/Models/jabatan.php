<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jabatan extends Model
{

    use HasFactory;

    protected $table = 'jabatan';
    protected $primaryKey = 'kd_jbtn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_jbtn',
        'nm_jbtn',
    ];

    public function petugas()
    {
        return $this->hasMany(petugas::class, 'kd_jbtn', 'kd_jbtn');
    }
}

