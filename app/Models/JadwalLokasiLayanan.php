<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalLokasiLayanan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_lokasi_layanan';

    protected $fillable = [
        'layanan_id',
        'hari',
        'kode_cabang',
        'keterangan',
        'status',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id', 'id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }
}

