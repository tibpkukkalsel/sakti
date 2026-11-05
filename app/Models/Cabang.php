<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $table = 'cabang';
    protected $primaryKey = 'kode_cabang';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_cabang',
        'nama_cabang',
        'lokasi_cabang',
        'radius_cabang',
    ];

    public function jadwalLokasi()
    {
        return $this->hasMany(JadwalLokasiLayanan::class, 'kode_cabang', 'kode_cabang');
    }

    public function layanan()
    {
        return $this->hasMany(Layanan::class, 'kode_cabang', 'kode_cabang');
    }
}

