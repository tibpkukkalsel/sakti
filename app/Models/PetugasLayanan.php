<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PetugasLayanan extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'petugas_layanan';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'layanan_id',
        'nama_petugas',
        'nip',
        'jabatan',
        'no_hp',
        'username',
        'password',
        'foto',
        'status_aktif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id');
    }

    public function jadwal()
    {
        return $this->hasMany(JadwalPetugasLayanan::class, 'petugas_id');
    }

    // Accessors untuk kompatibilitas sistem presensi & blade
    public function getNikAttribute()
    {
        // NIK presensi merujuk ke ID Layanan (armada) yang diwakilkan
        return (string) $this->attributes['layanan_id'];
    }

    public function getNamaLengkapAttribute()
    {
        return $this->attributes['nama_petugas'];
    }

    public function getNamaLayananAttribute()
    {
        return $this->layanan ? $this->layanan->nama_layanan : 'Layanan';
    }

    public function getKodeCabangAttribute()
    {
        return $this->layanan ? $this->layanan->kode_cabang : null;
    }

    public function getStatusLocationAttribute()
    {
        return $this->layanan ? $this->layanan->status_location : '1';
    }

    public function getStatusJamKerjaAttribute()
    {
        return $this->layanan ? $this->layanan->status_jam_kerja : '1';
    }

    public function getStatusScanAttribute()
    {
        return $this->layanan ? $this->layanan->status_scan : '1';
    }

    public function getKodeDeptAttribute()
    {
        return 'L01';
    }

    public function getPetugasIdAttribute()
    {
        return $this->attributes['id'];
    }
}
