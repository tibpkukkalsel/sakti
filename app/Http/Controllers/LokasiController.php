<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class LokasiController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('cabang');
        if (!empty($request->nama_cabang)) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_cabang', 'like', '%' . $request->nama_cabang . '%')
                  ->orWhere('lokasi_cabang', 'like', '%' . $request->nama_cabang . '%');
            });
        }
        $cabang = $query->orderBy('id', 'asc')->paginate(10)->withQueryString();
        return view('lokasi.index', compact('cabang'));
    }

    public function store(Request $request)
    {
        $nama_cabang = trim($request->nama_cabang);
        $lokasi_cabang = trim($request->lokasi_cabang);
        $radius_cabang = $request->radius_cabang ?: 100;

        try {
            $data = [
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
        $id = $request->id;
        $cabang = DB::table('cabang')->where('id', $id)->first();
        return view('lokasi.edit', compact('cabang'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $nama_cabang = trim($request->nama_cabang);
        $lokasi_cabang = trim($request->lokasi_cabang);
        $radius_cabang = $request->radius_cabang ?: 100;

        try {
            $data = [
                'nama_cabang' => $nama_cabang,
                'lokasi_cabang' => $lokasi_cabang,
                'radius_cabang' => $radius_cabang
            ];
            DB::table('cabang')->where('id', $id)->update($data);
            return Redirect::back()->with(['success' => 'Data Lokasi Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Lokasi Gagal Diupdate: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $hapus = DB::table('cabang')->where('id', $id)->delete();
        if ($hapus) {
            return Redirect::back()->with(['success' => 'Data Lokasi Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Lokasi Gagal Dihapus']);
        }
    }
}

