<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\RekapPenerimaanPkb;
use App\Models\Kabupaten;
use App\Models\PenerimaanLintasDaerah;
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

    private function uploadNoticePhoto($file, $prefix, $layananId, $tgl)
    {
        $tglStr = $tgl ? date('Y-m-d', strtotime((string) $tgl)) : date('Y-m-d');
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = $prefix . '-' . $layananId . '-' . $tglStr . '-' . date('His') . '-' . rand(100, 999) . '.' . $extension;
        $folderPath = 'public/uploads/notice/';
        $file->storeAs($folderPath, $filename);

        try {
            $publicDir = public_path('uploads/notice');
            if (!file_exists($publicDir)) {
                @mkdir($publicDir, 0777, true);
            }
            $storagePath = storage_path('app/public/uploads/notice/' . $filename);
            if (file_exists($storagePath)) {
                @copy($storagePath, $publicDir . '/' . $filename);
            } elseif ($file->isValid() && file_exists($file->getRealPath())) {
                @copy($file->getRealPath(), $publicDir . '/' . $filename);
            }
        } catch (\Throwable $e) {
            \Log::warning("Gagal copy foto notice ke public: " . $e->getMessage());
        }

        return $filename;
    }

    /**
     * Memeriksa apakah layanan berstatus libur pada tanggal tertentu.
     * Mengembalikan array ['is_libur' => bool, 'keterangan' => string, 'jadwal' => object|null]
     */
    private function getStatusLiburLayanan($layananId, $tanggal)
    {
        // 1. Cek Hari Libur Nasional / Bersama di tabel harilibur
        $dataLibur = DB::table('harilibur')
            ->leftJoin('harilibur_detail', 'harilibur.kode_libur', '=', 'harilibur_detail.kode_libur')
            ->where('harilibur.tanggal_libur', $tanggal)
            ->where(function ($query) use ($layananId) {
                $query->where('harilibur_detail.nik', (string) $layananId)
                    ->orWhere('harilibur.kode_cabang', 'ALL');
            })
            ->select('harilibur.keterangan')
            ->first();

        if ($dataLibur) {
            $ket = !empty($dataLibur->keterangan) ? $dataLibur->keterangan : 'Hari Libur Nasional / Bersama';
            return [
                'is_libur' => true,
                'keterangan' => $ket,
                'jadwal' => null,
            ];
        }

        // 2. Cek apakah ada jadwal lokasi operasional (khusus tanggal atau rutin mingguan)
        $dayNum = date('w', strtotime($tanggal));
        $hariMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $hariIndo = $hariMap[$dayNum] ?? 'Senin';

        $jadwalToday = DB::table('jadwal_lokasi_layanan')
            ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.id')
            ->where('jadwal_lokasi_layanan.layanan_id', $layananId)
            ->where('jadwal_lokasi_layanan.tanggal', $tanggal)
            ->where('jadwal_lokasi_layanan.status', 1)
            ->select('cabang.nama_cabang', 'jadwal_lokasi_layanan.kode_cabang')
            ->first();

        if (!$jadwalToday) {
            $jadwalToday = DB::table('jadwal_lokasi_layanan')
                ->join('cabang', 'jadwal_lokasi_layanan.kode_cabang', '=', 'cabang.id')
                ->where('jadwal_lokasi_layanan.layanan_id', $layananId)
                ->where('jadwal_lokasi_layanan.hari', $hariIndo)
                ->where('jadwal_lokasi_layanan.jenis_jadwal', 'rutin')
                ->where('jadwal_lokasi_layanan.status', 1)
                ->select('cabang.nama_cabang', 'jadwal_lokasi_layanan.kode_cabang')
                ->first();
        }

        if (!$jadwalToday) {
            return [
                'is_libur' => true,
                'keterangan' => 'Tidak ada jadwal operasional hari ini',
                'jadwal' => null,
            ];
        }

        // 3. Cek apakah presensi pada tanggal tersebut sudah tercatat berstatus libur ('l')
        $presensiLibur = DB::table('presensi')
            ->where('nik', (string) $layananId)
            ->where('tgl_presensi', $tanggal)
            ->where('status', 'l')
            ->first();

        if ($presensiLibur) {
            return [
                'is_libur' => true,
                'keterangan' => 'Layanan berstatus Libur',
                'jadwal' => null,
            ];
        }

        // 4. Cek apakah layanan sedang dalam status Maintenance
        $maintenance = DB::table('pengajuan_izin')
            ->where('nik', (string) $layananId)
            ->where('status', 'm')
            ->where('status_approved', 1)
            ->where('tgl_izin_dari', '<=', $tanggal)
            ->where('tgl_izin_sampai', '>=', $tanggal)
            ->first();

        if ($maintenance) {
            return [
                'is_libur' => true,
                'keterangan' => 'Layanan sedang dalam masa Maintenance',
                'jadwal' => null,
            ];
        }

        return [
            'is_libur' => false,
            'keterangan' => '',
            'jadwal' => $jadwalToday,
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->layanan_id ?? $user->id;
        $hariini = date('Y-m-d');

        // Cek status operasional / libur layanan hari ini
        $statusLibur = $this->getStatusLiburLayanan($layananId, $hariini);
        $isLiburHariIni = $statusLibur['is_libur'];
        $keteranganLibur = $statusLibur['keterangan'];

        // Cek apakah hari ini sudah ada input
        $penerimaanHariIni = RekapPenerimaanPkb::with(['cabang', 'lintasDaerah.kabupaten'])
            ->where('layanan_id', $layananId)
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
        $query = RekapPenerimaanPkb::with(['cabang', 'lintasDaerah'])->where('layanan_id', $layananId);

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

        $penerimaanIds = (clone $query)->pluck('id');
        $totalUnitLintasAkumulasi = DB::table('penerimaan_lintas_daerah')
            ->whereIn('penerimaan_id', $penerimaanIds)
            ->count();

        // Ambil histori penerimaan terpaginasi (6 item per halaman)
        $histori = $query->orderBy('tgl_penerimaan', 'desc')
            ->paginate(6)
            ->withQueryString();

        return view('penerimaan.index', compact('penerimaanHariIni', 'histori', 'totalAkumulasi', 'totalUnitLintasAkumulasi', 'labelAkumulasi', 'user', 'filter', 'dari', 'sampai', 'isLiburHariIni', 'keteranganLibur'));
    }

    public function bukaLayanan()
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->layanan_id ?? $user->id;
        $petugasId = $user->id;
        $tglDefault = date('Y-m-d');

        // Cek apakah hari ini sudah ada data
        $existing = RekapPenerimaanPkb::where('layanan_id', $layananId)
            ->where('tgl_penerimaan', $tglDefault)
            ->first();

        if ($existing) {
            if ($existing->status_verifikasi == 'draft') {
                return redirect('/penerimaan/' . $existing->id . '/tutup-layanan')->with(['warning' => 'Layanan hari ini sudah dibuka sebelumnya. Silakan lengkapi laporan dan nomor akhir notice saat pelayanan selesai.']);
            }
            return redirect('/penerimaan')->with(['warning' => 'Layanan hari ini sudah pernah diinput sebelumnya.']);
        }

        // Validasi status libur layanan
        $statusLibur = $this->getStatusLiburLayanan($layananId, $tglDefault);
        if ($statusLibur['is_libur']) {
            return redirect('/penerimaan')->with(['warning' => 'Tidak dapat membuka layanan: ' . $statusLibur['keterangan'] . '.']);
        }

        // Jika presensi hari ini sudah dilakukan oleh petugas tertentu, pastikan yang membuka adalah petugas yang sama
        $presensiHariIni = DB::table('presensi')
            ->where('nik', (string) $layananId)
            ->where('tgl_presensi', $tglDefault)
            ->first();

        if ($presensiHariIni && !empty($presensiHariIni->jam_in) && !empty($presensiHariIni->petugas_id) && $presensiHariIni->petugas_id != $petugasId) {
            $namaYangAbsen = $presensiHariIni->petugas_nama ?: 'Petugas lain';
            return redirect('/penerimaan')->with(['warning' => "Hanya petugas yang melakukan presensi hari ini ({$namaYangAbsen}) yang berhak membuka layanan."]);
        }

        $jadwalToday = $statusLibur['jadwal'];
        $lokasiId = $jadwalToday ? $jadwalToday->kode_cabang : ($user->kode_cabang ?: 1);
        $namaLokasi = $jadwalToday ? $jadwalToday->nama_cabang : (DB::table('cabang')->where('id', $lokasiId)->value('nama_cabang') ?? 'UPPD SAMSAT Batulicin');

        return view('penerimaan.buka_layanan', compact('user', 'tglDefault', 'lokasiId', 'namaLokasi'));
    }

    public function simpanBukaLayanan(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->layanan_id ?? $user->id;
        $petugasId = $user->id;
        $petugasNama = $user->nama_petugas ?? $user->nama_pic ?? $user->nama_layanan;
        $tgl = $request->tgl_penerimaan ?: date('Y-m-d');

        // Validasi status libur layanan
        $statusLibur = $this->getStatusLiburLayanan($layananId, $tgl);
        if ($statusLibur['is_libur']) {
            return redirect()->back()->withInput()->with(['warning' => 'Tidak dapat membuka layanan: ' . $statusLibur['keterangan'] . '.']);
        }

        // Jika presensi pada tanggal tersebut sudah dilakukan oleh petugas lain
        $presensiTgl = DB::table('presensi')
            ->where('nik', (string) $layananId)
            ->where('tgl_presensi', $tgl)
            ->first();

        if ($presensiTgl && !empty($presensiTgl->jam_in) && !empty($presensiTgl->petugas_id) && $presensiTgl->petugas_id != $petugasId) {
            $namaYangAbsen = $presensiTgl->petugas_nama ?: 'Petugas lain';
            return redirect()->back()->withInput()->with(['warning' => "Hanya petugas yang melakukan presensi ({$namaYangAbsen}) yang berhak membuka layanan."]);
        }

        $request->validate([
            'no_awal_notice' => 'required|string|max:50',
            'foto_awal_notice' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $fotoAwal = null;
        if ($request->hasFile('foto_awal_notice')) {
            $fotoAwal = $this->uploadNoticePhoto($request->file('foto_awal_notice'), 'awal', $layananId, $tgl);
        }

        $jadwalToday = $statusLibur['jadwal'];
        $kodeCabang = $jadwalToday ? $jadwalToday->kode_cabang : ($request->kode_cabang ?: ($user->kode_cabang ?: 1));

        try {
            RekapPenerimaanPkb::updateOrCreate(
                [
                    'layanan_id' => $layananId,
                    'tgl_penerimaan' => $tgl,
                ],
                [
                    'kode_cabang' => $kodeCabang,
                    'no_awal_notice' => $request->no_awal_notice,
                    'foto_awal_notice' => $fotoAwal,
                    'status_verifikasi' => 'draft',
                    'input_by_petugas_id' => $petugasId,
                ]
            );

            return redirect('/penerimaan')->with(['success' => 'Layanan berhasil dibuka! Nomor awal notice tersimpan sebagai draft.']);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with(['warning' => 'Gagal membuka layanan: ' . $e->getMessage()]);
        }
    }

    public function tutupLayanan($id)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->layanan_id ?? $user->id;

        $data = RekapPenerimaanPkb::where('id', $id)
            ->where('layanan_id', $layananId)
            ->firstOrFail();

        if ($data->status_verifikasi == 'disetujui') {
            return redirect('/penerimaan')->with(['success' => 'Data penerimaan ini telah disetujui dan tidak dapat diubah lagi.']);
        }

        if ($data->status_verifikasi == 'menunggu') {
            return redirect('/penerimaan')->with(['warning' => 'Laporan penerimaan sedang menunggu verifikasi admin.']);
        }

        $daftarLokasi = DB::table('cabang')->orderBy('nama_cabang')->get();

        return view('penerimaan.tutup_layanan', compact('data', 'user', 'daftarLokasi'));
    }

    public function kirimLaporan($id, Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->layanan_id ?? $user->id;
        $petugasId = $user->id;
        $petugasNama = $user->nama_petugas ?? $user->nama_pic ?? $user->nama_layanan;

        $data = RekapPenerimaanPkb::where('id', $id)
            ->where('layanan_id', $layananId)
            ->firstOrFail();

        if ($data->status_verifikasi == 'disetujui') {
            return redirect('/penerimaan')->with(['error' => 'Data penerimaan telah disetujui dan tidak dapat diubah.']);
        }

        if ($data->status_verifikasi == 'menunggu') {
            return redirect('/penerimaan')->with(['warning' => 'Laporan penerimaan sedang menunggu verifikasi admin.']);
        }

        $rules = [
            'no_akhir_notice' => 'required|string|max:50',
            'jumlah_rp_r2' => 'nullable',
            'jumlah_rp_r4' => 'nullable',
            'jumlah_unit_r2' => 'nullable|numeric|min:0',
            'jumlah_unit_r4' => 'nullable|numeric|min:0',
            'jumlah_notice' => 'nullable|numeric|min:0',
        ];

        if (empty($data->foto_akhir_notice)) {
            $rules['foto_akhir_notice'] = 'required|image|mimes:jpeg,png,jpg,webp|max:5120';
        } else {
            $rules['foto_akhir_notice'] = 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
        }

        $request->validate($rules);

        $fotoAkhir = $data->foto_akhir_notice;
        if ($request->hasFile('foto_akhir_notice')) {
            $fotoAkhir = $this->uploadNoticePhoto($request->file('foto_akhir_notice'), 'akhir', $layananId, $data->tgl_penerimaan);
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
                'no_akhir_notice' => $request->no_akhir_notice,
                'foto_akhir_notice' => $fotoAkhir,
                'jumlah_rp_r2' => $rpR2,
                'jumlah_rp_r4' => $rpR4,
                'total_rp' => $totalRp,
                'jumlah_unit_r2' => $unitR2,
                'jumlah_unit_r4' => $unitR4,
                'total_unit' => $totalUnit,
                'jumlah_notice' => $notice,
                'keterangan' => $request->keterangan,
                'status_verifikasi' => 'draft',
                'catatan_verifikasi' => null,
                'input_by_petugas_id' => $petugasId,
            ]);

            return redirect('/penerimaan')->with(['success' => 'Data penerimaan berhasil disimpan sebagai draft. Silakan periksa kembali atau kirim laporan jika sudah sesuai.']);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with(['warning' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function kirim($id)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->layanan_id ?? $user->id;
        $petugasId = $user->id;
        $petugasNama = $user->nama_petugas ?? $user->nama_pic ?? $user->nama_layanan;

        $data = RekapPenerimaanPkb::where('id', $id)
            ->where('layanan_id', $layananId)
            ->firstOrFail();

        if ($data->status_verifikasi == 'disetujui') {
            return redirect('/penerimaan')->with(['error' => 'Data penerimaan telah disetujui dan tidak dapat diubah.']);
        }

        if ($data->status_verifikasi == 'menunggu') {
            return redirect('/penerimaan')->with(['warning' => 'Laporan penerimaan sudah dalam antrean verifikasi admin.']);
        }

        if (empty($data->no_akhir_notice) || empty($data->foto_akhir_notice)) {
            return redirect('/penerimaan/' . $data->id . '/tutup-layanan')->with(['warning' => 'Silakan lengkapi nomor akhir notice dan bukti foto sebelum mengirim laporan.']);
        }

        try {
            $data->update([
                'status_verifikasi' => 'menunggu',
                'catatan_verifikasi' => null,
                'input_by_petugas_id' => $petugasId,
            ]);

            return redirect('/penerimaan')->with(['success' => 'Laporan penerimaan berhasil dikirim dan sedang menunggu verifikasi admin.']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Gagal mengirim laporan: ' . $e->getMessage()]);
        }
    }

    public function batalKirim($id)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->layanan_id ?? $user->id;

        $data = RekapPenerimaanPkb::where('id', $id)
            ->where('layanan_id', $layananId)
            ->firstOrFail();

        if ($data->status_verifikasi != 'menunggu') {
            return redirect('/penerimaan')->with(['warning' => 'Hanya laporan dengan status menunggu verifikasi yang dapat dibatalkan.']);
        }

        try {
            $data->update([
                'status_verifikasi' => 'draft',
            ]);

            return redirect('/penerimaan')->with(['success' => 'Pengiriman laporan berhasil dibatalkan. Status kembali ke draft.']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Gagal membatalkan pengiriman: ' . $e->getMessage()]);
        }
    }

    public function lintasDaerah($id)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user ? ($user->layanan_id ?? $user->id) : null;

        $dataQuery = RekapPenerimaanPkb::with(['cabang', 'layanan', 'lintasDaerah.kabupaten'])
            ->where('id', $id);

        if ($layananId) {
            $dataQuery->where('layanan_id', $layananId);
        }

        $data = $dataQuery->firstOrFail();
        $daftarKabupaten = Kabupaten::orderBy('nama_kabupaten', 'asc')->get();
        $isEditable = ($data->status_verifikasi == 'draft');

        $totalUnitLokal = (int) $data->total_unit;
        $totalUnitLintas = (int) $data->lintasDaerah->count();
        $totalUnitSemua = $totalUnitLokal + $totalUnitLintas;
        $totalRpLintas = (float) $data->lintasDaerah->sum('nominal');

        return view('penerimaan.lintas_daerah', compact(
            'data',
            'daftarKabupaten',
            'isEditable',
            'totalUnitLokal',
            'totalUnitLintas',
            'totalUnitSemua',
            'totalRpLintas'
        ));
    }

    public function storeLintasDaerah($id, Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user ? ($user->layanan_id ?? $user->id) : null;

        $data = RekapPenerimaanPkb::where('id', $id)
            ->when($layananId, function ($q) use ($layananId) {
                $q->where('layanan_id', $layananId);
            })
            ->firstOrFail();

        if ($data->status_verifikasi != 'draft') {
            return redirect()->back()->with(['error' => 'Data lintas daerah hanya dapat ditambah saat status masih draft.']);
        }

        $request->validate([
            'nopol' => 'required|string|max:20',
            'nama_wp' => 'required|string|max:100',
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'jenis_kendaraan' => 'required|in:R2,R4',
            'nominal' => 'nullable',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $nominal = 0;
        if ($request->filled('nominal')) {
            $nominal = (float) preg_replace('/[^0-9]/', '', $request->nominal);
        }

        try {
            PenerimaanLintasDaerah::create([
                'penerimaan_id' => $data->id,
                'kabupaten_id' => $request->kabupaten_id,
                'nopol' => strtoupper(trim($request->nopol)),
                'nama_wp' => trim($request->nama_wp),
                'jenis_kendaraan' => $request->jenis_kendaraan,
                'nominal' => $nominal,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->back()->with(['success' => 'Data notice lintas daerah berhasil ditambahkan.']);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with(['warning' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    public function updateLintasDaerah($lintasId, Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user ? ($user->layanan_id ?? $user->id) : null;

        $lintas = PenerimaanLintasDaerah::with('penerimaan')->findOrFail($lintasId);
        $data = $lintas->penerimaan;

        if ($layananId && $data->layanan_id != $layananId) {
            abort(403);
        }

        if ($data->status_verifikasi != 'draft') {
            return redirect()->back()->with(['error' => 'Data lintas daerah hanya dapat diubah saat status masih draft.']);
        }

        $request->validate([
            'nopol' => 'required|string|max:20',
            'nama_wp' => 'required|string|max:100',
            'kabupaten_id' => 'required|exists:kabupaten,id',
            'jenis_kendaraan' => 'required|in:R2,R4',
            'nominal' => 'nullable',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $nominal = 0;
        if ($request->filled('nominal')) {
            $nominal = (float) preg_replace('/[^0-9]/', '', $request->nominal);
        }

        try {
            $lintas->update([
                'kabupaten_id' => $request->kabupaten_id,
                'nopol' => strtoupper(trim($request->nopol)),
                'nama_wp' => trim($request->nama_wp),
                'jenis_kendaraan' => $request->jenis_kendaraan,
                'nominal' => $nominal,
                'keterangan' => $request->keterangan,
            ]);

            return redirect()->back()->with(['success' => 'Data notice lintas daerah berhasil diperbarui.']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Gagal memperbarui data: ' . $e->getMessage()]);
        }
    }

    public function deleteLintasDaerah($lintasId)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user ? ($user->layanan_id ?? $user->id) : null;

        $lintas = PenerimaanLintasDaerah::with('penerimaan')->findOrFail($lintasId);
        $data = $lintas->penerimaan;

        if ($layananId && $data->layanan_id != $layananId) {
            abort(403);
        }

        if ($data->status_verifikasi != 'draft') {
            return redirect()->back()->with(['error' => 'Data lintas daerah hanya dapat dihapus saat status masih draft.']);
        }

        try {
            $lintas->delete();
            return redirect()->back()->with(['success' => 'Data notice lintas daerah berhasil dihapus.']);
        } catch (\Exception $e) {
            return redirect()->back()->with(['warning' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->layanan_id ?? $user->id;
        $petugasId = $user->id;
        $tglDefault = date('Y-m-d');

        // Validasi: Cek apakah Layanan sudah absen masuk hari ini
        $presensiHariIni = DB::table('presensi')
            ->where('nik', (string) $layananId)
            ->where('tgl_presensi', $tglDefault)
            ->first();

        if (!$presensiHariIni || empty($presensiHariIni->jam_in)) {
            return redirect('/penerimaan')->with(['warning' => 'Layanan belum melakukan presensi masuk hari ini. Silakan lakukan presensi masuk terlebih dahulu sebelum mengisi penerimaan.']);
        }

        // Validasi: Cek apakah petugas yang login adalah orang yang melakukan presensi hari ini
        if (!empty($presensiHariIni->petugas_id) && $presensiHariIni->petugas_id != $petugasId) {
            $namaYangAbsen = $presensiHariIni->petugas_nama ?: 'Petugas lain';
            return redirect('/penerimaan')->with(['warning' => "Hanya petugas yang melakukan presensi hari ini ({$namaYangAbsen}) yang berhak menginput data penerimaan."]);
        }

        $daftarLokasi = DB::table('cabang')->orderBy('nama_cabang')->get();

        // Deteksi lokasi hari ini dari jadwal
        $dayNum = date('w', strtotime($tglDefault));
        $hariMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $hariIndo = $hariMap[$dayNum] ?? 'Senin';
        $jadwalToday = DB::table('jadwal_lokasi_layanan')
            ->where('layanan_id', $layananId)
            ->where(function ($q) use ($tglDefault, $hariIndo) {
                $q->where('tanggal', $tglDefault)->orWhere(function ($q2) use ($hariIndo) {
                    $q2->where('jenis_jadwal', 'rutin')->where('hari', $hariIndo);
                });
            })
            ->where('status', 1)
            ->orderBy('jenis_jadwal', 'desc')
            ->first();
        $lokasiDefault = $jadwalToday ? $jadwalToday->kode_cabang : ($user->kode_cabang ?: 1);

        return view('penerimaan.create', compact('user', 'tglDefault', 'daftarLokasi', 'lokasiDefault'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->layanan_id ?? $user->id;
        $petugasId = $user->id;
        $petugasNama = $user->nama_petugas ?? $user->nama_pic ?? $user->nama_layanan;
        $tgl = $request->tgl_penerimaan;

        // Validasi: Cek presensi masuk pada tanggal tersebut
        $presensiTgl = DB::table('presensi')
            ->where('nik', (string) $layananId)
            ->where('tgl_presensi', $tgl)
            ->first();

        if (!$presensiTgl || empty($presensiTgl->jam_in)) {
            return redirect()->back()->withInput()->with(['warning' => 'Layanan belum melakukan presensi masuk pada tanggal ' . date('d/m/Y', strtotime($tgl)) . '. Presensi masuk wajib dilakukan sebelum menginput penerimaan.']);
        }

        // Validasi: Cek apakah yang menginput adalah petugas yang melakukan presensi
        if (!empty($presensiTgl->petugas_id) && $presensiTgl->petugas_id != $petugasId) {
            $namaYangAbsen = $presensiTgl->petugas_nama ?: 'Petugas lain';
            return redirect()->back()->withInput()->with(['warning' => "Hanya petugas yang melakukan presensi pada tanggal tersebut ({$namaYangAbsen}) yang berhak menginput data penerimaan."]);
        }

        $request->validate([
            'tgl_penerimaan' => 'required|date',
            'kode_cabang' => 'nullable',
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

        $totalRp = $rpR2 + $rpR4;
        $totalUnit = $unitR2 + $unitR4;

        // Resolusi kode cabang / id lokasi
        $kodeCabang = $request->kode_cabang;
        if (empty($kodeCabang)) {
            $jadwalKhusus = DB::table('jadwal_lokasi_layanan')
                ->where('layanan_id', $layananId)
                ->where('tanggal', $tgl)
                ->where('status', 1)
                ->first();
            if ($jadwalKhusus && !empty($jadwalKhusus->kode_cabang)) {
                $kodeCabang = $jadwalKhusus->kode_cabang;
            } else {
                $dayNum = date('w', strtotime($tgl));
                $hariMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
                $hariIndo = $hariMap[$dayNum] ?? 'Senin';
                $jadwalRutin = DB::table('jadwal_lokasi_layanan')
                    ->where('layanan_id', $layananId)
                    ->where('jenis_jadwal', 'rutin')
                    ->where('hari', $hariIndo)
                    ->where('status', 1)
                    ->first();
                $kodeCabang = $jadwalRutin ? $jadwalRutin->kode_cabang : ($user->kode_cabang ?: 1);
            }
        }

        // Cek jika data pada tanggal tersebut sudah ada dan dikunci oleh admin
        $existing = RekapPenerimaanPkb::where('layanan_id', $layananId)
            ->where('tgl_penerimaan', $tgl)
            ->first();

        if ($existing && $existing->status_verifikasi == 'disetujui') {
            return redirect()->back()->withInput()->with(['error' => 'Data penerimaan untuk tanggal tersebut telah disetujui oleh admin dan tidak dapat diubah lagi.']);
        }

        $fotoAwal = $existing ? $existing->foto_awal_notice : null;
        if ($request->hasFile('foto_awal_notice')) {
            $fotoAwal = $this->uploadNoticePhoto($request->file('foto_awal_notice'), 'awal', $layananId, $tgl);
        }
        $fotoAkhir = $existing ? $existing->foto_akhir_notice : null;
        if ($request->hasFile('foto_akhir_notice')) {
            $fotoAkhir = $this->uploadNoticePhoto($request->file('foto_akhir_notice'), 'akhir', $layananId, $tgl);
        }

        try {
            RekapPenerimaanPkb::updateOrCreate(
                [
                    'layanan_id' => $layananId,
                    'tgl_penerimaan' => $tgl,
                ],
                [
                    'kode_cabang' => $kodeCabang,
                    'no_awal_notice' => $request->no_awal_notice ?: ($existing ? $existing->no_awal_notice : null),
                    'foto_awal_notice' => $fotoAwal,
                    'no_akhir_notice' => $request->no_akhir_notice ?: ($existing ? $existing->no_akhir_notice : null),
                    'foto_akhir_notice' => $fotoAkhir,
                    'jumlah_rp_r2' => $rpR2,
                    'jumlah_rp_r4' => $rpR4,
                    'total_rp' => $totalRp,
                    'jumlah_unit_r2' => $unitR2,
                    'jumlah_unit_r4' => $unitR4,
                    'total_unit' => $totalUnit,
                    'jumlah_notice' => $notice,
                    'status_verifikasi' => 'draft',
                    'catatan_verifikasi' => null,
                    'keterangan' => $request->keterangan,
                    'input_by_petugas_id' => $petugasId,
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
        $layananId = $user->layanan_id ?? $user->id;
        $petugasId = $user->id;

        $data = RekapPenerimaanPkb::where('id', $id)
            ->where('layanan_id', $layananId)
            ->firstOrFail();

        if ($data->status_verifikasi == 'disetujui') {
            return redirect('/penerimaan')->with(['warning' => 'Data penerimaan ini telah disetujui dan tidak dapat diubah lagi.']);
        }

        if ($data->status_verifikasi == 'menunggu') {
            return redirect('/penerimaan')->with(['warning' => 'Laporan penerimaan sedang menunggu verifikasi admin.']);
        }

        // Validasi: Cek apakah petugas yang membuka edit adalah yang melakukan presensi pada tanggal data
        $presensiTgl = DB::table('presensi')
            ->where('nik', (string) $layananId)
            ->where('tgl_presensi', $data->tgl_penerimaan)
            ->first();

        if ($presensiTgl && !empty($presensiTgl->petugas_id) && $presensiTgl->petugas_id != $petugasId) {
            $namaYangAbsen = $presensiTgl->petugas_nama ?: 'Petugas lain';
            return redirect('/penerimaan')->with(['warning' => "Hanya petugas yang melakukan presensi pada tanggal tersebut ({$namaYangAbsen}) yang berhak mengubah data penerimaan."]);
        }

        $daftarLokasi = DB::table('cabang')->orderBy('nama_cabang')->get();

        return view('penerimaan.edit', compact('data', 'user', 'daftarLokasi'));
    }

    public function update($id, Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->layanan_id ?? $user->id;
        $petugasId = $user->id;
        $petugasNama = $user->nama_petugas ?? $user->nama_pic ?? $user->nama_layanan;

        $data = RekapPenerimaanPkb::where('id', $id)
            ->where('layanan_id', $layananId)
            ->firstOrFail();

        if ($data->status_verifikasi == 'disetujui') {
            return redirect('/penerimaan')->with(['error' => 'Data penerimaan ini telah disetujui dan tidak dapat diubah lagi.']);
        }

        if ($data->status_verifikasi == 'menunggu') {
            return redirect('/penerimaan')->with(['warning' => 'Laporan penerimaan sedang menunggu verifikasi admin.']);
        }

        // Validasi: Cek presensi tanggal data
        $presensiTgl = DB::table('presensi')
            ->where('nik', (string) $layananId)
            ->where('tgl_presensi', $data->tgl_penerimaan)
            ->first();

        if ($presensiTgl && !empty($presensiTgl->petugas_id) && $presensiTgl->petugas_id != $petugasId) {
            $namaYangAbsen = $presensiTgl->petugas_nama ?: 'Petugas lain';
            return redirect('/penerimaan')->with(['warning' => "Hanya petugas yang melakukan presensi pada tanggal tersebut ({$namaYangAbsen}) yang berhak mengubah data penerimaan."]);
        }

        $rpR2 = (float) preg_replace('/[^0-9]/', '', $request->jumlah_rp_r2);
        $rpR4 = (float) preg_replace('/[^0-9]/', '', $request->jumlah_rp_r4);
        $unitR2 = (int) $request->jumlah_unit_r2;
        $unitR4 = (int) $request->jumlah_unit_r4;
        $notice = (int) $request->jumlah_notice;

        $totalRp = $rpR2 + $rpR4;
        $totalUnit = $unitR2 + $unitR4;

        try {
            $updateData = [
                'tgl_penerimaan' => $request->tgl_penerimaan ?: $data->tgl_penerimaan,
                'jumlah_rp_r2' => $rpR2,
                'jumlah_rp_r4' => $rpR4,
                'total_rp' => $totalRp,
                'jumlah_unit_r2' => $unitR2,
                'jumlah_unit_r4' => $unitR4,
                'total_unit' => $totalUnit,
                'jumlah_notice' => $notice,
                'keterangan' => $request->keterangan,
                'status_verifikasi' => 'draft',
                'catatan_verifikasi' => null,
                'input_by_petugas_id' => $petugasId,
            ];

            if ($request->filled('no_awal_notice')) {
                $updateData['no_awal_notice'] = $request->no_awal_notice;
            }
            if ($request->hasFile('foto_awal_notice')) {
                $updateData['foto_awal_notice'] = $this->uploadNoticePhoto($request->file('foto_awal_notice'), 'awal', $layananId, $data->tgl_penerimaan);
            }
            if ($request->filled('no_akhir_notice')) {
                $updateData['no_akhir_notice'] = $request->no_akhir_notice;
            }
            if ($request->hasFile('foto_akhir_notice')) {
                $updateData['foto_akhir_notice'] = $this->uploadNoticePhoto($request->file('foto_akhir_notice'), 'akhir', $layananId, $data->tgl_penerimaan);
            }

            if (!empty($request->kode_cabang)) {
                $updateData['kode_cabang'] = $request->kode_cabang;
            }
            $data->update($updateData);

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

        if ($data->status_verifikasi == 'disetujui') {
            return redirect('/penerimaan')->with(['error' => 'Data penerimaan ini telah disetujui dan tidak dapat dihapus lagi.']);
        }

        if ($data->status_verifikasi == 'menunggu') {
            return redirect('/penerimaan')->with(['warning' => 'Laporan penerimaan sedang menunggu verifikasi admin.']);
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

        $query = RekapPenerimaanPkb::with(['layanan', 'cabang', 'petugas', 'lintasDaerah'])
            ->whereBetween('tgl_penerimaan', [$tgl_dari, $tgl_sampai])
            ->orderBy('tgl_penerimaan', 'desc');

        if (!empty($layanan_id)) {
            $query->where('layanan_id', $layanan_id);
        }

        $penerimaan = $query->paginate(10)->withQueryString();

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
        $daftarLokasi = DB::table('cabang')->orderBy('nama_cabang')->get();

        return view('penerimaan.admin_index', compact('penerimaan', 'summary', 'daftarLayanan', 'daftarLokasi', 'tgl_dari', 'tgl_sampai', 'layanan_id'));
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required',
            'kode_cabang' => 'nullable',
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

        // Resolusi kode cabang / id lokasi
        $kodeCabang = $request->kode_cabang;
        if (empty($kodeCabang)) {
            $jadwalKhusus = DB::table('jadwal_lokasi_layanan')
                ->where('layanan_id', $request->layanan_id)
                ->where('tanggal', $request->tgl_penerimaan)
                ->where('status', 1)
                ->first();
            if ($jadwalKhusus && !empty($jadwalKhusus->kode_cabang)) {
                $kodeCabang = $jadwalKhusus->kode_cabang;
            } else {
                $dayNum = date('w', strtotime($request->tgl_penerimaan));
                $hariMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
                $hariIndo = $hariMap[$dayNum] ?? 'Senin';
                $jadwalRutin = DB::table('jadwal_lokasi_layanan')
                    ->where('layanan_id', $request->layanan_id)
                    ->where('jenis_jadwal', 'rutin')
                    ->where('hari', $hariIndo)
                    ->where('status', 1)
                    ->first();
                $layananItem = Layanan::find($request->layanan_id);
                $kodeCabang = $jadwalRutin ? $jadwalRutin->kode_cabang : ($layananItem ? $layananItem->kode_cabang : 1);
            }
        }

        try {
            RekapPenerimaanPkb::updateOrCreate(
                [
                    'layanan_id' => $request->layanan_id,
                    'tgl_penerimaan' => $request->tgl_penerimaan,
                ],
                [
                    'kode_cabang' => $kodeCabang,
                    'jumlah_rp_r2' => $rpR2,
                    'jumlah_rp_r4' => $rpR4,
                    'total_rp' => $totalRp,
                    'jumlah_unit_r2' => $unitR2,
                    'jumlah_unit_r4' => $unitR4,
                    'total_unit' => $totalUnit,
                    'jumlah_notice' => $notice,
                    'status_verifikasi' => 'disetujui',
                    'keterangan' => $request->keterangan,
                    'verified_at' => Carbon::now(),
                    'verified_by' => Auth::guard('user')->user()->id ?? null,
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
        $data = RekapPenerimaanPkb::with(['layanan', 'cabang'])->where('id', $id)->firstOrFail();
        $daftarLayanan = Layanan::orderBy('nama_layanan')->get();
        $daftarLokasi = DB::table('cabang')->orderBy('nama_cabang')->get();

        return view('penerimaan.admin_edit', compact('data', 'daftarLayanan', 'daftarLokasi'));
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

        $newStatus = $request->input('status_verifikasi', $data->status_verifikasi);

        try {
            $updateData = [
                'layanan_id' => $request->layanan_id ?: $data->layanan_id,
                'tgl_penerimaan' => $request->tgl_penerimaan ?: $data->tgl_penerimaan,
                'jumlah_rp_r2' => $rpR2,
                'jumlah_rp_r4' => $rpR4,
                'total_rp' => $totalRp,
                'jumlah_unit_r2' => $unitR2,
                'jumlah_unit_r4' => $unitR4,
                'total_unit' => $totalUnit,
                'jumlah_notice' => $notice,
                'status_verifikasi' => $newStatus,
                'catatan_verifikasi' => $request->has('catatan_verifikasi') ? $request->catatan_verifikasi : $data->catatan_verifikasi,
                'keterangan' => $request->keterangan,
            ];
            if ($request->filled('no_awal_notice')) {
                $updateData['no_awal_notice'] = $request->no_awal_notice;
            }
            if ($request->filled('no_akhir_notice')) {
                $updateData['no_akhir_notice'] = $request->no_akhir_notice;
            }
            if (!empty($request->kode_cabang)) {
                $updateData['kode_cabang'] = $request->kode_cabang;
            }
            $data->update($updateData);

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

    public function adminDetailVerifikasi(Request $request, $id)
    {
        $data = RekapPenerimaanPkb::with(['layanan', 'cabang', 'lintasDaerah.kabupaten'])->findOrFail($id);
        return view('penerimaan.admin_modal_verifikasi', compact('data'));
    }

    public function adminSetujui($id, Request $request)
    {
        $data = RekapPenerimaanPkb::findOrFail($id);
        $data->update([
            'status_verifikasi' => 'disetujui',
            'verified_at' => now(),
            'verified_by' => Auth::user()->id,
            'catatan_verifikasi' => null,
        ]);

        return Redirect::back()->with(['success' => 'Laporan penerimaan tanggal ' . date('d-m-Y', strtotime($data->tgl_penerimaan)) . ' (' . ($data->layanan->nama_layanan ?? '') . ') berhasil disetujui dan otomatis dikunci.']);
    }

    public function adminTolak($id, Request $request)
    {
        $data = RekapPenerimaanPkb::findOrFail($id);
        $wasApproved = ($data->status_verifikasi == 'disetujui');

        $catatan = $request->input('catatan_verifikasi');
        if (empty($catatan)) {
            $catatan = $wasApproved ? 'Persetujuan dibatalkan oleh admin untuk dilakukan revisi data.' : 'Laporan memerlukan revisi data.';
        }

        $data->update([
            'status_verifikasi' => 'ditolak',
            'catatan_verifikasi' => $catatan,
            'verified_at' => now(),
            'verified_by' => Auth::user()->id,
        ]);

        $msg = $wasApproved
            ? 'Persetujuan laporan berhasil dibatalkan dan status dikembalikan ke petugas layanan untuk direvisi.'
            : 'Laporan penerimaan tanggal ' . date('d-m-Y', strtotime($data->tgl_penerimaan)) . ' dikembalikan ke petugas layanan untuk direvisi.';

        return Redirect::back()->with(['success' => $msg]);
    }

    public function adminCetak(Request $request)
    {
        $tgl_dari = $request->tgl_dari ?: date('Y-m-01');
        $tgl_sampai = $request->tgl_sampai ?: date('Y-m-d');
        $layanan_id = $request->layanan_id;

        $query = RekapPenerimaanPkb::with(['layanan', 'cabang'])
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
    /**
     * Tampilan Menu Laporan Rekap Penerimaan PKB Per Layanan
     * Form sederhana untuk memilih periode (bulan & tahun) dan layanan
     */
    public function rekapLayanan()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $daftarLayanan = DB::table("layanan")->orderBy("nama_layanan", "asc")->get();

        return view("penerimaan.rekap_layanan", compact("namabulan", "daftarLayanan"));
    }

    /**
     * Tampilan Menu Laporan Rekap Penerimaan PKB Per Lokasi
     * Form sederhana untuk memilih periode (bulan & tahun) dan lokasi pelayanan
     */
    public function rekapLokasi()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $daftarLokasi = DB::table("cabang")->orderBy("nama_cabang", "asc")->get();

        return view("penerimaan.rekap_lokasi", compact("namabulan", "daftarLokasi"));
    }

    /**
     * Cetak Laporan Rekap Penerimaan PKB Per Layanan (PDF via mPDF / Excel)
     */
    public function cetakRekapLayanan(Request $request)
    {
        $hariini = date("Y-m-d");
        $bulanini = date("m");
        $tahunini = date("Y");

        $bulan = $request->bulan ?: $request->get("bulan");
        $tahun = $request->tahun ?: $request->get("tahun");
        $periode = $request->get("periode", "bulan_ini");
        $layanan_id = $request->get("layanan_id", "all");

        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        if (!empty($bulan) && !empty($tahun)) {
            $startDate = $tahun . "-" . str_pad($bulan, 2, "0", STR_PAD_LEFT) . "-01";
            $endDate = date("Y-m-t", strtotime($startDate));
            $periodeLabel = "Bulan " . $namaBulan[(int)$bulan] . " " . $tahun;
        } elseif ($periode == "hari_ini") {
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
            $startDate = date("Y-m-01");
            $endDate = date("Y-m-t");
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
                ->leftJoin("petugas_layanan", "rekap_penerimaan_pkb.input_by_petugas_id", "=", "petugas_layanan.id")
                ->where("rekap_penerimaan_pkb.layanan_id", $layanan_id)
                ->whereBetween("rekap_penerimaan_pkb.tgl_penerimaan", [$startDate, $endDate])
                ->select("rekap_penerimaan_pkb.*", "petugas_layanan.nama_petugas as input_by_nama")
                ->orderBy("rekap_penerimaan_pkb.tgl_penerimaan", "asc")
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

        $bulan = $request->bulan ?: $request->get("bulan");
        $tahun = $request->tahun ?: $request->get("tahun");
        $periode = $request->get("periode", "bulan_ini");
        $kode_cabang = $request->get("kode_cabang", "all");

        $namaBulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        if (!empty($bulan) && !empty($tahun)) {
            $startDate = $tahun . "-" . str_pad($bulan, 2, "0", STR_PAD_LEFT) . "-01";
            $endDate = date("Y-m-t", strtotime($startDate));
            $periodeLabel = "Bulan " . $namaBulan[(int)$bulan] . " " . $tahun;
        } elseif ($periode == "hari_ini") {
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
            $startDate = date("Y-m-01");
            $endDate = date("Y-m-t");
            $periodeLabel = "Bulan " . $namaBulan[(int)$bulanini] . " " . $tahunini;
        }

        $query = DB::table("rekap_penerimaan_pkb")
            ->join("layanan", "rekap_penerimaan_pkb.layanan_id", "=", "layanan.id")
            ->leftJoin("jadwal_lokasi_layanan as j_khusus", function ($join) {
                $join->on("j_khusus.layanan_id", "=", "rekap_penerimaan_pkb.layanan_id")
                    ->on("j_khusus.tanggal", "=", "rekap_penerimaan_pkb.tgl_penerimaan")
                    ->where("j_khusus.status", 1);
            })
            ->leftJoin("jadwal_lokasi_layanan as j_rutin", function ($join) {
                $join->on("j_rutin.layanan_id", "=", "rekap_penerimaan_pkb.layanan_id")
                    ->where("j_rutin.jenis_jadwal", "rutin")
                    ->where("j_rutin.status", 1)
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
            ->leftJoin("cabang as c_lokasi", DB::raw("COALESCE(rekap_penerimaan_pkb.kode_cabang, j_khusus.kode_cabang, j_rutin.kode_cabang, layanan.kode_cabang)"), "=", "c_lokasi.id")
            ->whereBetween("rekap_penerimaan_pkb.tgl_penerimaan", [$startDate, $endDate]);

        if (!empty($kode_cabang) && $kode_cabang != "all") {
            $query->where("c_lokasi.id", $kode_cabang);
        }

        $rekapLokasi = $query->select(
            DB::raw("COALESCE(c_lokasi.id, 'LAIN') as kode_cabang"),
            DB::raw("COALESCE(c_lokasi.nama_cabang, 'Lokasi Lainnya') as nama_cabang"),
            DB::raw("COALESCE(c_lokasi.lokasi_cabang, '-') as lokasi_cabang"),
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
            DB::raw("COALESCE(c_lokasi.id, 'LAIN')"),
            DB::raw("COALESCE(c_lokasi.nama_cabang, 'Lokasi Lainnya')"),
            DB::raw("COALESCE(c_lokasi.lokasi_cabang, '-')")
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
            $lokasiTerpilih = DB::table("cabang")->where("id", $kode_cabang)->first();
            $detailHarian = $query->clone()
                ->leftJoin("petugas_layanan", "rekap_penerimaan_pkb.input_by_petugas_id", "=", "petugas_layanan.id")
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
                    "petugas_layanan.nama_petugas as input_by_nama",
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
