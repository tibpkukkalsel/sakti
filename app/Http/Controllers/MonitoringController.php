<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class MonitoringController extends Controller
{
    /**
     * Middleware check agar hanya akun dengan role monitoring/admin yang bisa mengakses
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::guard('user')->check()) {
                return redirect('/')->with(['warning' => 'Silahkan login terlebih dahulu.']);
            }
            $user = Auth::guard('user')->user();
            if (!$user->hasRole('monitoring') && !$user->hasRole('administrator') && !$user->hasRole('admin departemen')) {
                Auth::guard('user')->logout();
                return redirect('/')->with(['warning' => 'Anda tidak memiliki hak akses Monitoring.']);
            }
            return $next($request);
        });
    }

    /**
     * Dashboard Akun Monitoring Mobile
     */
    public function dashboard(Request $request)
    {
        $user = Auth::guard('user')->user();
        $hariini = date('Y-m-d');
        $bulanini = date('m');
        $tahunini = date('Y');

        $namahari_array = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $namahari = $namahari_array[date('w')];
        $namabulan_array = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $tanggal_indo = $namahari . ', ' . date('j') . ' ' . $namabulan_array[(int) $bulanini] . ' ' . $tahunini;

        // 1. Data Ringkasan Layanan & Kehadiran Hari Ini
        $totalLayanan = DB::table('layanan')->count();

        $presensiHariIni = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->get();

        $jml_hadir = 0;
        $jml_maintenance = 0;
        $jml_libur = 0;

        foreach ($presensiHariIni as $p) {
            $st = strtolower($p->status ?? '');
            if ($st == 'h' || $st == 'tl' || (!empty($p->jam_in) && $st != 'm' && $st != 'x' && $st != 'l')) {
                $jml_hadir++;
            } elseif ($st == 'm' || $st == 'x') {
                $jml_maintenance++;
            } elseif ($st == 'l') {
                $jml_libur++;
            }
        }
        $jml_belum_absen = max(0, $totalLayanan - ($jml_hadir + $jml_maintenance + $jml_libur));

        // 2. Data Ringkasan Penerimaan PKB
        $penerimaanHariIni = (float) DB::table('rekap_penerimaan_pkb')
            ->where('tgl_penerimaan', $hariini)
            ->sum('total_rp');

        $objekHariIni = (int) DB::table('rekap_penerimaan_pkb')
            ->where('tgl_penerimaan', $hariini)
            ->sum('total_unit');

        $penerimaanBulanIni = (float) DB::table('rekap_penerimaan_pkb')
            ->whereYear('tgl_penerimaan', $tahunini)
            ->whereMonth('tgl_penerimaan', $bulanini)
            ->sum('total_rp');

        $objekBulanIni = (int) DB::table('rekap_penerimaan_pkb')
            ->whereYear('tgl_penerimaan', $tahunini)
            ->whereMonth('tgl_penerimaan', $bulanini)
            ->sum('total_unit');

        // 3. Data Grafik Tren Penerimaan (12 Bulan Tahun Ini) - Sama persis dengan Dashboard Admin
        $chartBulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartBulanValuesTotal = [];
        $chartBulanValuesR2 = [];
        $chartBulanValuesR4 = [];

        $monthlyData = DB::table('rekap_penerimaan_pkb')
            ->selectRaw('MONTH(tgl_penerimaan) as bulan, SUM(total_rp) as total_rp, SUM(jumlah_rp_r2) as rp_r2, SUM(jumlah_rp_r4) as rp_r4, SUM(total_unit) as total_unit')
            ->whereYear('tgl_penerimaan', $tahunini)
            ->groupBy(DB::raw('MONTH(tgl_penerimaan)'))
            ->get()
            ->keyBy('bulan');

        for ($m = 1; $m <= 12; $m++) {
            $rowM = $monthlyData->get($m);
            $chartBulanValuesTotal[] = $rowM ? (float) $rowM->total_rp : 0;
            $chartBulanValuesR2[] = $rowM ? (float) $rowM->rp_r2 : 0;
            $chartBulanValuesR4[] = $rowM ? (float) $rowM->rp_r4 : 0;
        }

        // 4. Data Status Real-time Layanan Hari Ini
        $monitoringLayanan = DB::table('layanan')
            ->leftJoin('jadwal_lokasi_layanan', function ($join) use ($namahari) {
                $join->on('layanan.id', '=', 'jadwal_lokasi_layanan.layanan_id')
                    ->where('jadwal_lokasi_layanan.hari', '=', $namahari);
            })
            ->leftJoin('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('jam_kerja', 'jadwal_lokasi_layanan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('presensi', function ($join) use ($hariini) {
                $join->on('layanan.id', '=', 'presensi.nik')
                    ->where('presensi.tgl_presensi', '=', $hariini);
            })
            ->leftJoin('rekap_penerimaan_pkb', function ($join) use ($hariini) {
                $join->on('layanan.id', '=', 'rekap_penerimaan_pkb.layanan_id')
                    ->where('rekap_penerimaan_pkb.tgl_penerimaan', '=', $hariini);
            })
            ->select(
                'layanan.id',
                'layanan.nama_layanan',
                'layanan.foto',
                'layanan.no_hp',
                'layanan.nama_pic',
                DB::raw('COALESCE(cabang.nama_cabang, "UPPD SAMSAT Batulicin") as lokasi'),
                'jam_kerja.nama_jam_kerja',
                'presensi.jam_in',
                'presensi.jam_out',
                'presensi.foto_in',
                'presensi.foto_out',
                'presensi.status as status_presensi',
                'rekap_penerimaan_pkb.total_rp as penerimaan_hari_ini',
                'rekap_penerimaan_pkb.total_unit as unit_hari_ini'
            )
            ->orderBy('layanan.nama_layanan', 'asc')
            ->get();

        return view('monitoring.dashboard', compact(
            'user',
            'tanggal_indo',
            'namahari',
            'totalLayanan',
            'jml_hadir',
            'jml_maintenance',
            'jml_libur',
            'jml_belum_absen',
            'penerimaanHariIni',
            'objekHariIni',
            'penerimaanBulanIni',
            'objekBulanIni',
            'chartBulanLabels',
            'chartBulanValuesTotal',
            'chartBulanValuesR2',
            'chartBulanValuesR4',
            'monitoringLayanan'
        ));
    }

    /**
     * Data Seluruh Layanan (Armada & Kru)
     */
    public function layanan(Request $request)
    {
        $search = $request->q;
        $query = DB::table('layanan')
            ->leftJoin('cabang', 'layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->select('layanan.*', 'cabang.nama_cabang as posko_default');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('layanan.nama_layanan', 'like', "%$search%")
                    ->orWhere('layanan.nama_pic', 'like', "%$search%")
                    ->orWhere('layanan.username', 'like', "%$search%");
            });
        }

        $layananList = $query->orderBy('layanan.nama_layanan', 'asc')->get();
        $totalLayanan = $layananList->count();

        return view('monitoring.layanan', compact('layananList', 'totalLayanan', 'search'));
    }

    /**
     * Jadwal Lokasi Seluruh Layanan
     */
    public function jadwal(Request $request)
    {
        $hariPilihan = $request->hari;
        $listHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $query = DB::table('jadwal_lokasi_layanan')
            ->join('layanan', 'jadwal_lokasi_layanan.layanan_id', '=', 'layanan.id')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('jam_kerja', 'jadwal_lokasi_layanan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->select(
                'jadwal_lokasi_layanan.*',
                'layanan.nama_layanan',
                'layanan.foto as foto_layanan',
                'layanan.nama_pic',
                'layanan.no_hp',
                'cabang.nama_cabang',
                'cabang.lokasi_cabang',
                'cabang.radius_cabang',
                'jam_kerja.nama_jam_kerja',
                'jam_kerja.jam_masuk',
                'jam_kerja.jam_pulang'
            );

        if (!empty($hariPilihan) && $hariPilihan != 'all') {
            $query->where('jadwal_lokasi_layanan.hari', $hariPilihan);
        }

        $jadwalList = $query->orderByRaw("FIELD(jadwal_lokasi_layanan.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('layanan.nama_layanan', 'asc')
            ->get();

        return view('monitoring.jadwal', compact('jadwalList', 'listHari', 'hariPilihan'));
    }

    /**
     * Data Maintenance / Perbaikan Armada Layanan
     */
    public function maintenance(Request $request)
    {
        $hariini = date('Y-m-d');

        // Layanan yang tercatat maintenance hari ini via presensi atau izin
        $maintenanceHariIni = DB::table('layanan')
            ->leftJoin('presensi', function ($join) use ($hariini) {
                $join->on('layanan.id', '=', 'presensi.nik')
                    ->where('presensi.tgl_presensi', '=', $hariini);
            })
            ->where(function ($q) {
                $q->where('presensi.status', 'm')
                    ->orWhere('presensi.status', 'x');
            })
            ->select(
                'layanan.*',
                'presensi.tgl_presensi',
                'presensi.status as status_presensi',
                'presensi.selisih_in as keterangan_maintenance'
            )
            ->get();

        // Riwayat maintenance terbaru
        $riwayatMaintenance = DB::table('presensi')
            ->join('layanan', 'presensi.nik', '=', 'layanan.id')
            ->whereIn('presensi.status', ['m', 'x'])
            ->select(
                'presensi.*',
                'layanan.nama_layanan',
                'layanan.foto',
                'layanan.nama_pic',
                'layanan.no_hp'
            )
            ->orderBy('presensi.tgl_presensi', 'desc')
            ->limit(30)
            ->get();

        return view('monitoring.maintenance', compact('maintenanceHariIni', 'riwayatMaintenance'));
    }

    /**
     * Data Monitoring Presensi Real-Time
     */
    public function presensi(Request $request)
    {
        $tanggal = $request->tanggal ?: date('Y-m-d');
        $namahari_array = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hariNama = $namahari_array[date('w', strtotime($tanggal))];

        $presensiList = DB::table('layanan')
            ->leftJoin('jadwal_lokasi_layanan', function ($join) use ($hariNama) {
                $join->on('layanan.id', '=', 'jadwal_lokasi_layanan.layanan_id')
                    ->where('jadwal_lokasi_layanan.hari', '=', $hariNama);
            })
            ->leftJoin('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('jam_kerja', 'jadwal_lokasi_layanan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('presensi', function ($join) use ($tanggal) {
                $join->on('layanan.id', '=', 'presensi.nik')
                    ->where('presensi.tgl_presensi', '=', $tanggal);
            })
            ->select(
                'layanan.id as layanan_id',
                'layanan.nama_layanan',
                'layanan.foto as foto_avatar',
                'layanan.nama_pic',
                'layanan.no_hp',
                DB::raw('COALESCE(cabang.nama_cabang, "UPPD SAMSAT Batulicin") as lokasi_tugas'),
                'jam_kerja.nama_jam_kerja',
                'jam_kerja.jam_masuk as jam_standar_masuk',
                'jam_kerja.jam_pulang as jam_standar_pulang',
                'presensi.id as presensi_id',
                'presensi.tgl_presensi',
                'presensi.jam_in',
                'presensi.jam_out',
                'presensi.foto_in',
                'presensi.foto_out',
                'presensi.lokasi_in',
                'presensi.lokasi_out',
                'presensi.status as status_presensi'
            )
            ->orderBy('layanan.nama_layanan', 'asc')
            ->get();

        // Hitung rekap status
        $statHadir = 0;
        $statMaintenance = 0;
        $statLibur = 0;
        $statTK = 0;

        foreach ($presensiList as $item) {
            $st = strtolower($item->status_presensi ?? '');
            if ($st == 'h' || $st == 'tl' || (!empty($item->jam_in) && $st != 'm' && $st != 'x' && $st != 'l')) {
                $statHadir++;
            } elseif ($st == 'm' || $st == 'x') {
                $statMaintenance++;
            } elseif ($st == 'l') {
                $statLibur++;
            } else {
                $statTK++;
            }
        }

        return view('monitoring.presensi', compact(
            'presensiList',
            'tanggal',
            'hariNama',
            'statHadir',
            'statMaintenance',
            'statLibur',
            'statTK'
        ));
    }

    /**
     * Data Penerimaan PKB Layanan
     */
    public function penerimaan(Request $request)
    {
        $bulan = $request->bulan ?: date('m');
        $tahun = $request->tahun ?: date('Y');
        $layanan_id = $request->layanan_id;

        $layananList = DB::table('layanan')->orderBy('nama_layanan')->get();

        $query = DB::table('rekap_penerimaan_pkb')
            ->join('layanan', 'rekap_penerimaan_pkb.layanan_id', '=', 'layanan.id')
            ->whereYear('rekap_penerimaan_pkb.tgl_penerimaan', $tahun)
            ->whereMonth('rekap_penerimaan_pkb.tgl_penerimaan', $bulan);

        if (!empty($layanan_id) && $layanan_id != 'all') {
            $query->where('rekap_penerimaan_pkb.layanan_id', $layanan_id);
        }

        $rekapList = $query->select(
            'rekap_penerimaan_pkb.*',
            'layanan.nama_layanan',
            'layanan.foto as foto_layanan'
        )
            ->orderBy('rekap_penerimaan_pkb.tgl_penerimaan', 'desc')
            ->get();

        $totalRp = $rekapList->sum('total_rp');
        $totalUnit = $rekapList->sum('total_unit');
        $totalR2Rp = $rekapList->sum('jumlah_rp_r2');
        $totalR4Rp = $rekapList->sum('jumlah_rp_r4');
        $totalUnitR2 = $rekapList->sum('jumlah_unit_r2');
        $totalUnitR4 = $rekapList->sum('jumlah_unit_r4');

        $namabulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        return view('monitoring.penerimaan', compact(
            'rekapList',
            'layananList',
            'bulan',
            'tahun',
            'layanan_id',
            'totalRp',
            'totalUnit',
            'totalR2Rp',
            'totalR4Rp',
            'totalUnitR2',
            'totalUnitR4',
            'namabulan'
        ));
    }

    /**
     * Profil Akun Monitoring
     */
    public function profile()
    {
        $user = Auth::guard('user')->user();
        return view('monitoring.profile', compact('user'));
    }

    /**
     * Update Password Akun Monitoring
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('user')->user();
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|min:6'
        ]);

        $data = ['name' => $request->name];
        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $user->id)->update($data);

        return Redirect::back()->with(['success' => 'Profil berhasil diperbarui.']);
    }
}
