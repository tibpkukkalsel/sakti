<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Layanan extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "layanan";
    protected $primaryKey = "id";
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_layanan',
        'username',
        'password',
        'nama_pic',
        'nip_pic',
        'jabatan_pic',
        'no_hp',
        'kode_cabang',
        'foto',
        'status_location',
        'status_jam_kerja',
        'status_scan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Accessors untuk kompatibilitas kode / blade lama yang masih memanggil nik / nama_lengkap / jabatan
    public function getNikAttribute()
    {
        return (string) $this->attributes['id'];
    }

    public function getNamaLengkapAttribute()
    {
        return $this->attributes['nama_layanan'];
    }

    public function getJabatanAttribute()
    {
        return $this->attributes['jabatan_pic'] ?? 'Petugas Layanan';
    }

    public function getKodeDeptAttribute()
    {
        return 'L01';
    }

    public function rekapPenerimaan()
    {
        return $this->hasMany(RekapPenerimaanPkb::class, 'layanan_id', 'id');
    }
}
