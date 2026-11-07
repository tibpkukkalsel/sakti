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
            ->leftJoin('jam_kerja', 'jadwal_lokasi_layanan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->select(
                'jadwal_lokasi_layanan.*',
                'layanan.nama_layanan',
                'layanan.nama_pic',
                'cabang.nama_cabang',
                'cabang.lokasi_cabang',
                'cabang.radius_cabang',
                'jam_kerja.nama_jam_kerja',
                'jam_kerja.jam_masuk',
                'jam_kerja.jam_pulang'
            );

        if ($request->filled('layanan_id')) {
            $query->where('jadwal_lokasi_layanan.layanan_id', $request->layanan_id);
        }

        if ($request->filled('jenis_jadwal')) {
            $query->where('jadwal_lokasi_layanan.jenis_jadwal', $request->jenis_jadwal);
        }

        if ($request->filled('hari')) {
            $query->where('jadwal_lokasi_layanan.hari', $request->hari);
        }

        if ($request->filled('tanggal')) {
            $query->where('jadwal_lokasi_layanan.tanggal', $request->tanggal);
        }

        if ($request->filled('kode_cabang')) {
            $query->where('jadwal_lokasi_layanan.kode_cabang', $request->kode_cabang);
        }

        // Urutkan: Event Khusus terbaru / tanggal terdekat dulu, lalu Rutin Mingguan (Senin - Minggu)
        $jadwal = $query->orderBy('layanan.nama_layanan', 'asc')
            ->orderBy('jadwal_lokasi_layanan.jenis_jadwal', 'asc') // 'khusus' then 'rutin'
            ->orderBy('jadwal_lokasi_layanan.tanggal', 'desc')
            ->orderByRaw("FIELD(jadwal_lokasi_layanan.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->paginate(10);

        $jadwal->appends($request->all());

        $listLayanan = DB::table('layanan')->orderBy('nama_layanan', 'asc')->get();
        $listLokasi = DB::table('cabang')->orderBy('nama_cabang', 'asc')->get();
        $listJamKerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja', 'asc')->get();
        $listHari = $this->listHari;

        // Ambil data matriks jadwal mingguan untuk quick overview
        $rawMatrix = DB::table('jadwal_lokasi_layanan')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('jadwal_lokasi_layanan.jenis_jadwal', 'rutin')
            ->select('jadwal_lokasi_layanan.layanan_id', 'jadwal_lokasi_layanan.hari', 'cabang.nama_cabang', 'jadwal_lokasi_layanan.keterangan')
            ->get();

        $matrix = [];
        foreach ($rawMatrix as $row) {
            $matrix[$row->layanan_id][$row->hari] = $row;
        }

        // Hitung total event khusus aktif mendatang
        $totalEventKhusus = DB::table('jadwal_lokasi_layanan')
            ->where('jenis_jadwal', 'khusus')
            ->where('status', 1)
            ->where('tanggal', '>=', date('Y-m-d'))
            ->count();

        return view('jadwallokasi.index', compact('jadwal', 'listLayanan', 'listLokasi', 'listJamKerja', 'listHari', 'matrix', 'totalEventKhusus'));
    }

    /**
     * Simpan jadwal lokasi harian baru / update jika sudah ada
     */
    public function store(Request $request)
    {
        $jenisJadwal = $request->jenis_jadwal ?? 'rutin';

        $rules = [
            'layanan_id' => 'required|exists:layanan,id',
            'jenis_jadwal' => 'required|in:rutin,khusus',
            'kode_cabang' => 'required|exists:cabang,kode_cabang',
            'kode_jam_kerja' => 'nullable|exists:jam_kerja,kode_jam_kerja',
            'keterangan' => 'nullable|string|max:255',
        ];

        if ($jenisJadwal == 'khusus') {
            $rules['tanggal'] = 'required|date';
        } else {
            $rules['hari'] = 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu';
        }

        $request->validate($rules);

        $defaultJk = DB::table('jam_kerja')->value('kode_jam_kerja');
        $kodeJamKerja = $request->filled('kode_jam_kerja') ? $request->kode_jam_kerja : $defaultJk;

        try {
            if ($jenisJadwal == 'khusus') {
                $tanggal = $request->tanggal;
                $dayEng = date('D', strtotime($tanggal));
                $hari = (new PresensiController)->gethari($dayEng);

                DB::table('jadwal_lokasi_layanan')->updateOrInsert(
                    [
                        'layanan_id' => $request->layanan_id,
                        'tanggal' => $tanggal,
                    ],
                    [
                        'jenis_jadwal' => 'khusus',
                        'hari' => $hari,
                        'kode_cabang' => $request->kode_cabang,
                        'kode_jam_kerja' => $kodeJamKerja,
                        'keterangan' => $request->keterangan,
                        'status' => $request->status ?? 1,
                        'updated_at' => now(),
                        'created_at' => now()
                    ]
                );

                $tglFormatted = date('d-m-Y', strtotime($tanggal));
                return Redirect::back()->with(['success' => 'Jadwal khusus event tanggal ' . $tglFormatted . ' (' . $hari . ') berhasil disimpan!']);
            } else {
                // Rutin Mingguan
                $exist = DB::table('jadwal_lokasi_layanan')
                    ->where('layanan_id', $request->layanan_id)
                    ->where('hari', $request->hari)
                    ->where('jenis_jadwal', 'rutin')
                    ->first();

                if ($exist) {
                    DB::table('jadwal_lokasi_layanan')->where('id', $exist->id)->update([
                        'kode_cabang' => $request->kode_cabang,
                        'kode_jam_kerja' => $kodeJamKerja,
                        'keterangan' => $request->keterangan,
                        'status' => $request->status ?? 1,
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('jadwal_lokasi_layanan')->insert([
                        'layanan_id' => $request->layanan_id,
                        'jenis_jadwal' => 'rutin',
                        'hari' => $request->hari,
                        'tanggal' => null,
                        'kode_cabang' => $request->kode_cabang,
                        'kode_jam_kerja' => $kodeJamKerja,
                        'keterangan' => $request->keterangan,
                        'status' => $request->status ?? 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                return Redirect::back()->with(['success' => 'Jadwal lokasi rutin hari ' . $request->hari . ' berhasil disimpan!']);
            }
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
        $listJamKerja = DB::table('jam_kerja')->orderBy('nama_jam_kerja', 'asc')->get();
        $listHari = $this->listHari;

        return view('jadwallokasi.edit', compact('jadwal', 'listLayanan', 'listLokasi', 'listJamKerja', 'listHari'));
    }

    /**
     * Update jadwal
     */
    public function update(Request $request, $id)
    {
        $jenisJadwal = $request->jenis_jadwal ?? 'rutin';

        $rules = [
            'layanan_id' => 'required|exists:layanan,id',
            'jenis_jadwal' => 'required|in:rutin,khusus',
            'kode_cabang' => 'required|exists:cabang,kode_cabang',
            'kode_jam_kerja' => 'nullable|exists:jam_kerja,kode_jam_kerja',
            'keterangan' => 'nullable|string|max:255',
        ];

        if ($jenisJadwal == 'khusus') {
            $rules['tanggal'] = 'required|date';
        } else {
            $rules['hari'] = 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu';
        }

        $request->validate($rules);

        $defaultJk = DB::table('jam_kerja')->value('kode_jam_kerja');
        $kodeJamKerja = $request->filled('kode_jam_kerja') ? $request->kode_jam_kerja : $defaultJk;

        try {
            if ($jenisJadwal == 'khusus') {
                $tanggal = $request->tanggal;
                $dayEng = date('D', strtotime($tanggal));
                $hari = (new PresensiController)->gethari($dayEng);

                $exist = DB::table('jadwal_lokasi_layanan')
                    ->where('layanan_id', $request->layanan_id)
                    ->where('tanggal', $tanggal)
                    ->where('id', '!=', $id)
                    ->first();

                if ($exist) {
                    return Redirect::back()->with(['warning' => 'Jadwal khusus untuk layanan tersebut pada tanggal ' . date('d-m-Y', strtotime($tanggal)) . ' sudah ada!']);
                }

                DB::table('jadwal_lokasi_layanan')->where('id', $id)->update([
                    'layanan_id' => $request->layanan_id,
                    'jenis_jadwal' => 'khusus',
                    'hari' => $hari,
                    'tanggal' => $tanggal,
                    'kode_cabang' => $request->kode_cabang,
                    'kode_jam_kerja' => $kodeJamKerja,
                    'keterangan' => $request->keterangan,
                    'status' => $request->status ?? 1,
                    'updated_at' => now(),
                ]);

                return Redirect::back()->with(['success' => 'Jadwal khusus event berhasil diperbarui!']);
            } else {
                $exist = DB::table('jadwal_lokasi_layanan')
                    ->where('layanan_id', $request->layanan_id)
                    ->where('hari', $request->hari)
                    ->where('jenis_jadwal', 'rutin')
                    ->where('id', '!=', $id)
                    ->first();

                if ($exist) {
                    return Redirect::back()->with(['warning' => 'Jadwal rutin untuk layanan tersebut pada hari ' . $request->hari . ' sudah ada!']);
                }

                DB::table('jadwal_lokasi_layanan')->where('id', $id)->update([
                    'layanan_id' => $request->layanan_id,
                    'jenis_jadwal' => 'rutin',
                    'hari' => $request->hari,
                    'tanggal' => null,
                    'kode_cabang' => $request->kode_cabang,
                    'kode_jam_kerja' => $kodeJamKerja,
                    'keterangan' => $request->keterangan,
                    'status' => $request->status ?? 1,
                    'updated_at' => now(),
                ]);

                return Redirect::back()->with(['success' => 'Jadwal rutin lokasi layanan berhasil diperbarui!']);
            }
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

        $defaultJk = DB::table('jam_kerja')->value('kode_jam_kerja');

        try {
            DB::beginTransaction();
            foreach ($jadwalInput as $hari => $data) {
                if (!empty($data['kode_cabang'])) {
                    $jkVal = !empty($data['kode_jam_kerja']) ? $data['kode_jam_kerja'] : $defaultJk;
                    $exist = DB::table('jadwal_lokasi_layanan')
                        ->where('layanan_id', $layananId)
                        ->where('hari', $hari)
                        ->where('jenis_jadwal', 'rutin')
                        ->first();

                    if ($exist) {
                        DB::table('jadwal_lokasi_layanan')->where('id', $exist->id)->update([
                            'kode_cabang' => $data['kode_cabang'],
                            'kode_jam_kerja' => $jkVal,
                            'keterangan' => $data['keterangan'] ?? null,
                            'status' => isset($data['status']) ? 1 : 0,
                            'updated_at' => now(),
                        ]);
                    } else {
                        DB::table('jadwal_lokasi_layanan')->insert([
                            'layanan_id' => $layananId,
                            'jenis_jadwal' => 'rutin',
                            'hari' => $hari,
                            'tanggal' => null,
                            'kode_cabang' => $data['kode_cabang'],
                            'kode_jam_kerja' => $jkVal,
                            'keterangan' => $data['keterangan'] ?? null,
                            'status' => isset($data['status']) ? 1 : 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } else {
                    // Jika dikosongkan, hapus jadwal rutin hari tersebut (jangan sentuh jadwal event)
                    DB::table('jadwal_lokasi_layanan')
                        ->where('layanan_id', $layananId)
                        ->where('hari', $hari)
                        ->where('jenis_jadwal', 'rutin')
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

        // 1. Cek apakah ada jadwal khusus event HARI INI
        $jadwalKhususHariIni = DB::table('jadwal_lokasi_layanan')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('jam_kerja', 'jadwal_lokasi_layanan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('jadwal_lokasi_layanan.layanan_id', $user->id)
            ->where('jadwal_lokasi_layanan.tanggal', $hariini)
            ->where('jadwal_lokasi_layanan.status', 1)
            ->select('jadwal_lokasi_layanan.*', 'cabang.nama_cabang', 'cabang.lokasi_cabang', 'cabang.radius_cabang', 'jam_kerja.nama_jam_kerja', 'jam_kerja.jam_masuk', 'jam_kerja.jam_pulang')
            ->first();

        // 2. Ambil jadwal mingguan rutin
        $jadwalMingguan = DB::table('jadwal_lokasi_layanan')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('jam_kerja', 'jadwal_lokasi_layanan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('jadwal_lokasi_layanan.layanan_id', $user->id)
            ->where('jadwal_lokasi_layanan.jenis_jadwal', 'rutin')
            ->where('jadwal_lokasi_layanan.status', 1)
            ->select('jadwal_lokasi_layanan.*', 'cabang.nama_cabang', 'cabang.lokasi_cabang', 'cabang.radius_cabang', 'jam_kerja.nama_jam_kerja', 'jam_kerja.jam_masuk', 'jam_kerja.jam_pulang')
            ->orderByRaw("FIELD(jadwal_lokasi_layanan.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->get();

        // Jika ada event khusus hari ini, prioritas event khusus. Jika tidak, ambil rutin
        $isEventKhususHariIni = ($jadwalKhususHariIni != null);
        $jadwalHariIni = $isEventKhususHariIni ? $jadwalKhususHariIni : $jadwalMingguan->firstWhere('hari', $namahariIni);

        // 3. Ambil daftar event khusus mendatang (mulai hari ini ke depan)
        $jadwalEventMendatang = DB::table('jadwal_lokasi_layanan')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('jam_kerja', 'jadwal_lokasi_layanan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('jadwal_lokasi_layanan.layanan_id', $user->id)
            ->where('jadwal_lokasi_layanan.jenis_jadwal', 'khusus')
            ->where('jadwal_lokasi_layanan.tanggal', '>=', $hariini)
            ->where('jadwal_lokasi_layanan.status', 1)
            ->select('jadwal_lokasi_layanan.*', 'cabang.nama_cabang', 'cabang.lokasi_cabang', 'cabang.radius_cabang', 'jam_kerja.nama_jam_kerja', 'jam_kerja.jam_masuk', 'jam_kerja.jam_pulang')
            ->orderBy('jadwal_lokasi_layanan.tanggal', 'asc')
            ->get();

        return view('jadwallokasi.user_jadwal', compact(
            'user',
            'jadwalMingguan',
            'jadwalHariIni',
            'namahariIni',
            'isEventKhususHariIni',
            'jadwalEventMendatang'
        ));
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

        // Ambil data jadwal lokasi rutin aktif
        $jadwalQuery = DB::table("jadwal_lokasi_layanan")
            ->join("cabang", "jadwal_lokasi_layanan.kode_cabang", "=", "cabang.kode_cabang")
            ->leftJoin("jam_kerja", "jadwal_lokasi_layanan.kode_jam_kerja", "=", "jam_kerja.kode_jam_kerja")
            ->select(
                "jadwal_lokasi_layanan.*",
                "cabang.nama_cabang",
                "cabang.lokasi_cabang",
                "cabang.radius_cabang",
                "jam_kerja.nama_jam_kerja",
                "jam_kerja.jam_masuk",
                "jam_kerja.jam_pulang"
            )
            ->where("jadwal_lokasi_layanan.jenis_jadwal", "rutin")
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

        // Ambil daftar event khusus mendatang
        $eventKhususQuery = DB::table("jadwal_lokasi_layanan")
            ->join("layanan", "jadwal_lokasi_layanan.layanan_id", "=", "layanan.id")
            ->join("cabang", "jadwal_lokasi_layanan.kode_cabang", "=", "cabang.kode_cabang")
            ->leftJoin("jam_kerja", "jadwal_lokasi_layanan.kode_jam_kerja", "=", "jam_kerja.kode_jam_kerja")
            ->where("jadwal_lokasi_layanan.jenis_jadwal", "khusus")
            ->where("jadwal_lokasi_layanan.status", 1)
            ->where("jadwal_lokasi_layanan.tanggal", ">=", date("Y-m-d"))
            ->select(
                "jadwal_lokasi_layanan.*",
                "layanan.nama_layanan",
                "cabang.nama_cabang",
                "cabang.lokasi_cabang",
                "cabang.radius_cabang",
                "jam_kerja.nama_jam_kerja",
                "jam_kerja.jam_masuk",
                "jam_kerja.jam_pulang"
            )
            ->orderBy("jadwal_lokasi_layanan.tanggal", "asc");

        if (!empty($layanan_id) && $layanan_id != "all") {
            $eventKhususQuery->where("jadwal_lokasi_layanan.layanan_id", $layanan_id);
        }

        $listEventKhusus = $eventKhususQuery->get();

        return view("jadwallokasi.jadwal_layanan", compact(
            "allLayanan",
            "layananList",
            "listHari",
            "matrix",
            "layanan_id",
            "hari",
            "totalJadwalAktif",
            "listEventKhusus"
        ));
    }
}


