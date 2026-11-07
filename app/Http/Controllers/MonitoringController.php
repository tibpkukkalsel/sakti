<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
    public function dashboard(Request $request = null)
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

        $r2HariIniUnit = (int) DB::table('rekap_penerimaan_pkb')
            ->where('tgl_penerimaan', $hariini)
            ->sum('jumlah_unit_r2');

        $r2HariIniRp = (float) DB::table('rekap_penerimaan_pkb')
            ->where('tgl_penerimaan', $hariini)
            ->sum('jumlah_rp_r2');

        $r4HariIniUnit = (int) DB::table('rekap_penerimaan_pkb')
            ->where('tgl_penerimaan', $hariini)
            ->sum('jumlah_unit_r4');

        $r4HariIniRp = (float) DB::table('rekap_penerimaan_pkb')
            ->where('tgl_penerimaan', $hariini)
            ->sum('jumlah_rp_r4');

        $r2HariIni = $r2HariIniUnit;
        $r4HariIni = $r4HariIniUnit;

        $noticeHariIni = (int) DB::table('rekap_penerimaan_pkb')
            ->where('tgl_penerimaan', $hariini)
            ->sum('jumlah_notice');

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

        $summaryTahunBerjalan = DB::table('rekap_penerimaan_pkb')
            ->whereYear('tgl_penerimaan', $tahunini)
            ->selectRaw('
                COALESCE(SUM(total_rp), 0) as total_rp,
                COALESCE(SUM(total_unit), 0) as total_unit,
                COALESCE(SUM(jumlah_notice), 0) as total_notice,
                COALESCE(SUM(jumlah_rp_r2), 0) as rp_r2,
                COALESCE(SUM(jumlah_unit_r2), 0) as unit_r2,
                COALESCE(SUM(jumlah_rp_r4), 0) as rp_r4,
                COALESCE(SUM(jumlah_unit_r4), 0) as unit_r4
            ')
            ->first();

        $totalTahunBerjalanRp = (float) ($summaryTahunBerjalan->total_rp ?? 0);
        $totalTahunBerjalanUnit = (int) ($summaryTahunBerjalan->total_unit ?? 0);
        $totalTahunBerjalanNotice = (int) ($summaryTahunBerjalan->total_notice ?? 0);
        $totalTahunBerjalanR2Rp = (float) ($summaryTahunBerjalan->rp_r2 ?? 0);
        $totalTahunBerjalanR2Unit = (int) ($summaryTahunBerjalan->unit_r2 ?? 0);
        $totalTahunBerjalanR4Rp = (float) ($summaryTahunBerjalan->rp_r4 ?? 0);
        $totalTahunBerjalanR4Unit = (int) ($summaryTahunBerjalan->unit_r4 ?? 0);
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

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

        // 3b. Data Grafik Bulanan Per Layanan (Tahun Berjalan)
        $rawPendapatanLayananBulan = DB::table('rekap_penerimaan_pkb')
            ->join('layanan', 'rekap_penerimaan_pkb.layanan_id', '=', 'layanan.id')
            ->whereYear('rekap_penerimaan_pkb.tgl_penerimaan', $tahunini)
            ->selectRaw('
                rekap_penerimaan_pkb.layanan_id,
                layanan.nama_layanan,
                MONTH(rekap_penerimaan_pkb.tgl_penerimaan) as bulan,
                COALESCE(SUM(rekap_penerimaan_pkb.total_rp), 0) as total_rp
            ')
            ->groupBy('rekap_penerimaan_pkb.layanan_id', 'layanan.nama_layanan', DB::raw('MONTH(rekap_penerimaan_pkb.tgl_penerimaan)'))
            ->get();

        $daftarLayananChart = DB::table('layanan')->orderBy('nama_layanan', 'asc')->get();

        $chartLayananPerBulanSeries = [];
        foreach ($daftarLayananChart as $lay) {
            $monthlyValues = [];
            for ($m = 1; $m <= 12; $m++) {
                $item = $rawPendapatanLayananBulan->first(function ($r) use ($lay, $m) {
                    return $r->layanan_id == $lay->id && $r->bulan == $m;
                });
                $monthlyValues[] = (float) ($item ? $item->total_rp : 0);
            }
            $chartLayananPerBulanSeries[] = [
                'name' => $lay->nama_layanan,
                'data' => $monthlyValues
            ];
        }

        // 3c. Data Pie Chart Total & Persentase Pendapatan Per Layanan (Tahun Berjalan)
        $pendapatanPerLayananTahunIni = DB::table('rekap_penerimaan_pkb')
            ->join('layanan', 'rekap_penerimaan_pkb.layanan_id', '=', 'layanan.id')
            ->whereYear('rekap_penerimaan_pkb.tgl_penerimaan', $tahunini)
            ->select(
                'layanan.nama_layanan',
                DB::raw('COALESCE(SUM(rekap_penerimaan_pkb.total_rp), 0) as total_rp')
            )
            ->groupBy('layanan.id', 'layanan.nama_layanan')
            ->orderBy('layanan.nama_layanan', 'asc')
            ->get();

        $pieLayananLabels = $pendapatanPerLayananTahunIni->pluck('nama_layanan')->toArray();
        $pieLayananValues = $pendapatanPerLayananTahunIni->pluck('total_rp')->map(fn($v) => (float) $v)->toArray();

        // 3d. Data Pie Chart Persentase Pendapatan Berdasarkan Hari (Tahun Berjalan)
        $dayQuery = DB::table('rekap_penerimaan_pkb')
            ->whereYear('tgl_penerimaan', $tahunini)
            ->selectRaw("
                CASE DAYOFWEEK(tgl_penerimaan)
                    WHEN 2 THEN 'Senin'
                    WHEN 3 THEN 'Selasa'
                    WHEN 4 THEN 'Rabu'
                    WHEN 5 THEN 'Kamis'
                    WHEN 6 THEN 'Jumat'
                    WHEN 7 THEN 'Sabtu'
                    ELSE 'Minggu'
                END as nama_hari,
                DAYOFWEEK(tgl_penerimaan) as day_num,
                COALESCE(SUM(total_rp), 0) as total_rp
            ")
            ->groupBy('day_num', 'nama_hari')
            ->orderByRaw("FIELD(day_num, 2, 3, 4, 5, 6, 7, 1)")
            ->get();

        $pieHariLabels = $dayQuery->pluck('nama_hari')->toArray();
        $pieHariValues = $dayQuery->pluck('total_rp')->map(fn($v) => (float) $v)->toArray();

        // 3e. Detail Penerimaan Per Hari: Nama Layanan, Nama Lokasi, Detail Rp (Urutan Tertinggi Diatas)
        $detailHariQuery = DB::table('rekap_penerimaan_pkb')
            ->join('layanan', 'rekap_penerimaan_pkb.layanan_id', '=', 'layanan.id')
            ->leftJoin('jadwal_lokasi_layanan as j_khusus', function ($join) {
                $join->on('j_khusus.layanan_id', '=', 'rekap_penerimaan_pkb.layanan_id')
                    ->on('j_khusus.tanggal', '=', 'rekap_penerimaan_pkb.tgl_penerimaan')
                    ->where('j_khusus.status', 1);
            })
            ->leftJoin('jadwal_lokasi_layanan as j_rutin', function ($join) {
                $join->on('j_rutin.layanan_id', '=', 'rekap_penerimaan_pkb.layanan_id')
                    ->where('j_rutin.jenis_jadwal', 'rutin')
                    ->where('j_rutin.status', 1)
                    ->whereRaw("j_rutin.hari = CASE DAYOFWEEK(rekap_penerimaan_pkb.tgl_penerimaan)
                        WHEN 1 THEN 'Minggu'
                        WHEN 2 THEN 'Senin'
                        WHEN 3 THEN 'Selasa'
                        WHEN 4 THEN 'Rabu'
                        WHEN 5 THEN 'Kamis'
                        WHEN 6 THEN 'Jumat'
                        WHEN 7 THEN 'Sabtu'
                    END");
            })
            ->leftJoin('cabang as c_lokasi', DB::raw("COALESCE(rekap_penerimaan_pkb.kode_cabang, j_khusus.kode_cabang, j_rutin.kode_cabang, layanan.kode_cabang)"), '=', 'c_lokasi.kode_cabang')
            ->whereYear('rekap_penerimaan_pkb.tgl_penerimaan', $tahunini)
            ->selectRaw("
                CASE DAYOFWEEK(rekap_penerimaan_pkb.tgl_penerimaan)
                    WHEN 2 THEN 'Senin'
                    WHEN 3 THEN 'Selasa'
                    WHEN 4 THEN 'Rabu'
                    WHEN 5 THEN 'Kamis'
                    WHEN 6 THEN 'Jumat'
                    WHEN 7 THEN 'Sabtu'
                    ELSE 'Minggu'
                END as nama_hari,
                layanan.nama_layanan,
                COALESCE(c_lokasi.nama_cabang, 'UPPD SAMSAT Batulicin') as nama_lokasi,
                COALESCE(SUM(rekap_penerimaan_pkb.total_rp), 0) as total_rp,
                COALESCE(SUM(rekap_penerimaan_pkb.total_unit), 0) as total_unit,
                COALESCE(SUM(rekap_penerimaan_pkb.jumlah_notice), 0) as total_notice,
                COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r2), 0) as rp_r2,
                COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r2), 0) as unit_r2,
                COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r4), 0) as rp_r4,
                COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r4), 0) as unit_r4
            ")
            ->groupBy(
                DB::raw("CASE DAYOFWEEK(rekap_penerimaan_pkb.tgl_penerimaan)
                    WHEN 2 THEN 'Senin'
                    WHEN 3 THEN 'Selasa'
                    WHEN 4 THEN 'Rabu'
                    WHEN 5 THEN 'Kamis'
                    WHEN 6 THEN 'Jumat'
                    WHEN 7 THEN 'Sabtu'
                    ELSE 'Minggu'
                END"),
                'layanan.id',
                'layanan.nama_layanan',
                DB::raw("COALESCE(c_lokasi.nama_cabang, 'UPPD SAMSAT Batulicin')")
            )
            ->orderBy('total_rp', 'desc')
            ->get();

        $detailPerHariJson = $detailHariQuery->groupBy('nama_hari')->toArray();

        // 4. Data Status Real-time Layanan Hari Ini
        $monitoringLayanan = DB::table('layanan')
            ->leftJoin('jadwal_lokasi_layanan as j_khusus', function ($join) use ($hariini) {
                $join->on('j_khusus.layanan_id', '=', 'layanan.id')
                    ->where('j_khusus.jenis_jadwal', '=', 'khusus')
                    ->where('j_khusus.tanggal', '=', $hariini)
                    ->where('j_khusus.status', '=', 1);
            })
            ->leftJoin('jadwal_lokasi_layanan as j_rutin', function ($join) use ($namahari) {
                $join->on('j_rutin.layanan_id', '=', 'layanan.id')
                    ->where('j_rutin.jenis_jadwal', '=', 'rutin')
                    ->where('j_rutin.hari', '=', $namahari)
                    ->where('j_rutin.status', '=', 1);
            })
            ->leftJoin('cabang', DB::raw("COALESCE(j_khusus.kode_cabang, j_rutin.kode_cabang, layanan.kode_cabang)"), '=', 'cabang.kode_cabang')
            ->leftJoin('jam_kerja', DB::raw("COALESCE(j_khusus.kode_jam_kerja, j_rutin.kode_jam_kerja)"), '=', 'jam_kerja.kode_jam_kerja')
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
            'bulanini',
            'tahunini',
            'namabulan',
            'totalTahunBerjalanRp',
            'totalTahunBerjalanUnit',
            'totalTahunBerjalanNotice',
            'totalTahunBerjalanR2Rp',
            'totalTahunBerjalanR2Unit',
            'totalTahunBerjalanR4Rp',
            'totalTahunBerjalanR4Unit',
            'totalLayanan',
            'jml_hadir',
            'jml_maintenance',
            'jml_libur',
            'jml_belum_absen',
            'penerimaanHariIni',
            'objekHariIni',
            'r2HariIni',
            'r4HariIni',
            'r2HariIniUnit',
            'r2HariIniRp',
            'r4HariIniUnit',
            'r4HariIniRp',
            'noticeHariIni',
            'penerimaanBulanIni',
            'objekBulanIni',
            'chartBulanLabels',
            'chartBulanValuesTotal',
            'chartBulanValuesR2',
            'chartBulanValuesR4',
            'chartLayananPerBulanSeries',
            'pieLayananLabels',
            'pieLayananValues',
            'pieHariLabels',
            'pieHariValues',
            'detailPerHariJson',
            'monitoringLayanan'
        ));
    }

    /**
     * Data Seluruh Layanan (Armada & Kru)
     */
    public function layanan(Request $request)
    {
        $search = $request->q;
        $hariini = date('Y-m-d');
        $namahari_array = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $namahari = $namahari_array[date('w')];

        $query = DB::table('layanan')
            ->leftJoin('jadwal_lokasi_layanan as j_khusus', function ($join) use ($hariini) {
                $join->on('j_khusus.layanan_id', '=', 'layanan.id')
                    ->where('j_khusus.jenis_jadwal', '=', 'khusus')
                    ->where('j_khusus.tanggal', '=', $hariini)
                    ->where('j_khusus.status', '=', 1);
            })
            ->leftJoin('jadwal_lokasi_layanan as j_rutin', function ($join) use ($namahari) {
                $join->on('j_rutin.layanan_id', '=', 'layanan.id')
                    ->where('j_rutin.jenis_jadwal', '=', 'rutin')
                    ->where('j_rutin.hari', '=', $namahari)
                    ->where('j_rutin.status', '=', 1);
            })
            ->leftJoin('cabang as c_aktif', DB::raw("COALESCE(j_khusus.kode_cabang, j_rutin.kode_cabang, layanan.kode_cabang)"), '=', 'c_aktif.kode_cabang')
            ->leftJoin('jam_kerja', DB::raw("COALESCE(j_khusus.kode_jam_kerja, j_rutin.kode_jam_kerja)"), '=', 'jam_kerja.kode_jam_kerja')
            ->select(
                'layanan.*',
                DB::raw("COALESCE(c_aktif.nama_cabang, 'UPPD SAMSAT Batulicin') as lokasi_hari_ini"),
                'c_aktif.nama_cabang as lokasi_default',
                'jam_kerja.nama_jam_kerja',
                'jam_kerja.jam_masuk',
                'jam_kerja.jam_pulang',
                DB::raw("CASE WHEN j_khusus.id IS NOT NULL THEN 'khusus' WHEN j_rutin.id IS NOT NULL THEN 'rutin' ELSE 'default' END as status_jadwal")
            );

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
     * Jadwal Lokasi Seluruh Layanan (Dikelompokkan per Hari & Filter Mingguan vs Event)
     */
    public function jadwal(Request $request)
    {
        $namahari_array = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $hariini = $namahari_array[date('w')];
        $listHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // 1. Jadwal Rutin Mingguan
        $jadwalRutin = DB::table('jadwal_lokasi_layanan')
            ->join('layanan', 'jadwal_lokasi_layanan.layanan_id', '=', 'layanan.id')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('jam_kerja', 'jadwal_lokasi_layanan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('jadwal_lokasi_layanan.jenis_jadwal', 'rutin')
            ->select(
                'jadwal_lokasi_layanan.*',
                'layanan.nama_layanan',
                'layanan.foto as foto_layanan',
                'cabang.nama_cabang',
                'jam_kerja.nama_jam_kerja',
                'jam_kerja.jam_masuk',
                'jam_kerja.jam_pulang'
            )
            ->orderByRaw("FIELD(jadwal_lokasi_layanan.hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('layanan.nama_layanan', 'asc')
            ->get();

        $jadwalByHari = $jadwalRutin->groupBy('hari');

        // 2. Jadwal Event Khusus Mendatang
        $eventKhusus = DB::table('jadwal_lokasi_layanan')
            ->join('layanan', 'jadwal_lokasi_layanan.layanan_id', '=', 'layanan.id')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('jam_kerja', 'jadwal_lokasi_layanan.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('jadwal_lokasi_layanan.jenis_jadwal', 'khusus')
            ->where('jadwal_lokasi_layanan.status', 1)
            ->where('jadwal_lokasi_layanan.tanggal', '>=', date('Y-m-d'))
            ->select(
                'jadwal_lokasi_layanan.*',
                'layanan.nama_layanan',
                'layanan.foto as foto_layanan',
                'cabang.nama_cabang',
                'jam_kerja.nama_jam_kerja',
                'jam_kerja.jam_masuk',
                'jam_kerja.jam_pulang'
            )
            ->orderBy('jadwal_lokasi_layanan.tanggal', 'asc')
            ->orderBy('layanan.nama_layanan', 'asc')
            ->get();

        $eventByTanggal = $eventKhusus->groupBy('tanggal');

        return view('monitoring.jadwal', compact('jadwalByHari', 'listHari', 'hariini', 'eventKhusus', 'eventByTanggal'));
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
     * Data Monitoring Presensi Real-Time & Riwayat Presensi
     */
    public function presensi(Request $request)
    {
        $periode = $request->periode ?: 'hari_ini';
        $layanan_id = $request->layanan_id;
        $namabulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $namahari_array = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $hariini = date('Y-m-d');
        if ($periode == 'minggu_ini') {
            $dari_tanggal = Carbon::now()->startOfWeek()->toDateString();
            $sampai_tanggal = Carbon::now()->endOfWeek()->toDateString();
            $labelPeriode = 'Minggu Ini (' . date('d M', strtotime($dari_tanggal)) . ' - ' . date('d M Y', strtotime($sampai_tanggal)) . ')';
        } elseif ($periode == 'bulan_ini') {
            $dari_tanggal = date('Y-m-01');
            $sampai_tanggal = date('Y-m-t');
            $labelPeriode = 'Bulan Ini (' . $namabulan[(int)date('m')] . ' ' . date('Y') . ')';
        } elseif ($periode == 'tahun_ini') {
            $dari_tanggal = date('Y-01-01');
            $sampai_tanggal = date('Y-12-31');
            $labelPeriode = 'Tahun ' . date('Y');
        } elseif ($periode == 'custom') {
            $dari_tanggal = $request->dari_tanggal ?: $hariini;
            $sampai_tanggal = $request->sampai_tanggal ?: $hariini;
            if ($dari_tanggal == $sampai_tanggal) {
                $labelPeriode = date('d/m/Y', strtotime($dari_tanggal));
            } else {
                $labelPeriode = date('d/m/Y', strtotime($dari_tanggal)) . ' - ' . date('d/m/Y', strtotime($sampai_tanggal));
            }
        } else {
            // Default: hari_ini
            $periode = 'hari_ini';
            $dari_tanggal = $hariini;
            $sampai_tanggal = $hariini;
            $labelPeriode = 'Hari Ini (' . date('d M Y') . ')';
        }

        // Backward compatibility jika ada parameter tanggal
        if (!$request->has('periode') && $request->has('tanggal')) {
            $dari_tanggal = $request->tanggal;
            $sampai_tanggal = $request->tanggal;
            if ($dari_tanggal == $hariini) {
                $periode = 'hari_ini';
                $labelPeriode = 'Hari Ini (' . date('d M Y') . ')';
            } else {
                $periode = 'custom';
                $labelPeriode = date('d/m/Y', strtotime($dari_tanggal));
            }
        }

        $layananList = DB::table('layanan')->orderBy('nama_layanan')->get();

        if ($dari_tanggal == $sampai_tanggal) {
            // Tampilan per hari spesifik (menampilkan seluruh armada layanan)
            $singleDate = $dari_tanggal;
            $hariNama = $namahari_array[date('w', strtotime($singleDate))];

            $query = DB::table('layanan')
                ->leftJoin('jadwal_lokasi_layanan as j_khusus', function ($join) use ($singleDate) {
                    $join->on('j_khusus.layanan_id', '=', 'layanan.id')
                        ->where('j_khusus.jenis_jadwal', '=', 'khusus')
                        ->where('j_khusus.tanggal', '=', $singleDate)
                        ->where('j_khusus.status', '=', 1);
                })
                ->leftJoin('jadwal_lokasi_layanan as j_rutin', function ($join) use ($hariNama) {
                    $join->on('j_rutin.layanan_id', '=', 'layanan.id')
                        ->where('j_rutin.jenis_jadwal', '=', 'rutin')
                        ->where('j_rutin.hari', '=', $hariNama)
                        ->where('j_rutin.status', '=', 1);
                })
                ->leftJoin('cabang', DB::raw("COALESCE(j_khusus.kode_cabang, j_rutin.kode_cabang, layanan.kode_cabang)"), '=', 'cabang.kode_cabang')
                ->leftJoin('jam_kerja', DB::raw("COALESCE(j_khusus.kode_jam_kerja, j_rutin.kode_jam_kerja)"), '=', 'jam_kerja.kode_jam_kerja')
                ->leftJoin('presensi', function ($join) use ($singleDate) {
                    $join->on('layanan.id', '=', 'presensi.nik')
                        ->where('presensi.tgl_presensi', '=', $singleDate);
                });

            if (!empty($layanan_id) && $layanan_id != 'all') {
                $query->where('layanan.id', $layanan_id);
            }

            $query->select(
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
                DB::raw("COALESCE(presensi.tgl_presensi, '{$singleDate}') as tgl_presensi"),
                'presensi.jam_in',
                'presensi.jam_out',
                'presensi.foto_in',
                'presensi.foto_out',
                'presensi.lokasi_in',
                'presensi.lokasi_out',
                'presensi.status as status_presensi'
            )->orderBy('layanan.nama_layanan', 'asc');
        } else {
            // Tampilan rentang tanggal (multi-hari: Minggu Ini, Bulan Ini, Tahun Ini, Custom Rentang)
            $query = DB::table('presensi')
                ->join('layanan', 'presensi.nik', '=', 'layanan.id')
                ->whereBetween('presensi.tgl_presensi', [$dari_tanggal, $sampai_tanggal])
                ->leftJoin('jadwal_lokasi_layanan as j_khusus', function ($join) {
                    $join->on('j_khusus.layanan_id', '=', 'layanan.id')
                        ->on('j_khusus.tanggal', '=', 'presensi.tgl_presensi')
                        ->where('j_khusus.jenis_jadwal', '=', 'khusus')
                        ->where('j_khusus.status', '=', 1);
                })
                ->leftJoin('jadwal_lokasi_layanan as j_rutin', function ($join) {
                    $join->on('j_rutin.layanan_id', '=', 'layanan.id')
                        ->whereRaw("j_rutin.hari = CASE DAYOFWEEK(presensi.tgl_presensi)
                            WHEN 1 THEN 'Minggu'
                            WHEN 2 THEN 'Senin'
                            WHEN 3 THEN 'Selasa'
                            WHEN 4 THEN 'Rabu'
                            WHEN 5 THEN 'Kamis'
                            WHEN 6 THEN 'Jumat'
                            WHEN 7 THEN 'Sabtu'
                        END")
                        ->where('j_rutin.jenis_jadwal', '=', 'rutin')
                        ->where('j_rutin.status', '=', 1);
                })
                ->leftJoin('cabang', DB::raw("COALESCE(j_khusus.kode_cabang, j_rutin.kode_cabang, layanan.kode_cabang)"), '=', 'cabang.kode_cabang')
                ->leftJoin('jam_kerja', DB::raw("COALESCE(j_khusus.kode_jam_kerja, j_rutin.kode_jam_kerja)"), '=', 'jam_kerja.kode_jam_kerja');

            if (!empty($layanan_id) && $layanan_id != 'all') {
                $query->where('layanan.id', $layanan_id);
            }

            $query->select(
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
            ->orderBy('presensi.tgl_presensi', 'desc')
            ->orderBy('presensi.id', 'desc');
        }

        // Hitung akumulasi status keseluruhan sebelum dipaginasi
        $allRecords = (clone $query)->get();
        $statHadir = 0;
        $statMaintenance = 0;
        $statLibur = 0;
        $statTK = 0;

        foreach ($allRecords as $item) {
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

        // Paginasi per 10 data
        $presensiList = $query->paginate(10)->withQueryString();

        return view('monitoring.presensi', compact(
            'presensiList',
            'layananList',
            'periode',
            'dari_tanggal',
            'sampai_tanggal',
            'labelPeriode',
            'layanan_id',
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
        $periode = $request->periode ?: 'bulan_ini';
        $layanan_id = $request->layanan_id;
        $namabulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $hariini = date('Y-m-d');
        if ($periode == 'minggu_ini') {
            $dari_tanggal = Carbon::now()->startOfWeek()->toDateString();
            $sampai_tanggal = Carbon::now()->endOfWeek()->toDateString();
            $labelPeriode = 'Minggu Ini (' . date('d M', strtotime($dari_tanggal)) . ' - ' . date('d M Y', strtotime($sampai_tanggal)) . ')';
        } elseif ($periode == 'tahun_ini') {
            $dari_tanggal = date('Y-01-01');
            $sampai_tanggal = date('Y-12-31');
            $labelPeriode = 'Tahun ' . date('Y');
        } elseif ($periode == 'custom') {
            $dari_tanggal = $request->dari_tanggal ?: date('Y-m-01');
            $sampai_tanggal = $request->sampai_tanggal ?: date('Y-m-d');
            $labelPeriode = date('d/m/Y', strtotime($dari_tanggal)) . ' - ' . date('d/m/Y', strtotime($sampai_tanggal));
        } else {
            // Default: bulan_ini
            $periode = 'bulan_ini';
            $dari_tanggal = date('Y-m-01');
            $sampai_tanggal = date('Y-m-t');
            $labelPeriode = $namabulan[(int)date('m')] . ' ' . date('Y');
        }

        $layananList = DB::table('layanan')->orderBy('nama_layanan')->get();

        $query = DB::table('rekap_penerimaan_pkb')
            ->join('layanan', 'rekap_penerimaan_pkb.layanan_id', '=', 'layanan.id')
            ->leftJoin('cabang', 'rekap_penerimaan_pkb.kode_cabang', '=', 'cabang.kode_cabang')
            ->whereBetween('rekap_penerimaan_pkb.tgl_penerimaan', [$dari_tanggal, $sampai_tanggal]);

        if (!empty($layanan_id) && $layanan_id != 'all') {
            $query->where('rekap_penerimaan_pkb.layanan_id', $layanan_id);
        }

        // Hitung total akumulasi sesuai filter
        $summary = (clone $query)->selectRaw('
            COALESCE(SUM(rekap_penerimaan_pkb.total_rp), 0) as total_rp,
            COALESCE(SUM(rekap_penerimaan_pkb.total_unit), 0) as total_unit,
            COALESCE(SUM(rekap_penerimaan_pkb.jumlah_notice), 0) as total_notice,
            COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r2), 0) as total_r2_rp,
            COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r4), 0) as total_r4_rp,
            COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r2), 0) as total_unit_r2,
            COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r4), 0) as total_unit_r4
        ')->first();

        $totalRp = (float) ($summary->total_rp ?? 0);
        $totalUnit = (int) ($summary->total_unit ?? 0);
        $totalNotice = (int) ($summary->total_notice ?? 0);
        $totalR2Rp = (float) ($summary->total_r2_rp ?? 0);
        $totalR4Rp = (float) ($summary->total_r4_rp ?? 0);
        $totalUnitR2 = (int) ($summary->total_unit_r2 ?? 0);
        $totalUnitR4 = (int) ($summary->total_unit_r4 ?? 0);

        // Ambil data 10 per halaman dengan pagination
        $rekapList = $query->select(
            'rekap_penerimaan_pkb.*',
            'layanan.nama_layanan',
            'layanan.foto as foto_layanan',
            'cabang.nama_cabang as nama_lokasi'
        )
            ->orderBy('rekap_penerimaan_pkb.tgl_penerimaan', 'desc')
            ->orderBy('rekap_penerimaan_pkb.id', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('monitoring.penerimaan', compact(
            'rekapList',
            'layananList',
            'periode',
            'dari_tanggal',
            'sampai_tanggal',
            'labelPeriode',
            'layanan_id',
            'totalRp',
            'totalUnit',
            'totalNotice',
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
            'no_hp' => 'nullable|string|max:25',
            'password' => 'nullable|min:6'
        ]);

        $data = [
            'name' => $request->name,
            'no_hp' => $request->no_hp
        ];
        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')->where('id', $user->id)->update($data);

        return Redirect::back()->with(['success' => 'Profil berhasil diperbarui.']);
    }
}

