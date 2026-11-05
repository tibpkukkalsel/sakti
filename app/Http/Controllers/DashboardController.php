<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m") * 1; //1 atau Januari
        $tahunini = date("Y"); // 2023
        $nik = Auth::guard('karyawan')->user()->nik;
        $kode_cabang = Auth::guard('karyawan')->user()->kode_cabang;

        $kode_dept = Auth::guard('karyawan')->user()->kode_dept;
        $presensihariini = DB::table('presensi')
            ->select('presensi.*', 'pengajuan_izin.keterangan')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->where('presensi.nik', $nik)
            ->where('presensi.tgl_presensi', $hariini)
            ->first();

        // Cek hari libur hari ini untuk pegawai
        $dataliburhariini = DB::table('harilibur')
            ->leftJoin('harilibur_detail', 'harilibur.kode_libur', '=', 'harilibur_detail.kode_libur')
            ->where('harilibur.tanggal_libur', $hariini)
            ->where(function ($query) use ($nik, $kode_cabang) {
                $query->where('harilibur_detail.nik', $nik)
                    ->orWhere('harilibur.kode_cabang', 'ALL')
                    ->orWhere('harilibur.kode_cabang', $kode_cabang);
            })
            ->select('harilibur.kode_libur', 'harilibur.tanggal_libur', 'harilibur.keterangan')
            ->first();

        if (!$dataliburhariini && $presensihariini && $presensihariini->status == 'l') {
            $dataliburhariini = (object)[
                'kode_libur' => 'LIBUR',
                'tanggal_libur' => $hariini,
                'keterangan' => !empty($presensihariini->keterangan) ? $presensihariini->keterangan : 'Libur Nasional / Bersama'
            ];
        }

        // Rentang tanggal minggu yang berjalan (Senin sampai Minggu)
        $startOfWeek = Carbon::now()->startOfWeek()->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek()->toDateString();

        $historimingguini = DB::table('presensi')
            ->select('presensi.*', 'keterangan', 'jam_kerja.*', 'doc_sid', 'nama_cuti')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->leftJoin('pengajuan_izin', 'presensi.kode_izin', '=', 'pengajuan_izin.kode_izin')
            ->leftJoin('master_cuti', 'pengajuan_izin.kode_cuti', '=', 'master_cuti.kode_cuti')
            ->where('presensi.nik', $nik)
            ->whereBetween('presensi.tgl_presensi', [$startOfWeek, $endOfWeek])
            ->orderBy('tgl_presensi', 'desc')
            ->get();

        // Ambil hari libur minggu ini untuk pegawai dan gabungkan ke histori
        $hariliburmingguini = DB::table('harilibur')
            ->leftJoin('harilibur_detail', 'harilibur.kode_libur', '=', 'harilibur_detail.kode_libur')
            ->whereBetween('harilibur.tanggal_libur', [$startOfWeek, $endOfWeek])
            ->where(function ($query) use ($nik, $kode_cabang) {
                $query->where('harilibur_detail.nik', $nik)
                    ->orWhere('harilibur.kode_cabang', 'ALL')
                    ->orWhere('harilibur.kode_cabang', $kode_cabang);
            })
            ->select('harilibur.tanggal_libur', 'harilibur.keterangan')
            ->distinct()
            ->get();

        $existingDates = $historimingguini->pluck('tgl_presensi')->toArray();
        $historyList = $historimingguini->map(function ($item) use ($hariliburmingguini) {
            if ($item->status == 'l' && empty($item->keterangan)) {
                $hl = $hariliburmingguini->firstWhere('tanggal_libur', $item->tgl_presensi);
                if ($hl) {
                    $item->keterangan = $hl->keterangan;
                }
            }
            return $item;
        });

        foreach ($hariliburmingguini as $hl) {
            if (!in_array($hl->tanggal_libur, $existingDates)) {
                $historyList->push((object)[
                    'id' => null,
                    'nik' => $nik,
                    'tgl_presensi' => $hl->tanggal_libur,
                    'jam_in' => null,
                    'jam_out' => null,
                    'foto_in' => null,
                    'foto_out' => null,
                    'lokasi_in' => null,
                    'lokasi_out' => null,
                    'status' => 'l',
                    'status_in' => null,
                    'status_out' => null,
                    'kode_jam_kerja' => null,
                    'keterangan' => $hl->keterangan,
                    'nama_jam_kerja' => 'Libur Nasional',
                    'jam_masuk' => null,
                    'jam_pulang' => null,
                    'doc_sid' => null,
                    'nama_cuti' => null,
                    'kode_izin' => null
                ]);
            }
        }

        $historimingguini = $historyList->sortByDesc('tgl_presensi')->values();
        $historibulanini = $historimingguini; // Kompatibilitas alias

        $rekappresensi = DB::table('presensi')
            ->selectRaw('
            SUM(IF(status="h",1,0)) as jmlhadir,
            SUM(IF(status="i",1,0)) as jmlizin,
            SUM(IF(status="s",1,0)) as jmlsakit,
            SUM(IF(status="c",1,0)) as jmlcuti,
            SUM(IF(status="h" AND ((jam_in IS NOT NULL AND jam_in != "" AND jam_in > jam_masuk) OR jam_in IS NULL OR jam_in = ""), 1, 0)) as jmlterlambat
            ')
            ->leftJoin('jam_kerja', 'presensi.kode_jam_kerja', '=', 'jam_kerja.kode_jam_kerja')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulanini . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahunini . '"')
            ->first();

        $totalLiburBulanIni = count($hariliburmingguini);

        $leaderboard = DB::table('presensi')
            ->join('pegawai', 'presensi.nik', '=', 'pegawai.nik')
            ->where('tgl_presensi', $hariini)
            ->orderBy('jam_in')
            ->get();
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        $namahari = (new PresensiController)->gethari(date('D', strtotime($hariini)));
        $jadwalHariIni = DB::table('jadwal_lokasi_layanan')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('jadwal_lokasi_layanan.layanan_id', Auth::guard('karyawan')->user()->id)
            ->where('jadwal_lokasi_layanan.hari', $namahari)
            ->where('jadwal_lokasi_layanan.status', 1)
            ->select('cabang.*', 'jadwal_lokasi_layanan.keterangan as ket_jadwal')
            ->first();

        if ($jadwalHariIni) {
            $cabang = $jadwalHariIni;
        } else {
            $cabang = DB::table('cabang')->where('kode_cabang', $kode_cabang)->first();
        }
        $departemen = DB::table('seksi')->where('kode_dept', $kode_dept)->first();

        // Penerimaan PKB hari ini untuk akun layanan ini
        $penerimaanHariIniLayanan = DB::table('rekap_penerimaan_pkb')
            ->where('layanan_id', Auth::guard('karyawan')->user()->id)
            ->where('tgl_penerimaan', $hariini)
            ->first();

        return view('dashboard.dashboard', compact('presensihariini', 'dataliburhariini', 'historimingguini', 'historibulanini', 'namabulan', 'bulanini', 'tahunini', 'rekappresensi', 'totalLiburBulanIni', 'leaderboard', 'cabang', 'departemen', 'startOfWeek', 'endOfWeek', 'penerimaanHariIniLayanan', 'jadwalHariIni', 'namahari', 'hariini'));
    }

    /**
     * Dashboard 1: Presensi Layanan
     * Menampilkan Card ringkasan kehadiran akun layanan hari ini
     * serta tabel monitoring layanan, lokasi tugas, jam masuk & jam keluar (tanpa button aksi).
     */
    public function dashboardadmin()
    {
        $hariini = date("Y-m-d");
        $user = Auth::guard('user')->user();

        // 1. Total Akun Layanan
        $totalLayanan = DB::table('layanan')->count();

        // 2. Nama hari bahasa Indonesia (Senin, Selasa, dll)
        $namahari = (new PresensiController)->gethari(date('D', strtotime($hariini)));

        // 3. Ambil data seluruh akun layanan beserta cabang default-nya
        $listLayanan = DB::table('layanan')
            ->select('layanan.*', 'cabang.nama_cabang as nama_cabang_default')
            ->leftJoin('cabang', 'layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->orderBy('layanan.nama_layanan', 'asc')
            ->get();

        // 4. Presensi hari ini
        $presensiHariIni = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->get()
            ->keyBy(function ($item) {
                return (string) $item->nik;
            });

        // 5. Jadwal lokasi pelayanan hari ini (dinamis per hari)
        $jadwalHariIni = DB::table('jadwal_lokasi_layanan')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.kode_cabang')
            ->where('jadwal_lokasi_layanan.hari', $namahari)
            ->where('jadwal_lokasi_layanan.status', 1)
            ->select('jadwal_lokasi_layanan.*', 'cabang.nama_cabang', 'cabang.lokasi_cabang', 'cabang.radius_cabang')
            ->get()
            ->keyBy('layanan_id');

        $jmlLayananAktif = 0;
        $jmlLayananPulang = 0;
        $monitoringLayanan = [];

        foreach ($listLayanan as $l) {
            $p = $presensiHariIni->get((string) $l->id) ?: $presensiHariIni->get((string) $l->username);
            $j = $jadwalHariIni->get($l->id);

            // Tentukan lokasi yang ditampilkan
            $lokasiTampil = '-';
            if ($j) {
                $lokasiTampil = $j->nama_cabang . ($j->keterangan ? ' (' . $j->keterangan . ')' : '');
            } elseif (!empty($l->nama_cabang_default)) {
                $lokasiTampil = $l->nama_cabang_default;
            }

            $jamIn = $p ? $p->jam_in : null;
            $jamOut = $p ? $p->jam_out : null;

            $statusText = 'Belum Hadir';
            $statusBadge = 'bg-secondary text-white';

            if ($p && !empty($jamIn)) {
                $jmlLayananAktif++;
                if (!empty($jamOut)) {
                    $jmlLayananPulang++;
                    $statusText = 'Sudah Pulang';
                    $statusBadge = 'bg-success text-white';
                } else {
                    $statusText = 'Sedang Bertugas';
                    $statusBadge = 'bg-primary text-white';
                }
            }

            $monitoringLayanan[] = (object) [
                'id' => $l->id,
                'nama_layanan' => $l->nama_layanan,
                'nama_pic' => $l->nama_pic,
                'nip_pic' => $l->nip_pic,
                'jabatan_pic' => $l->jabatan_pic,
                'foto' => $l->foto,
                'lokasi' => $lokasiTampil,
                'jam_in' => $jamIn,
                'jam_out' => $jamOut,
                'status_text' => $statusText,
                'status_badge' => $statusBadge,
            ];
        }

        // Hitung layanan yang izin / sakit / cuti hari ini
        $jmlIzinPresensi = DB::table('presensi')
            ->where('tgl_presensi', $hariini)
            ->whereIn('status', ['i', 's', 'c'])
            ->count();

        $jmlIzinApproved = DB::table('pengajuan_izin')
            ->where('status_approved', 1)
            ->where('tgl_izin_dari', '<=', $hariini)
            ->where('tgl_izin_sampai', '>=', $hariini)
            ->count();

        $jmlIzin = max($jmlIzinPresensi, $jmlIzinApproved);
        $jmlBelumAbsen = max(0, $totalLayanan - $jmlLayananAktif - $jmlIzin);

        return view('dashboard.dashboardadmin', compact(
            'totalLayanan',
            'jmlLayananAktif',
            'jmlLayananPulang',
            'jmlIzin',
            'jmlBelumAbsen',
            'monitoringLayanan',
            'hariini',
            'namahari'
        ));
    }

    /**
     * Dashboard 2: Penerimaan PKB
     * Menampilkan Filter Periode & Layanan, Card ringkasan total,
     * Grafik tren bulanan, grafik bar ke samping lokasi/layanan tertinggi,
     * serta tabel data penerimaan tanpa kolom PIC.
     */
    public function dashboardPenerimaan(Request $request)
    {
        $user = Auth::guard('user')->user();
        $hariini = date('Y-m-d');
        $tahunini = date('Y');

        // Parameter filter
        $layanan_id = $request->layanan_id; // 'all' atau ID spesifik
        $periode = $request->periode ?: 'bulan_ini'; // 'hari_ini', 'bulan_ini', 'tahun_ini', 'custom'

        $tgl_dari = $request->tgl_dari;
        $tgl_sampai = $request->tgl_sampai;

        if ($periode == 'hari_ini') {
            $startDate = $hariini;
            $endDate = $hariini;
            $periodeLabel = 'Hari Ini (' . date('d-m-Y', strtotime($hariini)) . ')';
        } elseif ($periode == 'tahun_ini') {
            $startDate = date('Y-01-01');
            $endDate = date('Y-12-31');
            $periodeLabel = 'Tahun Ini (' . $tahunini . ')';
        } elseif ($periode == 'custom') {
            $startDate = $tgl_dari ?: date('Y-m-01');
            $endDate = $tgl_sampai ?: $hariini;
            $periodeLabel = date('d-m-Y', strtotime($startDate)) . ' s/d ' . date('d-m-Y', strtotime($endDate));
        } else {
            // Default: bulan_ini
            $periode = 'bulan_ini';
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            $namaBulanIndo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $periodeLabel = 'Bulan Ini (' . $namaBulanIndo[(int) date('m')] . ' ' . $tahunini . ')';
        }

        // 1. Query Agregasi Cards Ringkasan
        $queryCard = DB::table('rekap_penerimaan_pkb')
            ->whereBetween('tgl_penerimaan', [$startDate, $endDate]);

        if (!empty($layanan_id) && $layanan_id != 'all') {
            $queryCard->where('layanan_id', $layanan_id);
        }

        $summary = $queryCard->selectRaw('
            COALESCE(SUM(total_rp), 0) as total_rp,
            COALESCE(SUM(jumlah_rp_r2), 0) as sum_rp_r2,
            COALESCE(SUM(jumlah_unit_r2), 0) as sum_unit_r2,
            COALESCE(SUM(jumlah_rp_r4), 0) as sum_rp_r4,
            COALESCE(SUM(jumlah_unit_r4), 0) as sum_unit_r4,
            COALESCE(SUM(total_unit), 0) as sum_total_unit,
            COALESCE(SUM(jumlah_notice), 0) as sum_notice
        ')->first();

        // 2. Grafik Tren Penerimaan Per Bulan (Tahun Berjalan)
        $monthlyQuery = DB::table('rekap_penerimaan_pkb')
            ->whereYear('tgl_penerimaan', $tahunini);

        if (!empty($layanan_id) && $layanan_id != 'all') {
            $monthlyQuery->where('layanan_id', $layanan_id);
        }

        $monthlyDataRaw = $monthlyQuery->selectRaw('
            MONTH(tgl_penerimaan) as bulan,
            COALESCE(SUM(total_rp), 0) as total_rp,
            COALESCE(SUM(jumlah_rp_r2), 0) as rp_r2,
            COALESCE(SUM(jumlah_rp_r4), 0) as rp_r4
        ')
        ->groupBy(DB::raw('MONTH(tgl_penerimaan)'))
        ->get()
        ->keyBy('bulan');

        $namaBulanSingkat = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartBulanLabels = $namaBulanSingkat;
        $chartBulanValuesTotal = [];
        $chartBulanValuesR2 = [];
        $chartBulanValuesR4 = [];

        for ($m = 1; $m <= 12; $m++) {
            $row = $monthlyDataRaw->get($m);
            $chartBulanValuesTotal[] = (float) ($row ? $row->total_rp : 0);
            $chartBulanValuesR2[] = (float) ($row ? $row->rp_r2 : 0);
            $chartBulanValuesR4[] = (float) ($row ? $row->rp_r4 : 0);
        }

        // 3. Grafik Bar ke Samping: 5 Lokasi yang paling banyak mendapatkan penerimaan (Top 5 Lokasi)
        $topLokasiQuery = DB::table('rekap_penerimaan_pkb')
            ->join('layanan', 'rekap_penerimaan_pkb.layanan_id', '=', 'layanan.id')
            ->leftJoin('jadwal_lokasi_layanan', function ($join) {
                $join->on('jadwal_lokasi_layanan.layanan_id', '=', 'rekap_penerimaan_pkb.layanan_id')
                    ->where('jadwal_lokasi_layanan.status', 1)
                    ->whereRaw("jadwal_lokasi_layanan.hari = CASE DAYOFWEEK(rekap_penerimaan_pkb.tgl_penerimaan)
                        WHEN 1 THEN 'Minggu'
                        WHEN 2 THEN 'Senin'
                        WHEN 3 THEN 'Selasa'
                        WHEN 4 THEN 'Rabu'
                        WHEN 5 THEN 'Kamis'
                        WHEN 6 THEN 'Jumat'
                        WHEN 7 THEN 'Sabtu'
                    END");
            })
            ->leftJoin('cabang as c_jadwal', 'jadwal_lokasi_layanan.kode_cabang', '=', 'c_jadwal.kode_cabang')
            ->leftJoin('cabang as c_default', 'layanan.kode_cabang', '=', 'c_default.kode_cabang')
            ->whereBetween('rekap_penerimaan_pkb.tgl_penerimaan', [$startDate, $endDate]);

        if (!empty($layanan_id) && $layanan_id != 'all') {
            $topLokasiQuery->where('rekap_penerimaan_pkb.layanan_id', $layanan_id);
        }

        $topLokasi = $topLokasiQuery->select(
            DB::raw("COALESCE(c_jadwal.nama_cabang, c_default.nama_cabang, 'Lainnya') as nama_lokasi"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.total_rp), 0) as total_rp"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.total_unit), 0) as total_unit"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_notice), 0) as total_notice")
        )
        ->groupBy(DB::raw("COALESCE(c_jadwal.nama_cabang, c_default.nama_cabang, 'Lainnya')"))
        ->orderBy('total_rp', 'desc')
        ->limit(5)
        ->get();

        $chartLokasiLabels = $topLokasi->pluck('nama_lokasi')->toArray();
        $chartLokasiValues = $topLokasi->pluck('total_rp')->map(fn($v) => (float)$v)->toArray();

        // 4. Grafik Bar ke Samping: Layanan yang paling banyak mendapatkan penerimaan
        $topLayananQuery = DB::table('rekap_penerimaan_pkb')
            ->join('layanan', 'rekap_penerimaan_pkb.layanan_id', '=', 'layanan.id')
            ->whereBetween('rekap_penerimaan_pkb.tgl_penerimaan', [$startDate, $endDate]);

        if (!empty($layanan_id) && $layanan_id != 'all') {
            $topLayananQuery->where('rekap_penerimaan_pkb.layanan_id', $layanan_id);
        }

        $topLayanan = $topLayananQuery->selectRaw('
            layanan.nama_layanan,
            COALESCE(SUM(rekap_penerimaan_pkb.total_rp), 0) as total_rp,
            COALESCE(SUM(rekap_penerimaan_pkb.total_unit), 0) as total_unit,
            COALESCE(SUM(rekap_penerimaan_pkb.jumlah_notice), 0) as total_notice
        ')
        ->groupBy('layanan.id', 'layanan.nama_layanan')
        ->orderBy('total_rp', 'desc')
        ->limit(10)
        ->get();

        $chartTopLabels = $topLayanan->pluck('nama_layanan')->toArray();
        $chartTopValues = $topLayanan->pluck('total_rp')->map(fn($v) => (float)$v)->toArray();

        // 5. Data Tabel Rekapitulasi (Tanpa PIC sesuai permintaan user)
        $queryTabel = DB::table('rekap_penerimaan_pkb')
            ->join('layanan', 'rekap_penerimaan_pkb.layanan_id', '=', 'layanan.id')
            ->whereBetween('rekap_penerimaan_pkb.tgl_penerimaan', [$startDate, $endDate])
            ->select(
                'rekap_penerimaan_pkb.*',
                'layanan.nama_layanan'
            )
            ->orderBy('rekap_penerimaan_pkb.tgl_penerimaan', 'desc');

        if (!empty($layanan_id) && $layanan_id != 'all') {
            $queryTabel->where('rekap_penerimaan_pkb.layanan_id', $layanan_id);
        }

        $dataPenerimaan = $queryTabel->paginate(25)->withQueryString();
        $daftarLayanan = DB::table('layanan')->orderBy('nama_layanan', 'asc')->get();

        return view('dashboard.dashboard_penerimaan', compact(
            'summary',
            'daftarLayanan',
            'dataPenerimaan',
            'layanan_id',
            'periode',
            'periodeLabel',
            'startDate',
            'endDate',
            'tgl_dari',
            'tgl_sampai',
            'chartBulanLabels',
            'chartBulanValuesTotal',
            'chartBulanValuesR2',
            'chartBulanValuesR4',
            'chartTopLabels',
            'chartTopValues',
            'chartLokasiLabels',
            'chartLokasiValues',
            'tahunini'
        ));
    }
}
