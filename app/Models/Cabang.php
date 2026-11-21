<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $table = 'cabang';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'nama_cabang',
        'lokasi_cabang',
        'radius_cabang',
    ];

    public function jadwalLokasi()
    {
        return $this->hasMany(JadwalLokasiLayanan::class, 'kode_cabang', 'id');
    }

    public function layanan()
    {
        return $this->hasMany(Layanan::class, 'kode_cabang', 'id');
    }
}

