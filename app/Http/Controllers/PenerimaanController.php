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

        // Ambil histori penerimaan akun layanan ini
        $query = RekapPenerimaanPkb::where('layanan_id', $layananId)
            ->orderBy('tgl_penerimaan', 'desc');

        if (!empty($request->bulan)) {
            $query->whereMonth('tgl_penerimaan', $request->bulan);
        }
        if (!empty($request->tahun)) {
            $query->whereYear('tgl_penerimaan', $request->tahun);
        }

        $histori = $query->paginate(15);

        // Hitung total penerimaan bulan ini untuk akun layanan ini
        $bulanSekarang = date('m');
        $tahunSekarang = date('Y');
        $totalBulanIni = RekapPenerimaanPkb::where('layanan_id', $layananId)
            ->whereMonth('tgl_penerimaan', $bulanSekarang)
            ->whereYear('tgl_penerimaan', $tahunSekarang)
            ->selectRaw('SUM(total_rp) as total_rp, SUM(total_unit) as total_unit, SUM(jumlah_notice) as total_notice')
            ->first();

        return view('penerimaan.index', compact('penerimaanHariIni', 'histori', 'totalBulanIni', 'user'));
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
            'jumlah_rp_r2' => 'required',
            'jumlah_rp_r4' => 'required',
            'jumlah_unit_r2' => 'required|numeric|min:0',
            'jumlah_unit_r4' => 'required|numeric|min:0',
            'jumlah_notice' => 'required|numeric|min:0',
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

        return view('penerimaan.edit', compact('data', 'user'));
    }

    public function update($id, Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $data = RekapPenerimaanPkb::where('id', $id)
            ->where('layanan_id', $user->id)
            ->firstOrFail();

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

        $penerimaan = $query->paginate(20);

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
            'jumlah_rp_r2' => 'required',
            'jumlah_rp_r4' => 'required',
            'jumlah_unit_r2' => 'required|numeric|min:0',
            'jumlah_unit_r4' => 'required|numeric|min:0',
            'jumlah_notice' => 'required|numeric|min:0',
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
}

