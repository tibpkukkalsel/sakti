<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\RekapPenerimaanPkb;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class PenerimaanController extends Controller
{
    // ==========================================
    // BAGIAN LAYANAN (MOBILE / KARYAWAN GUARD)
    // ==========================================

    public function index(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->id;
        $hariini = date('Y-m-d');

        // Cek apakah hari ini sudah ada input
        $penerimaanHariIni = RekapPenerimaanPkb::where('layanan_id', $layananId)
            ->where('tgl_penerimaan', $hariini)
            ->first();

        // Filter: default 'minggu' (Minggu Ini), opsi: 'bulan', 'tahun', 'custom'
        $filter = $request->get('filter', 'minggu');
        if (!in_array($filter, ['minggu', 'bulan', 'tahun', 'custom'])) {
            $filter = 'minggu';
        }

        $dari = $request->get('dari', date('Y-m-01'));
        $sampai = $request->get('sampai', date('Y-m-d'));

        // Ambil query histori penerimaan akun layanan ini sesuai filter
        $query = RekapPenerimaanPkb::where('layanan_id', $layananId);

        if ($filter === 'minggu') {
            $startOfWeek = Carbon::now()->startOfWeek()->format('Y-m-d');
            $endOfWeek = Carbon::now()->endOfWeek()->format('Y-m-d');
            $query->whereBetween('tgl_penerimaan', [$startOfWeek, $endOfWeek]);
            $labelAkumulasi = 'Akumulasi Minggu Ini';
        } elseif ($filter === 'bulan') {
            $query->whereMonth('tgl_penerimaan', date('m'))
                ->whereYear('tgl_penerimaan', date('Y'));
            $labelAkumulasi = 'Akumulasi Bulan ' . Carbon::now()->translatedFormat('F Y');
        } elseif ($filter === 'tahun') {
            $query->whereYear('tgl_penerimaan', date('Y'));
            $labelAkumulasi = 'Akumulasi Tahun ' . date('Y');
        } elseif ($filter === 'custom') {
            if (!empty($dari) && !empty($sampai)) {
                $query->whereBetween('tgl_penerimaan', [$dari, $sampai]);
                $labelAkumulasi = 'Akumulasi (' . Carbon::parse($dari)->format('d/m/Y') . ' - ' . Carbon::parse($sampai)->format('d/m/Y') . ')';
            } elseif (!empty($dari)) {
                $query->where('tgl_penerimaan', '>=', $dari);
                $labelAkumulasi = 'Akumulasi (Mulai ' . Carbon::parse($dari)->format('d/m/Y') . ')';
            } elseif (!empty($sampai)) {
                $query->where('tgl_penerimaan', '<=', $sampai);
                $labelAkumulasi = 'Akumulasi (Sampai ' . Carbon::parse($sampai)->format('d/m/Y') . ')';
            } else {
                $labelAkumulasi = 'Akumulasi Periode';
            }
        }

        // Hitung total penerimaan akumulasi mengikuti filter yang aktif
        $totalAkumulasi = (clone $query)
            ->selectRaw('SUM(total_rp) as total_rp, SUM(total_unit) as total_unit, SUM(jumlah_notice) as total_notice')
            ->first();

        // Ambil histori penerimaan terpaginasi (6 item per halaman)
        $histori = $query->orderBy('tgl_penerimaan', 'desc')
            ->paginate(6)
            ->withQueryString();

        return view('penerimaan.index', compact('penerimaanHariIni', 'histori', 'totalAkumulasi', 'labelAkumulasi', 'user', 'filter', 'dari', 'sampai'));
    }

    public function create()
    {
        $user = Auth::guard('karyawan')->user();
        $tglDefault = date('Y-m-d');
        return view('penerimaan.create', compact('user', 'tglDefault'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->id;

        $request->validate([
            'tgl_penerimaan' => 'required|date',
            'jumlah_rp_r2' => 'nullable',
            'jumlah_rp_r4' => 'nullable',
            'jumlah_unit_r2' => 'nullable|numeric|min:0',
            'jumlah_unit_r4' => 'nullable|numeric|min:0',
            'jumlah_notice' => 'nullable|numeric|min:0',
        ]);

        // Bersihkan format rupiah jika ada titik atau koma
        $rpR2 = (float) preg_replace('/[^0-9]/', '', $request->jumlah_rp_r2);
        $rpR4 = (float) preg_replace('/[^0-9]/', '', $request->jumlah_rp_r4);
        $unitR2 = (int) $request->jumlah_unit_r2;
        $unitR4 = (int) $request->jumlah_unit_r4;
        $notice = (int) $request->jumlah_notice;
        $tgl = $request->tgl_penerimaan;

        $totalRp = $rpR2 + $rpR4;
        $totalUnit = $unitR2 + $unitR4;

        // Cek jika data pada tanggal tersebut sudah ada dan dikunci oleh admin
        $existing = RekapPenerimaanPkb::where('layanan_id', $layananId)
            ->where('tgl_penerimaan', $tgl)
            ->first();

        if ($existing && $existing->is_locked) {
            return redirect()->back()->withInput()->with(['error' => 'Data penerimaan untuk tanggal tersebut telah dikunci oleh admin dan tidak dapat diubah lagi.']);
        }

        try {
            RekapPenerimaanPkb::updateOrCreate(
                [
                    'layanan_id' => $layananId,
                    'tgl_penerimaan' => $tgl,
                ],
                [
                    'jumlah_rp_r2' => $rpR2,
                    'jumlah_rp_r4' => $rpR4,
                    'total_rp' => $totalRp,
                    'jumlah_unit_r2' => $unitR2,
                    'jumlah_unit_r4' => $unitR4,
                    'total_unit' => $totalUnit,
                    'jumlah_notice' => $notice,
                    'keterangan' => $request->keterangan,
                ]
            );

            return redirect('/penerimaan')->with(['success' => 'Rekap Penerimaan PKB berhasil disimpan!']);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with(['warning' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $user = Auth::guard('karyawan')->user();
        $data = RekapPenerimaanPkb::where('id', $id)
            ->where('layanan_id', $user->id)
            ->firstOrFail();

        if ($data->is_locked) {
            return redirect('/penerimaan')->with(['warning' => 'Data penerimaan ini telah dikunci oleh admin dan tidak dapat diubah lagi.']);
        }

        return view('penerimaan.edit', compact('data', 'user'));
    }

    public function update($id, Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $data = RekapPenerimaanPkb::where('id', $id)
            ->where('layanan_id', $user->id)
            ->firstOrFail();

        if ($data->is_locked) {
            return redirect('/penerimaan')->with(['error' => 'Data penerimaan ini telah dikunci oleh admin dan tidak dapat diubah lagi.']);
        }

        $rpR2 = (float) preg_replace('/[^0-9]/', '', $request->jumlah_rp_r2);
        $rpR4 = (float) preg_replace('/[^0-9]/', '', $request->jumlah_rp_r4);
        $unitR2 = (int) $request->jumlah_unit_r2;
        $unitR4 = (int) $request->jumlah_unit_r4;
        $notice = (int) $request->jumlah_notice;

        $totalRp = $rpR2 + $rpR4;
        $totalUnit = $unitR2 + $unitR4;

        try {
            $data->update([
                'tgl_penerimaan' => $request->tgl_penerimaan ?: $data->tgl_penerimaan,
                'jumlah_rp_r2' => $rpR2,
                'jumlah_rp_r4' => $rpR4,
                'total_rp' => $totalRp,
                'jumlah_unit_r2' => $unitR2,
                'jumlah_unit_r4' => $unitR4,
                'total_unit' => $totalUnit,
                'jumlah_notice' => $notice,
                'keterangan' => $request->keterangan,
            ]);

            return redirect('/penerimaan')->with(['success' => 'Data Penerimaan PKB berhasil diperbarui!']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Gagal memperbarui: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $user = Auth::guard('karyawan')->user();
        $data = RekapPenerimaanPkb::where('id', $id)
            ->where('layanan_id', $user->id)
            ->firstOrFail();

        if ($data->is_locked) {
            return redirect('/penerimaan')->with(['error' => 'Data penerimaan ini telah dikunci oleh admin dan tidak dapat dihapus lagi.']);
        }

        $data->delete();
        return redirect('/penerimaan')->with(['success' => 'Data berhasil dihapus']);
    }

    // ==========================================
    // BAGIAN ADMIN PANEL (USER GUARD)
    // ==========================================

    public function adminIndex(Request $request)
    {
        $tgl_dari = $request->tgl_dari ?: date('Y-m-01');
        $tgl_sampai = $request->tgl_sampai ?: date('Y-m-d');
        $layanan_id = $request->layanan_id;

        $query = RekapPenerimaanPkb::with('layanan')
            ->whereBetween('tgl_penerimaan', [$tgl_dari, $tgl_sampai])
            ->orderBy('tgl_penerimaan', 'desc');

        if (!empty($layanan_id)) {
            $query->where('layanan_id', $layanan_id);
        }

        $penerimaan = $query->paginate(20)->withQueryString();

        // Agregasi total
        $summaryQuery = RekapPenerimaanPkb::whereBetween('tgl_penerimaan', [$tgl_dari, $tgl_sampai]);
        if (!empty($layanan_id)) {
            $summaryQuery->where('layanan_id', $layanan_id);
        }

        $summary = $summaryQuery->selectRaw('
            SUM(jumlah_rp_r2) as sum_rp_r2,
            SUM(jumlah_rp_r4) as sum_rp_r4,
            SUM(total_rp) as sum_total_rp,
            SUM(jumlah_unit_r2) as sum_unit_r2,
            SUM(jumlah_unit_r4) as sum_unit_r4,
            SUM(total_unit) as sum_total_unit,
            SUM(jumlah_notice) as sum_notice
        ')->first();

        $daftarLayanan = Layanan::orderBy('nama_layanan')->get();

        return view('penerimaan.admin_index', compact('penerimaan', 'summary', 'daftarLayanan', 'tgl_dari', 'tgl_sampai', 'layanan_id'));
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required',
            'tgl_penerimaan' => 'required|date',
            'jumlah_rp_r2' => 'nullable',
            'jumlah_rp_r4' => 'nullable',
            'jumlah_unit_r2' => 'nullable|numeric|min:0',
            'jumlah_unit_r4' => 'nullable|numeric|min:0',
            'jumlah_notice' => 'nullable|numeric|min:0',
        ]);

        $rpR2 = (float) preg_replace('/[^0-9]/', '', $request->jumlah_rp_r2);
        $rpR4 = (float) preg_replace('/[^0-9]/', '', $request->jumlah_rp_r4);
        $unitR2 = (int) $request->jumlah_unit_r2;
        $unitR4 = (int) $request->jumlah_unit_r4;
        $notice = (int) $request->jumlah_notice;

        $totalRp = $rpR2 + $rpR4;
        $totalUnit = $unitR2 + $unitR4;

        try {
            RekapPenerimaanPkb::updateOrCreate(
                [
                    'layanan_id' => $request->layanan_id,
                    'tgl_penerimaan' => $request->tgl_penerimaan,
                ],
                [
                    'jumlah_rp_r2' => $rpR2,
                    'jumlah_rp_r4' => $rpR4,
                    'total_rp' => $totalRp,
                    'jumlah_unit_r2' => $unitR2,
                    'jumlah_unit_r4' => $unitR4,
                    'total_unit' => $totalUnit,
                    'jumlah_notice' => $notice,
                    'keterangan' => $request->keterangan,
                    'is_locked' => $request->has('is_locked') ? 1 : 0,
                ]
            );

            return Redirect::back()->with(['success' => 'Data Penerimaan PKB Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    public function adminEdit(Request $request)
    {
        $id = $request->id;
        $data = RekapPenerimaanPkb::with('layanan')->where('id', $id)->firstOrFail();
        $daftarLayanan = Layanan::orderBy('nama_layanan')->get();

        return view('penerimaan.admin_edit', compact('data', 'daftarLayanan'));
    }

    public function adminUpdate($id, Request $request)
    {
        $data = RekapPenerimaanPkb::findOrFail($id);

        $rpR2 = (float) preg_replace('/[^0-9]/', '', $request->jumlah_rp_r2);
        $rpR4 = (float) preg_replace('/[^0-9]/', '', $request->jumlah_rp_r4);
        $unitR2 = (int) $request->jumlah_unit_r2;
        $unitR4 = (int) $request->jumlah_unit_r4;
        $notice = (int) $request->jumlah_notice;

        $totalRp = $rpR2 + $rpR4;
        $totalUnit = $unitR2 + $unitR4;

        try {
            $data->update([
                'layanan_id' => $request->layanan_id ?: $data->layanan_id,
                'tgl_penerimaan' => $request->tgl_penerimaan ?: $data->tgl_penerimaan,
                'jumlah_rp_r2' => $rpR2,
                'jumlah_rp_r4' => $rpR4,
                'total_rp' => $totalRp,
                'jumlah_unit_r2' => $unitR2,
                'jumlah_unit_r4' => $unitR4,
                'total_unit' => $totalUnit,
                'jumlah_notice' => $notice,
                'keterangan' => $request->keterangan,
                'is_locked' => $request->has('is_locked') ? 1 : 0,
            ]);

            return Redirect::back()->with(['success' => 'Data Penerimaan PKB Berhasil Diupdate']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal mengupdate data: ' . $e->getMessage()]);
        }
    }

    public function adminDelete($id)
    {
        $data = RekapPenerimaanPkb::findOrFail($id);
        $data->delete();

        return Redirect::back()->with(['success' => 'Data Penerimaan PKB Berhasil Dihapus']);
    }

    public function adminToggleLock($id)
    {
        $data = RekapPenerimaanPkb::findOrFail($id);
        $data->is_locked = !$data->is_locked;
        $data->save();

        $statusText = $data->is_locked ? 'dikunci (user tidak dapat mengedit/menghapus)' : 'dibuka kuncinya (user dapat mengedit)';
        return Redirect::back()->with(['success' => "Data penerimaan tanggal " . date('d-m-Y', strtotime($data->tgl_penerimaan)) . " berhasil {$statusText}."]);
    }

    public function adminBulkLock(Request $request)
    {
        $lockAction = $request->input('lock_action', 'lock'); // 'lock' atau 'unlock'
        $tgl_dari = $request->tgl_dari;
        $tgl_sampai = $request->tgl_sampai;
        $layanan_id = $request->layanan_id;

        $query = RekapPenerimaanPkb::query();

        if (!empty($tgl_dari) && !empty($tgl_sampai)) {
            $query->whereBetween('tgl_penerimaan', [$tgl_dari, $tgl_sampai]);
        }
        if (!empty($layanan_id)) {
            $query->where('layanan_id', $layanan_id);
        }

        $newVal = ($lockAction === 'lock') ? 1 : 0;
        $count = $query->update(['is_locked' => $newVal]);

        $label = ($lockAction === 'lock') ? 'dikunci (user tidak dapat mengedit/menghapus)' : 'dibuka kuncinya';
        return Redirect::back()->with(['success' => "Sebanyak {$count} data penerimaan berhasil {$label}."]);
    }

    public function adminCetak(Request $request)
    {
        $tgl_dari = $request->tgl_dari ?: date('Y-m-01');
        $tgl_sampai = $request->tgl_sampai ?: date('Y-m-d');
        $layanan_id = $request->layanan_id;

        $query = RekapPenerimaanPkb::with('layanan')
            ->whereBetween('tgl_penerimaan', [$tgl_dari, $tgl_sampai])
            ->orderBy('tgl_penerimaan', 'asc');

        if (!empty($layanan_id)) {
            $query->where('layanan_id', $layanan_id);
        }

        $penerimaan = $query->get();

        $summary = (object)[
            'sum_rp_r2' => $penerimaan->sum('jumlah_rp_r2'),
            'sum_rp_r4' => $penerimaan->sum('jumlah_rp_r4'),
            'sum_total_rp' => $penerimaan->sum('total_rp'),
            'sum_unit_r2' => $penerimaan->sum('jumlah_unit_r2'),
            'sum_unit_r4' => $penerimaan->sum('jumlah_unit_r4'),
            'sum_total_unit' => $penerimaan->sum('total_unit'),
            'sum_notice' => $penerimaan->sum('jumlah_notice'),
        ];

        $filterLayanan = !empty($layanan_id) ? Layanan::find($layanan_id) : null;

        return view('penerimaan.cetak', compact('penerimaan', 'summary', 'tgl_dari', 'tgl_sampai', 'filterLayanan'));
    }

    /**
     * Tampilan Rekap Penerimaan PKB Per Layanan
     * Menampilkan grafik dan data tabel untuk penerimaan per layanan,
     * dilengkapi filter layanan yang dipilih dan periode tanggal.
     */
    public function rekapLayanan(Request $request)
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m");
        $tahunini = date("Y");

        $periode = $request->get("periode", "bulan_ini");
        $layanan_id = $request->get("layanan_id", "all");

        if ($periode == "hari_ini") {
            $startDate = $hariini;
            $endDate = $hariini;
            $periodeLabel = "Hari Ini (" . date("d/m/Y", strtotime($hariini)) . ")";
        } elseif ($periode == "tahun_ini") {
            $startDate = $tahunini . "-01-01";
            $endDate = $tahunini . "-12-31";
            $periodeLabel = "Tahun Ini (" . $tahunini . ")";
        } elseif ($periode == "custom") {
            $startDate = $request->get("tgl_dari", date("Y-m-01"));
            $endDate = $request->get("tgl_sampai", $hariini);
            $periodeLabel = date("d/m/Y", strtotime($startDate)) . " s/d " . date("d/m/Y", strtotime($endDate));
        } else {
            // Default: bulan_ini
            $periode = "bulan_ini";
            $startDate = date("Y-m-01");
            $endDate = date("Y-m-t");
            $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            $periodeLabel = "Bulan " . $namaBulan[(int)$bulanini] . " " . $tahunini;
        }

        $tgl_dari = $startDate;
        $tgl_sampai = $endDate;

        $daftarLayanan = DB::table("layanan")->orderBy("nama_layanan", "asc")->get();

        // Query data agregasi per layanan
        $query = DB::table("layanan")
            ->leftJoin("rekap_penerimaan_pkb", function ($join) use ($startDate, $endDate) {
                $join->on("layanan.id", "=", "rekap_penerimaan_pkb.layanan_id")
                    ->whereBetween("rekap_penerimaan_pkb.tgl_penerimaan", [$startDate, $endDate]);
            });

        if (!empty($layanan_id) && $layanan_id != "all") {
            $query->where("layanan.id", $layanan_id);
        }

        $rekapLayanan = $query->select(
            "layanan.id as layanan_id",
            "layanan.nama_layanan",
            "layanan.nama_pic",
            "layanan.nip_pic",
            "layanan.jabatan_pic",
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r2), 0) as rp_r2"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r2), 0) as unit_r2"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r4), 0) as rp_r4"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r4), 0) as unit_r4"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.total_rp), 0) as total_rp"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.total_unit), 0) as total_unit"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_notice), 0) as total_notice"),
            DB::raw("COUNT(rekap_penerimaan_pkb.id) as total_input")
        )
        ->groupBy("layanan.id", "layanan.nama_layanan", "layanan.nama_pic", "layanan.nip_pic", "layanan.jabatan_pic")
        ->orderBy("total_rp", "desc")
        ->get();

        // Hitung Grand Total
        $grandTotal = (object) [
            "rp_r2" => $rekapLayanan->sum("rp_r2"),
            "unit_r2" => $rekapLayanan->sum("unit_r2"),
            "rp_r4" => $rekapLayanan->sum("rp_r4"),
            "unit_r4" => $rekapLayanan->sum("unit_r4"),
            "total_rp" => $rekapLayanan->sum("total_rp"),
            "total_unit" => $rekapLayanan->sum("total_unit"),
            "total_notice" => $rekapLayanan->sum("total_notice"),
        ];

        // Siapkan Data Grafik
        $chartLabels = [];
        $chartValuesTotal = [];
        $chartValuesR2 = [];
        $chartValuesR4 = [];

        if (!empty($layanan_id) && $layanan_id != "all") {
            // Jika 1 layanan dipilih: Grafik tren harian pada periode tersebut
            $trenHarian = DB::table("rekap_penerimaan_pkb")
                ->where("layanan_id", $layanan_id)
                ->whereBetween("tgl_penerimaan", [$startDate, $endDate])
                ->orderBy("tgl_penerimaan", "asc")
                ->get();

            foreach ($trenHarian as $th) {
                $chartLabels[] = date("d/m", strtotime($th->tgl_penerimaan));
                $chartValuesTotal[] = (float) $th->total_rp;
                $chartValuesR2[] = (float) $th->jumlah_rp_r2;
                $chartValuesR4[] = (float) $th->jumlah_rp_r4;
            }
        } else {
            // Semua Layanan: Grafik Bar Perbandingan Antar Layanan
            foreach ($rekapLayanan as $row) {
                $chartLabels[] = $row->nama_layanan;
                $chartValuesTotal[] = (float) $row->total_rp;
                $chartValuesR2[] = (float) $row->rp_r2;
                $chartValuesR4[] = (float) $row->rp_r4;
            }
        }

        $layananTerpilih = (!empty($layanan_id) && $layanan_id != "all")
            ? DB::table("layanan")->where("id", $layanan_id)->first()
            : null;

        return view("penerimaan.rekap_layanan", compact(
            "daftarLayanan",
            "rekapLayanan",
            "grandTotal",
            "chartLabels",
            "chartValuesTotal",
            "chartValuesR2",
            "chartValuesR4",
            "layanan_id",
            "layananTerpilih",
            "periode",
            "periodeLabel",
            "tgl_dari",
            "tgl_sampai"
        ));
    }

    /**
     * Tampilan Rekap Penerimaan PKB Per Lokasi
     * Menampilkan grafik dan data tabel untuk penerimaan per lokasi pelayanan.
     */
    public function rekapLokasi(Request $request)
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m");
        $tahunini = date("Y");

        $periode = $request->get("periode", "bulan_ini");
        $kode_cabang = $request->get("kode_cabang", "all");

        if ($periode == "hari_ini") {
            $startDate = $hariini;
            $endDate = $hariini;
            $periodeLabel = "Hari Ini (" . date("d/m/Y", strtotime($hariini)) . ")";
        } elseif ($periode == "tahun_ini") {
            $startDate = $tahunini . "-01-01";
            $endDate = $tahunini . "-12-31";
            $periodeLabel = "Tahun Ini (" . $tahunini . ")";
        } elseif ($periode == "custom") {
            $startDate = $request->get("tgl_dari", date("Y-m-01"));
            $endDate = $request->get("tgl_sampai", $hariini);
            $periodeLabel = date("d/m/Y", strtotime($startDate)) . " s/d " . date("d/m/Y", strtotime($endDate));
        } else {
            // Default: bulan_ini
            $periode = "bulan_ini";
            $startDate = date("Y-m-01");
            $endDate = date("Y-m-t");
            $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            $periodeLabel = "Bulan " . $namaBulan[(int)$bulanini] . " " . $tahunini;
        }

        $tgl_dari = $startDate;
        $tgl_sampai = $endDate;

        $daftarLokasi = DB::table("cabang")->orderBy("nama_cabang", "asc")->get();

        // Query penerimaan dengan pemetaan lokasi jadwal harian atau default cabang layanan
        $query = DB::table("rekap_penerimaan_pkb")
            ->join("layanan", "rekap_penerimaan_pkb.layanan_id", "=", "layanan.id")
            ->leftJoin("jadwal_lokasi_layanan", function ($join) {
                $join->on("jadwal_lokasi_layanan.layanan_id", "=", "rekap_penerimaan_pkb.layanan_id")
                    ->where("jadwal_lokasi_layanan.status", 1)
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
            ->leftJoin("cabang as c_jadwal", "jadwal_lokasi_layanan.kode_cabang", "=", "c_jadwal.kode_cabang")
            ->leftJoin("cabang as c_default", "layanan.kode_cabang", "=", "c_default.kode_cabang")
            ->whereBetween("rekap_penerimaan_pkb.tgl_penerimaan", [$startDate, $endDate]);

        if (!empty($kode_cabang) && $kode_cabang != "all") {
            $query->whereRaw("COALESCE(c_jadwal.kode_cabang, c_default.kode_cabang) = ?", [$kode_cabang]);
        }

        $rekapLokasi = $query->select(
            DB::raw("COALESCE(c_jadwal.kode_cabang, c_default.kode_cabang, 'LAIN') as kode_cabang"),
            DB::raw("COALESCE(c_jadwal.nama_cabang, c_default.nama_cabang, 'Lokasi Lainnya') as nama_cabang"),
            DB::raw("COALESCE(c_jadwal.lokasi_cabang, c_default.lokasi_cabang, '-') as lokasi_cabang"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r2), 0) as rp_r2"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r2), 0) as unit_r2"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r4), 0) as rp_r4"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r4), 0) as unit_r4"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.total_rp), 0) as total_rp"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.total_unit), 0) as total_unit"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_notice), 0) as total_notice"),
            DB::raw("COUNT(rekap_penerimaan_pkb.id) as total_input")
        )
        ->groupBy(
            DB::raw("COALESCE(c_jadwal.kode_cabang, c_default.kode_cabang, 'LAIN')"),
            DB::raw("COALESCE(c_jadwal.nama_cabang, c_default.nama_cabang, 'Lokasi Lainnya')"),
            DB::raw("COALESCE(c_jadwal.lokasi_cabang, c_default.lokasi_cabang, '-')")
        )
        ->orderBy("total_rp", "desc")
        ->get();

        // Hitung Grand Total
        $grandTotal = (object) [
            "rp_r2" => $rekapLokasi->sum("rp_r2"),
            "unit_r2" => $rekapLokasi->sum("unit_r2"),
            "rp_r4" => $rekapLokasi->sum("rp_r4"),
            "unit_r4" => $rekapLokasi->sum("unit_r4"),
            "total_rp" => $rekapLokasi->sum("total_rp"),
            "total_unit" => $rekapLokasi->sum("total_unit"),
            "total_notice" => $rekapLokasi->sum("total_notice"),
        ];

        // Siapkan Data Grafik
        $chartLabels = $rekapLokasi->pluck("nama_cabang")->toArray();
        $chartValuesTotal = $rekapLokasi->pluck("total_rp")->map(fn($v) => (float)$v)->toArray();
        $chartValuesR2 = $rekapLokasi->pluck("rp_r2")->map(fn($v) => (float)$v)->toArray();
        $chartValuesR4 = $rekapLokasi->pluck("rp_r4")->map(fn($v) => (float)$v)->toArray();

        $lokasiTerpilih = (!empty($kode_cabang) && $kode_cabang != "all")
            ? DB::table("cabang")->where("kode_cabang", $kode_cabang)->first()
            : null;

        return view("penerimaan.rekap_lokasi", compact(
            "daftarLokasi",
            "rekapLokasi",
            "grandTotal",
            "chartLabels",
            "chartValuesTotal",
            "chartValuesR2",
            "chartValuesR4",
            "kode_cabang",
            "lokasiTerpilih",
            "periode",
            "periodeLabel",
            "tgl_dari",
            "tgl_sampai"
        ));
    }

    /**
     * Cetak Laporan Rekap Penerimaan PKB Per Layanan (PDF via mPDF / Excel)
     */
    public function cetakRekapLayanan(Request $request)
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m");
        $tahunini = date("Y");

        $periode = $request->get("periode", "bulan_ini");
        $layanan_id = $request->get("layanan_id", "all");

        if ($periode == "hari_ini") {
            $startDate = $hariini;
            $endDate = $hariini;
            $periodeLabel = "Hari Ini (" . date("d/m/Y", strtotime($hariini)) . ")";
        } elseif ($periode == "tahun_ini") {
            $startDate = $tahunini . "-01-01";
            $endDate = $tahunini . "-12-31";
            $periodeLabel = "Tahun Ini (" . $tahunini . ")";
        } elseif ($periode == "custom") {
            $startDate = $request->get("tgl_dari", date("Y-m-01"));
            $endDate = $request->get("tgl_sampai", $hariini);
            $periodeLabel = date("d/m/Y", strtotime($startDate)) . " s/d " . date("d/m/Y", strtotime($endDate));
        } else {
            $periode = "bulan_ini";
            $startDate = date("Y-m-01");
            $endDate = date("Y-m-t");
            $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            $periodeLabel = "Bulan " . $namaBulan[(int)$bulanini] . " " . $tahunini;
        }

        $query = DB::table("layanan")
            ->leftJoin("rekap_penerimaan_pkb", function ($join) use ($startDate, $endDate) {
                $join->on("layanan.id", "=", "rekap_penerimaan_pkb.layanan_id")
                    ->whereBetween("rekap_penerimaan_pkb.tgl_penerimaan", [$startDate, $endDate]);
            });

        if (!empty($layanan_id) && $layanan_id != "all") {
            $query->where("layanan.id", $layanan_id);
        }

        $rekapLayanan = $query->select(
            "layanan.id as layanan_id",
            "layanan.nama_layanan",
            "layanan.nama_pic",
            "layanan.nip_pic",
            "layanan.jabatan_pic",
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r2), 0) as rp_r2"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r2), 0) as unit_r2"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r4), 0) as rp_r4"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r4), 0) as unit_r4"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.total_rp), 0) as total_rp"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.total_unit), 0) as total_unit"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_notice), 0) as total_notice"),
            DB::raw("COUNT(DISTINCT rekap_penerimaan_pkb.tgl_penerimaan) as total_hari_operasi")
        )
        ->groupBy("layanan.id", "layanan.nama_layanan", "layanan.nama_pic", "layanan.nip_pic", "layanan.jabatan_pic")
        ->orderBy("total_rp", "desc")
        ->get();

        $grandTotal = (object) [
            "rp_r2" => $rekapLayanan->sum("rp_r2"),
            "unit_r2" => $rekapLayanan->sum("unit_r2"),
            "rp_r4" => $rekapLayanan->sum("rp_r4"),
            "unit_r4" => $rekapLayanan->sum("unit_r4"),
            "total_rp" => $rekapLayanan->sum("total_rp"),
            "total_unit" => $rekapLayanan->sum("total_unit"),
            "total_notice" => $rekapLayanan->sum("total_notice"),
            "total_hari_operasi" => $rekapLayanan->sum("total_hari_operasi"),
        ];

        $detailHarian = null;
        $layananTerpilih = null;
        if (!empty($layanan_id) && $layanan_id != "all") {
            $layananTerpilih = DB::table("layanan")->where("id", $layanan_id)->first();
            $detailHarian = DB::table("rekap_penerimaan_pkb")
                ->where("layanan_id", $layanan_id)
                ->whereBetween("tgl_penerimaan", [$startDate, $endDate])
                ->orderBy("tgl_penerimaan", "asc")
                ->get();
        }

        $konfigurasi_laporan = DB::table('konfigurasi_laporan')->where('id', 1)->first();

        if ($request->has('exportexcel') || $request->input('export') == 'excel') {
            $time = date("d-M-Y_His");
            return response(view('penerimaan.cetak_rekap_layanan_excel', compact(
                'rekapLayanan', 'grandTotal', 'detailHarian', 'layananTerpilih',
                'periodeLabel', 'startDate', 'endDate', 'konfigurasi_laporan'
            )), 200, [
                'Content-Type' => 'application/vnd-ms-excel',
                'Content-Disposition' => 'attachment; filename="Rekap_Penerimaan_Layanan_' . $time . '.xls"',
            ]);
        }

        $tempDir = storage_path('app/mpdf');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'Legal',
            'orientation' => 'L',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'tempDir' => $tempDir,
        ]);

        $html = view('penerimaan.cetak_rekap_layanan', compact(
            'rekapLayanan', 'grandTotal', 'detailHarian', 'layananTerpilih',
            'periodeLabel', 'startDate', 'endDate', 'konfigurasi_laporan'
        ))->render();

        $mpdf->WriteHTML($html);
        $namaFile = 'Laporan_Rekap_Penerimaan_Layanan_' . date('Ymd_His') . '.pdf';

        return response($mpdf->Output($namaFile, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $namaFile . '"',
        ]);
    }

    /**
     * Cetak Laporan Rekap Penerimaan PKB Per Lokasi (PDF via mPDF / Excel)
     */
    public function cetakRekapLokasi(Request $request)
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m");
        $tahunini = date("Y");

        $periode = $request->get("periode", "bulan_ini");
        $kode_cabang = $request->get("kode_cabang", "all");

        if ($periode == "hari_ini") {
            $startDate = $hariini;
            $endDate = $hariini;
            $periodeLabel = "Hari Ini (" . date("d/m/Y", strtotime($hariini)) . ")";
        } elseif ($periode == "tahun_ini") {
            $startDate = $tahunini . "-01-01";
            $endDate = $tahunini . "-12-31";
            $periodeLabel = "Tahun Ini (" . $tahunini . ")";
        } elseif ($periode == "custom") {
            $startDate = $request->get("tgl_dari", date("Y-m-01"));
            $endDate = $request->get("tgl_sampai", $hariini);
            $periodeLabel = date("d/m/Y", strtotime($startDate)) . " s/d " . date("d/m/Y", strtotime($endDate));
        } else {
            $periode = "bulan_ini";
            $startDate = date("Y-m-01");
            $endDate = date("Y-m-t");
            $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
            $periodeLabel = "Bulan " . $namaBulan[(int)$bulanini] . " " . $tahunini;
        }

        $query = DB::table("rekap_penerimaan_pkb")
            ->join("layanan", "rekap_penerimaan_pkb.layanan_id", "=", "layanan.id")
            ->leftJoin("jadwal_lokasi_layanan", function ($join) {
                $join->on("jadwal_lokasi_layanan.layanan_id", "=", "rekap_penerimaan_pkb.layanan_id")
                    ->where("jadwal_lokasi_layanan.status", 1)
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
            ->leftJoin("cabang as c_jadwal", "jadwal_lokasi_layanan.kode_cabang", "=", "c_jadwal.kode_cabang")
            ->leftJoin("cabang as c_default", "layanan.kode_cabang", "=", "c_default.kode_cabang")
            ->whereBetween("rekap_penerimaan_pkb.tgl_penerimaan", [$startDate, $endDate]);

        if (!empty($kode_cabang) && $kode_cabang != "all") {
            $query->whereRaw("COALESCE(c_jadwal.kode_cabang, c_default.kode_cabang) = ?", [$kode_cabang]);
        }

        $rekapLokasi = $query->select(
            DB::raw("COALESCE(c_jadwal.kode_cabang, c_default.kode_cabang, 'LAIN') as kode_cabang"),
            DB::raw("COALESCE(c_jadwal.nama_cabang, c_default.nama_cabang, 'Lokasi Lainnya') as nama_cabang"),
            DB::raw("COALESCE(c_jadwal.lokasi_cabang, c_default.lokasi_cabang, '-') as lokasi_cabang"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r2), 0) as rp_r2"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r2), 0) as unit_r2"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_rp_r4), 0) as rp_r4"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_unit_r4), 0) as unit_r4"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.total_rp), 0) as total_rp"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.total_unit), 0) as total_unit"),
            DB::raw("COALESCE(SUM(rekap_penerimaan_pkb.jumlah_notice), 0) as total_notice"),
            DB::raw("COUNT(DISTINCT rekap_penerimaan_pkb.tgl_penerimaan) as total_hari_operasi")
        )
        ->groupBy(
            DB::raw("COALESCE(c_jadwal.kode_cabang, c_default.kode_cabang, 'LAIN')"),
            DB::raw("COALESCE(c_jadwal.nama_cabang, c_default.nama_cabang, 'Lokasi Lainnya')"),
            DB::raw("COALESCE(c_jadwal.lokasi_cabang, c_default.lokasi_cabang, '-')")
        )
        ->orderBy("total_rp", "desc")
        ->get();

        $grandTotal = (object) [
            "rp_r2" => $rekapLokasi->sum("rp_r2"),
            "unit_r2" => $rekapLokasi->sum("unit_r2"),
            "rp_r4" => $rekapLokasi->sum("rp_r4"),
            "unit_r4" => $rekapLokasi->sum("unit_r4"),
            "total_rp" => $rekapLokasi->sum("total_rp"),
            "total_unit" => $rekapLokasi->sum("total_unit"),
            "total_notice" => $rekapLokasi->sum("total_notice"),
            "total_hari_operasi" => $rekapLokasi->sum("total_hari_operasi"),
        ];

        $detailHarian = null;
        $lokasiTerpilih = null;
        if (!empty($kode_cabang) && $kode_cabang != "all") {
            $lokasiTerpilih = DB::table("cabang")->where("kode_cabang", $kode_cabang)->first();
            $detailHarian = $query->clone()
                ->select(
                    "rekap_penerimaan_pkb.tgl_penerimaan",
                    "layanan.nama_layanan",
                    "rekap_penerimaan_pkb.jumlah_rp_r2",
                    "rekap_penerimaan_pkb.jumlah_unit_r2",
                    "rekap_penerimaan_pkb.jumlah_rp_r4",
                    "rekap_penerimaan_pkb.jumlah_unit_r4",
                    "rekap_penerimaan_pkb.total_rp",
                    "rekap_penerimaan_pkb.total_unit",
                    "rekap_penerimaan_pkb.jumlah_notice",
                    "rekap_penerimaan_pkb.keterangan"
                )
                ->orderBy("rekap_penerimaan_pkb.tgl_penerimaan", "asc")
                ->get();
        }

        $konfigurasi_laporan = DB::table('konfigurasi_laporan')->where('id', 1)->first();

        if ($request->has('exportexcel') || $request->input('export') == 'excel') {
            $time = date("d-M-Y_His");
            return response(view('penerimaan.cetak_rekap_lokasi_excel', compact(
                'rekapLokasi', 'grandTotal', 'detailHarian', 'lokasiTerpilih',
                'periodeLabel', 'startDate', 'endDate', 'konfigurasi_laporan'
            )), 200, [
                'Content-Type' => 'application/vnd-ms-excel',
                'Content-Disposition' => 'attachment; filename="Rekap_Penerimaan_Lokasi_' . $time . '.xls"',
            ]);
        }

        $tempDir = storage_path('app/mpdf');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'Legal',
            'orientation' => 'L',
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 12,
            'margin_bottom' => 12,
            'tempDir' => $tempDir,
        ]);

        $html = view('penerimaan.cetak_rekap_lokasi', compact(
            'rekapLokasi', 'grandTotal', 'detailHarian', 'lokasiTerpilih',
            'periodeLabel', 'startDate', 'endDate', 'konfigurasi_laporan'
        ))->render();

        $mpdf->WriteHTML($html);
        $namaFile = 'Laporan_Rekap_Penerimaan_Lokasi_' . date('Ymd_His') . '.pdf';

        return response($mpdf->Output($namaFile, 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $namaFile . '"',
        ]);
    }

}
