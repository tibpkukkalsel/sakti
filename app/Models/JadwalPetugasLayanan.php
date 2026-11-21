<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPetugasLayanan extends Model
{
    use HasFactory;

    protected $table = 'jadwal_petugas_layanan';
    protected $primaryKey = 'id';

    protected $fillable = [
        'layanan_id',
        'petugas_id',
        'hari',
        'tanggal',
        'status',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function petugas()
    {
        return $this->belongsTo(PetugasLayanan::class, 'petugas_id');
    }
}
