<?php

namespace App\Http\Controllers;

use App\Models\Harilibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class HariliburController extends Controller
{
    public function index()
    {
        $query = Harilibur::query();
        $query->orderBy('tanggal_libur', 'desc')->orderBy('kode_libur', 'desc');
        $harilibur = $query->paginate(10);
        $layanan = DB::table('layanan')->get()->keyBy('id');
        return view('harilibur.index', compact('harilibur', 'layanan'));
    }

    public function create()
    {
        $layanan = DB::table('layanan')->orderBy('id')->get();
        return view('harilibur.create', compact('layanan'));
    }

    public function store(Request $request)
    {
        //LB24001
        $tahun = date('Y', strtotime($request->tanggal_libur));
        $thn = substr($tahun, 2, 2);
        $lastlibur = DB::table('harilibur')
            ->whereRaw('YEAR(tanggal_libur)="' . $tahun . '"')
            ->orderBy('kode_libur', 'desc')
            ->first();
        $lastkodelibur = $lastlibur != null ? $lastlibur->kode_libur : "";
        $format = "LB"  . $thn;
        $kode_libur = buatkode($lastkodelibur, $format, 3);
        $layanan_id = !empty($request->layanan_id) ? $request->layanan_id : 'ALL';

        DB::beginTransaction();
        try {
            DB::table('harilibur')
                ->insert([
                    'kode_libur' => $kode_libur,
                    'tanggal_libur' => $request->tanggal_libur,
                    'kode_cabang' => $layanan_id,
                    'keterangan' => $request->keterangan
                ]);

            if ($layanan_id == 'ALL') {
                $semuaLayanan = DB::table('layanan')->select('id')->get();
                $detail = [];
                foreach ($semuaLayanan as $l) {
                    $detail[] = [
                        'kode_libur' => $kode_libur,
                        'nik' => $l->id
                    ];
                }
                if (!empty($detail)) {
                    DB::table('harilibur_detail')->insert($detail);
                }
            } else {
                DB::table('harilibur_detail')->insert([
                    'kode_libur' => $kode_libur,
                    'nik' => $layanan_id
                ]);
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Hari Libur Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }


    public function edit(Request $request)
    {
        $kode_libur = $request->kode_libur;
        $harilibur = DB::table('harilibur')->where('kode_libur', $kode_libur)->first();
        $layanan = DB::table('layanan')->orderBy('id')->get();
        return view('harilibur.edit', compact('layanan', 'harilibur'));
    }


    public function update(Request $request, $kode_libur)
    {
        $layanan_id = !empty($request->layanan_id) ? $request->layanan_id : 'ALL';
        DB::beginTransaction();
        try {
            DB::table('harilibur')
                ->where('kode_libur', $kode_libur)
                ->update([
                    'tanggal_libur' => $request->tanggal_libur,
                    'kode_cabang' => $layanan_id,
                    'keterangan' => $request->keterangan
                ]);

            // Sync harilibur_detail
            DB::table('harilibur_detail')->where('kode_libur', $kode_libur)->delete();
            if ($layanan_id == 'ALL') {
                $semuaLayanan = DB::table('layanan')->select('id')->get();
                $detail = [];
                foreach ($semuaLayanan as $l) {
                    $detail[] = [
                        'kode_libur' => $kode_libur,
                        'nik' => $l->id
                    ];
                }
                if (!empty($detail)) {
                    DB::table('harilibur_detail')->insert($detail);
                }
            } else {
                DB::table('harilibur_detail')->insert([
                    'kode_libur' => $kode_libur,
                    'nik' => $layanan_id
                ]);
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }

    public function delete($kode_libur)
    {
        DB::beginTransaction();
        try {
            DB::table('harilibur_detail')->where('kode_libur', $kode_libur)->delete();
            DB::table('harilibur')->where('kode_libur', $kode_libur)->delete();
            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => $e->getMessage()]);
        }
    }


    public function setkaryawanlibur($kode_libur)
    {
        $harilibur = DB::table('harilibur')
            ->where('kode_libur', $kode_libur)->first();
        $layanan = DB::table('layanan')->get()->keyBy('id');
        return view('harilibur.setkaryawanlibur', compact('harilibur', 'layanan'));
    }


    public function setlistkaryawanlibur($kode_libur)
    {
        return view('harilibur.setlistkaryawanlibur', compact('kode_libur'));
    }


    public function getsetlistkaryawanlibur($kode_libur)
    {
        $harilibur = DB::table('harilibur')->where('kode_libur', $kode_libur)->first();
        $query = DB::table('layanan')
            ->select('layanan.id as nik', 'layanan.nama_layanan as nama_lengkap', 'layanan.nama_pic as jabatan', 'hariliburdetail.nik as ceknik')
            ->leftJoin(
                DB::raw("(
                SELECT nik FROM harilibur_detail
                WHERE kode_libur = '$kode_libur'
            ) hariliburdetail"),
                function ($join) {
                    $join->on('layanan.id', '=', 'hariliburdetail.nik');
                }
            );

        if ($harilibur && !empty($harilibur->kode_cabang) && $harilibur->kode_cabang != 'ALL') {
            $query->where('layanan.id', $harilibur->kode_cabang);
        }

        $karyawan = $query->orderBy('nama_layanan')->get();
        return view('harilibur.getsetlistkaryawanlibur', compact('karyawan', 'kode_libur'));
    }


    public function storekaryawanlibur(Request $request)
    {
        try {
            $cek = DB::table('harilibur_detail')
                ->where('kode_libur', $request->kode_libur)
                ->where('nik', $request->nik)->count();
            if ($cek > 0) {
                return 1;
            }
            DB::table('harilibur_detail')->insert([
                'kode_libur' => $request->kode_libur,
                'nik' => $request->nik,
            ]);
            return 0;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    public function removekaryawanlibur(Request $request)
    {
        try {
            DB::table('harilibur_detail')
                ->where('kode_libur', $request->kode_libur)
                ->where('nik', $request->nik)
                ->delete();
            return 0;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    public function getkaryawanlibur($kode_libur)
    {
        $karyawanlibur = DB::table('harilibur_detail')
            ->join('layanan', 'harilibur_detail.nik', '=', 'layanan.id')
            ->where('kode_libur', $kode_libur)
            ->select('harilibur_detail.*', 'layanan.nama_layanan as nama_lengkap', 'layanan.nama_pic as jabatan')
            ->get();

        return view('harilibur.getkaryawanlibur', compact('karyawanlibur', 'kode_libur'));
    }
}
