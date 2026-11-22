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
        'no_awal_notice',
        'foto_awal_notice',
        'no_akhir_notice',
        'foto_akhir_notice',
        'status_verifikasi',
        'catatan_verifikasi',
        'verified_at',
        'verified_by',
        'keterangan',
        'input_by_petugas_id',
    ];

    protected $casts = [
        'tgl_penerimaan' => 'date',
        'verified_at' => 'datetime',
        'jumlah_rp_r2' => 'float',
        'jumlah_rp_r4' => 'float',
        'total_rp' => 'float',
        'jumlah_unit_r2' => 'integer',
        'jumlah_unit_r4' => 'integer',
        'total_unit' => 'integer',
        'jumlah_notice' => 'integer',
    ];

    public function getFotoAwalUrlAttribute()
    {
        if (empty($this->foto_awal_notice)) {
            return null;
        }
        $filename = $this->foto_awal_notice;
        $path = public_path('uploads/notice/' . $filename);
        $v = file_exists($path) ? '?v=' . filemtime($path) : '?v=' . time();
        if (file_exists($path)) {
            return asset('uploads/notice/' . $filename) . $v;
        }
        return asset('storage/uploads/notice/' . $filename) . $v;
    }

    public function getFotoAkhirUrlAttribute()
    {
        if (empty($this->foto_akhir_notice)) {
            return null;
        }
        $filename = $this->foto_akhir_notice;
        $path = public_path('uploads/notice/' . $filename);
        $v = file_exists($path) ? '?v=' . filemtime($path) : '?v=' . time();
        if (file_exists($path)) {
            return asset('uploads/notice/' . $filename) . $v;
        }
        return asset('storage/uploads/notice/' . $filename) . $v;
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, 'layanan_id', 'id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Cabang::class, 'kode_cabang', 'id');
    }

    public function getIdLokasiAttribute()
    {
        return $this->kode_cabang;
    }

    public function setIdLokasiAttribute($value)
    {
        $this->attributes['kode_cabang'] = $value;
    }

    public function lintasDaerah()
    {
        return $this->hasMany(PenerimaanLintasDaerah::class, 'penerimaan_id');
    }

    public function getTotalUnitLintasAttribute()
    {
        return $this->relationLoaded('lintasDaerah') ? $this->lintasDaerah->count() : $this->lintasDaerah()->count();
    }

    public function getTotalRpLintasAttribute()
    {
        return $this->relationLoaded('lintasDaerah') ? (float) $this->lintasDaerah->sum('nominal') : (float) $this->lintasDaerah()->sum('nominal');
    }

    public function petugas()
    {
        return $this->belongsTo(PetugasLayanan::class, 'input_by_petugas_id');
    }

    public function getInputByNamaAttribute()
    {
        return $this->petugas->nama_petugas ?? null;
    }

    public function getIsLockedAttribute()
    {
        return ($this->status_verifikasi == 'disetujui');
    }

    public function getNamaLayananAttribute()
    {
        return $this->layanan->nama_layanan ?? null;
    }

    public function getNamaLokasiAttribute()
    {
        return $this->cabang->nama_cabang ?? null;
    }
}

