<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenerimaanLintasDaerah extends Model
{
    use HasFactory;

    protected $table = 'penerimaan_lintas_daerah';
    protected $primaryKey = 'id';

    protected $fillable = [
        'penerimaan_id',
        'kabupaten_id',
        'nopol',
        'nama_wp',
        'jenis_kendaraan',
        'nominal',
        'keterangan',
    ];

    protected $casts = [
        'nominal' => 'float',
    ];

    public function penerimaan()
    {
        return $this->belongsTo(RekapPenerimaanPkb::class, 'penerimaan_id');
    }

    public function kabupaten()
    {
        return $this->belongsTo(Kabupaten::class, 'kabupaten_id');
    }
}
