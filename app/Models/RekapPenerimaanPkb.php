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
        'tgl_penerimaan',
        'jumlah_rp_r2',
        'jumlah_rp_r4',
        'total_rp',
        'jumlah_unit_r2',
        'jumlah_unit_r4',
        'total_unit',
        'jumlah_notice',
        'keterangan',
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
    ];

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id', 'id');
    }
}
