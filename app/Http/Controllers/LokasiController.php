<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LokasiController extends Controller
{
    public function index()
    {
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        return view('lokasi.index', compact('cabang'));
    }

    public function store(Request $request)
    {
        $kode_cabang = strtoupper(trim($request->kode_cabang));
        $nama_cabang = trim($request->nama_cabang);
        $lokasi_cabang = trim($request->lokasi_cabang);
        $radius_cabang = $request->radius_cabang ?: 100;

        try {
            $data = [
                'kode_cabang' => $kode_cabang,
                'nama_cabang' => $nama_cabang,
                'lokasi_cabang' => $lokasi_cabang,
                'radius_cabang' => $radius_cabang
            ];
            DB::table('cabang')->insert($data);
            return Redirect::back()->with(['success' => 'Data Lokasi Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Lokasi Gagal Disimpan: ' . $e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        return view('lokasi.edit', compact('cabang'));
    }

    public function update(Request $request)
    {
        $kode_cabang = $request->kode_cabang;
        $nama_cabang = trim($request->nama_cabang);
        $lokasi_cabang = trim($request->lokasi_cabang);
        $radius_cabang = $request->radius_cabang ?: 100;

        try {
            $data = [
                'nama_cabang' => $nama_cabang,
                'lokasi_cabang' => $lokasi_cabang,
                'radius_cabang' => $radius_cabang
            ];
            DB::table('cabang')
                ->where('kode_cabang', $kode_cabang)
                ->update($data);
            return Redirect::back()->with(['success' => 'Data Lokasi Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Lokasi Gagal Diupdate: ' . $e->getMessage()]);
        }
    }

    public function delete($kode_cabang)
    {
        $hapus = DB::table('cabang')->where('kode_cabang', $kode_cabang)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Lokasi Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Lokasi Gagal Dihapus']);
        }
    }
}
