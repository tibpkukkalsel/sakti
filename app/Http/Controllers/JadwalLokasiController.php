<?php

namespace App\Http\Controllers;

use App\Models\JadwalLokasiLayanan;
use App\Models\Layanan;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class JadwalLokasiController extends Controller
{
    protected $listHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    /**
     * Tampilan Admin: Kelola Jadwal Lokasi Layanan
     */
    public function index(Request $request)
    {
        $query = DB::table('jadwal_lokasi_layanan')
            ->join('layanan', 'jadwal_lokasi_layanan.layanan_id', '=', 'layanan.id')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select(
                'jadwal_lokasi_layanan.*',
                'layanan.nama_layanan',
                'layanan.nama_pic',
                'cabang.nama_cabang',
                'cabang.lokasi_cabang',
                'cabang.radius_cabang'
            );

        if ($request->filled('layanan_id')) {
            $query->where('jadwal_lokasi_layanan.layanan_id', $request->layanan_id);
        }

        if ($request->filled('hari')) {
            $query->where('jadwal_lokasi_layanan.hari', $request->hari);
        }

        if ($request->filled('kode_cabang')) {
            $query->where('jadwal_lokasi_layanan.kode_cabang', $request->kode_cabang);
        }

        // Urutkan berdasarkan Layanan dan Urutan Hari (Senin - Minggu)
        $jadwal = $query->orderBy('layanan.nama_layanan', 'asc')
            ->orderByRaw("FIELD(jadwal_lokasi_layanan.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->paginate(15);

        $jadwal->appends($request->all());

        $listLayanan = DB::table('layanan')->orderBy('nama_layanan', 'asc')->get();
        $listLokasi = DB::table('cabang')->orderBy('nama_cabang', 'asc')->get();
        $listHari = $this->listHari;

        // Ambil data matriks jadwal mingguan untuk quick overview
        $rawMatrix = DB::table('jadwal_lokasi_layanan')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select('jadwal_lokasi_layanan.layanan_id', 'jadwal_lokasi_layanan.hari', 'cabang.nama_cabang', 'jadwal_lokasi_layanan.keterangan')
            ->get();

        $matrix = [];
        foreach ($rawMatrix as $row) {
            $matrix[$row->layanan_id][$row->hari] = $row;
        }

        return view('jadwallokasi.index', compact('jadwal', 'listLayanan', 'listLokasi', 'listHari', 'matrix'));
    }

    /**
     * Simpan jadwal lokasi harian baru / update jika sudah ada
     */
    public function store(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'kode_cabang' => 'required|exists:cabang,kode_cabang',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            DB::table('jadwal_lokasi_layanan')->updateOrInsert(
                [
                    'layanan_id' => $request->layanan_id,
                    'hari' => $request->hari
                ],
                [
                    'kode_cabang' => $request->kode_cabang,
                    'keterangan' => $request->keterangan,
                    'status' => $request->status ?? 1,
                    'updated_at' => now(),
                    'created_at' => now()
                ]
            );

            return Redirect::back()->with(['success' => 'Jadwal lokasi layanan hari ' . $request->hari . ' berhasil disimpan!']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal menyimpan jadwal: ' . $e->getMessage()]);
        }
    }

    /**
     * Modal Edit AJAX
     */
    public function edit(Request $request)
    {
        $jadwal = DB::table('jadwal_lokasi_layanan')->where('id', $request->id)->first();
        if (!$jadwal) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        $listLayanan = DB::table('layanan')->orderBy('nama_layanan', 'asc')->get();
        $listLokasi = DB::table('cabang')->orderBy('nama_cabang', 'asc')->get();
        $listHari = $this->listHari;

        return view('jadwallokasi.edit', compact('jadwal', 'listLayanan', 'listLokasi', 'listHari'));
    }

    /**
     * Update jadwal
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'kode_cabang' => 'required|exists:cabang,kode_cabang',
            'keterangan' => 'nullable|string|max:255',
        ]);

        try {
            // Cek duplikasi hari pada layanan yang sama selain ID ini
            $exist = DB::table('jadwal_lokasi_layanan')
                ->where('layanan_id', $request->layanan_id)
                ->where('hari', $request->hari)
                ->where('id', '!=', $id)
                ->first();

            if ($exist) {
                return Redirect::back()->with(['warning' => 'Jadwal untuk layanan tersebut pada hari ' . $request->hari . ' sudah ada!']);
            }

            DB::table('jadwal_lokasi_layanan')->where('id', $id)->update([
                'layanan_id' => $request->layanan_id,
                'hari' => $request->hari,
                'kode_cabang' => $request->kode_cabang,
                'keterangan' => $request->keterangan,
                'status' => $request->status ?? 1,
                'updated_at' => now(),
            ]);

            return Redirect::back()->with(['success' => 'Jadwal lokasi layanan berhasil diperbarui!']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal memperbarui jadwal: ' . $e->getMessage()]);
        }
    }

    /**
     * Hapus jadwal
     */
    public function delete($id)
    {
        try {
            DB::table('jadwal_lokasi_layanan')->where('id', $id)->delete();
            return Redirect::back()->with(['success' => 'Jadwal lokasi layanan berhasil dihapus!']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal menghapus jadwal: ' . $e->getMessage()]);
        }
    }

    /**
     * Simpan jadwal mingguan sekaligus (Bulk Weekly Schedule)
     */
    public function storeBulk(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required|exists:layanan,id',
            'jadwal' => 'required|array',
        ]);

        $layananId = $request->layanan_id;
        $jadwalInput = $request->jadwal;

        try {
            DB::beginTransaction();
            foreach ($jadwalInput as $hari => $data) {
                if (!empty($data['kode_cabang'])) {
                    DB::table('jadwal_lokasi_layanan')->updateOrInsert(
                        [
                            'layanan_id' => $layananId,
                            'hari' => $hari
                        ],
                        [
                            'kode_cabang' => $data['kode_cabang'],
                            'keterangan' => $data['keterangan'] ?? null,
                            'status' => isset($data['status']) ? 1 : 0,
                            'updated_at' => now(),
                            'created_at' => now()
                        ]
                    );
                } else {
                    // Jika dikosongkan, hapus jadwal hari tersebut
                    DB::table('jadwal_lokasi_layanan')
                        ->where('layanan_id', $layananId)
                        ->where('hari', $hari)
                        ->delete();
                }
            }
            DB::commit();

            return Redirect::back()->with(['success' => 'Jadwal mingguan lokasi layanan berhasil disimpan serentak!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Gagal menyimpan jadwal serentak: ' . $e->getMessage()]);
        }
    }

    /**
     * Tampilan Mobile Petugas Layanan: Melihat Jadwal Lokasi Sendiri
     */
    public function userJadwal()
    {
        $user = Auth::guard('karyawan')->user();
        $hariini = date('Y-m-d');
        $namahariIni = (new PresensiController)->gethari(date('D', strtotime($hariini)));

        $jadwalMingguan = DB::table('jadwal_lokasi_layanan')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('jadwal_lokasi_layanan.layanan_id', $user->id)
            ->where('jadwal_lokasi_layanan.status', 1)
            ->select('jadwal_lokasi_layanan.*', 'cabang.nama_cabang', 'cabang.lokasi_cabang', 'cabang.radius_cabang')
            ->orderByRaw("FIELD(jadwal_lokasi_layanan.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->get();

        $jadwalHariIni = $jadwalMingguan->firstWhere('hari', $namahariIni);

        return view('jadwallokasi.user_jadwal', compact('user', 'jadwalMingguan', 'jadwalHariIni', 'namahariIni'));
    }

    /**
     * Tampilan Khusus Admin: Jadwal Layanan
     * Menampilkan data tabel jadwal layanan yang dikelompokkan per layanan per hari untuk lokasinya,
     * dilengkapi filter semua dan layanan yang dipilih.
     */
    public function jadwalLayananView(Request $request)
    {
        $layanan_id = $request->layanan_id;
        $hari = $request->hari;

        $allLayanan = DB::table("layanan")->orderBy("nama_layanan", "asc")->get();
        $listHari = $this->listHari;

        // Query Layanan yang ditampilkan (semua atau spesifik yang dipilih)
        $layananQuery = DB::table("layanan")
            ->leftJoin("cabang", "layanan.kode_cabang", "=", "cabang.kode_cabang")
            ->select("layanan.*", "cabang.nama_cabang as nama_cabang_default")
            ->orderBy("layanan.nama_layanan", "asc");

        if (!empty($layanan_id) && $layanan_id != "all") {
            $layananQuery->where("layanan.id", $layanan_id);
        }

        $layananList = $layananQuery->get();

        // Ambil data jadwal lokasi aktif
        $jadwalQuery = DB::table("jadwal_lokasi_layanan")
            ->join("cabang", "jadwal_lokasi_layanan.kode_cabang", "=", "cabang.kode_cabang")
            ->select(
                "jadwal_lokasi_layanan.*",
                "cabang.nama_cabang",
                "cabang.lokasi_cabang",
                "cabang.radius_cabang"
            )
            ->where("jadwal_lokasi_layanan.status", 1);

        if (!empty($layanan_id) && $layanan_id != "all") {
            $jadwalQuery->where("jadwal_lokasi_layanan.layanan_id", $layanan_id);
        }

        if (!empty($hari) && $hari != "all") {
            $jadwalQuery->where("jadwal_lokasi_layanan.hari", $hari);
        }

        $rawJadwal = $jadwalQuery->get();

        // Susun matriks [layanan_id][hari]
        $matrix = [];
        foreach ($rawJadwal as $j) {
            $matrix[$j->layanan_id][$j->hari] = $j;
        }

        $totalJadwalAktif = count($rawJadwal);

        return view("jadwallokasi.jadwal_layanan", compact(
            "allLayanan",
            "layananList",
            "listHari",
            "matrix",
            "layanan_id",
            "hari",
            "totalJadwalAktif"
        ));
    }
}


