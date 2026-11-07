<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapPenerimaanPkb extends Model
{
    use HasFactory;

    protected $table = 'rekap_penerimaan_pkb';
    protected $primaryKey = 'id';

    protected $fillable = [
        'layanan_id',
        'kode_cabang',
        'tgl_penerimaan',
        'jumlah_rp_r2',
        'jumlah_rp_r4',
        'total_rp',
        'jumlah_unit_r2',
        'jumlah_unit_r4',
        'total_unit',
        'jumlah_notice',
        'keterangan',
        'is_locked',
    ];

    protected $casts = [
        'tgl_penerimaan' => 'date',
        'jumlah_rp_r2' => 'float',
        'jumlah_rp_r4' => 'float',
        'total_rp' => 'float',
        'jumlah_unit_r2' => 'integer',
        'jumlah_unit_r4' => 'integer',
        'total_unit' => 'integer',
        'jumlah_notice' => 'integer',
        'is_locked' => 'boolean',
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id', 'id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }

    public function lokasi()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'kode_cabang');
    }

    public function getIdLokasiAttribute()
    {
        return $this->kode_cabang;
    }

    public function setIdLokasiAttribute($value)
    {
        $this->attributes['kode_cabang'] = $value;
    }
}

