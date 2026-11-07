<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class MaintenanceController extends Controller
{
    /**
     * Halaman Utama Status & Riwayat Maintenance Layanan (Mobile View)
     */
    public function index(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->id;
        $hariini = date('Y-m-d');

        // Status maintenance aktif hari ini untuk akun layanan ini
        $maintenanceHariIni = DB::table('pengajuan_izin')
            ->where('nik', (string) $layananId)
            ->where('status', 'm')
            ->where('status_approved', 1)
            ->where('tgl_izin_dari', '<=', $hariini)
            ->where('tgl_izin_sampai', '>=', $hariini)
            ->first();

        // Riwayat pengajuan status maintenance layanan
        $query = DB::table('pengajuan_izin')
            ->where('nik', (string) $layananId)
            ->where('status', 'm')
            ->orderBy('tgl_izin_dari', 'desc');

        if (!empty($request->bulan)) {
            $query->whereMonth('tgl_izin_dari', $request->bulan);
        }
        if (!empty($request->tahun)) {
            $query->whereYear('tgl_izin_dari', $request->tahun);
        }

        $riwayat = $query->paginate(15);
        $riwayat->appends($request->all());

        return view('maintenance.index', compact('user', 'maintenanceHariIni', 'riwayat', 'hariini'));
    }

    /**
     * Form Input Buat Status Maintenance / Unit OFF
     */
    public function create()
    {
        $user = Auth::guard('karyawan')->user();
        $hariini = date('Y-m-d');

        return view('maintenance.create', compact('user', 'hariini'));
    }

    /**
     * Simpan Pengajuan Status Maintenance Layanan
     */
    public function store(Request $request)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->id;

        $request->validate([
            'tgl_izin_dari' => 'required|date',
            'tgl_izin_sampai' => 'required|date|after_or_equal:tgl_izin_dari',
            'keterangan' => 'required|string|max:255',
        ], [
            'tgl_izin_dari.required' => 'Tanggal mulai maintenance wajib diisi.',
            'tgl_izin_sampai.required' => 'Tanggal selesai maintenance wajib diisi.',
            'tgl_izin_sampai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'keterangan.required' => 'Keterangan kendala / alasan maintenance wajib diisi.',
            'keterangan.max' => 'Keterangan tidak boleh lebih dari 255 karakter.',
        ]);

        $tglDari = $request->tgl_izin_dari;
        $tglSampai = $request->tgl_izin_sampai;
        $keterangan = $request->keterangan;

        // Cek apakah tanggal yang dipilih bentrok dengan maintenance aktif yang sudah terdaftar
        $bentrok = DB::table('pengajuan_izin')
            ->where('nik', (string) $layananId)
            ->where('status', 'm')
            ->where(function ($q) use ($tglDari, $tglSampai) {
                $q->whereBetween('tgl_izin_dari', [$tglDari, $tglSampai])
                    ->orWhereBetween('tgl_izin_sampai', [$tglDari, $tglSampai])
                    ->orWhere(function ($sub) use ($tglDari, $tglSampai) {
                        $sub->where('tgl_izin_dari', '<=', $tglDari)
                            ->where('tgl_izin_sampai', '>=', $tglSampai);
                    });
            })
            ->first();

        if ($bentrok) {
            return Redirect::back()->withInput()->with([
                'error' => 'Sudah ada jadwal maintenance pada rentang tanggal ' . date('d/m/Y', strtotime($bentrok->tgl_izin_dari)) . ' s/d ' . date('d/m/Y', strtotime($bentrok->tgl_izin_sampai)) . '.'
            ]);
        }

        // Generate Kode Izin Maintenance
        $prefix = 'MT' . date('my');
        $last = DB::table('pengajuan_izin')
            ->where('kode_izin', 'like', $prefix . '%')
            ->orderBy('kode_izin', 'desc')
            ->first();

        $noUrut = 1;
        if ($last) {
            $noUrut = (int) substr($last->kode_izin, -3) + 1;
        }
        $kodeIzin = $prefix . sprintf('%03d', $noUrut);

        DB::beginTransaction();
        try {
            DB::table('pengajuan_izin')->insert([
                'kode_izin' => $kodeIzin,
                'nik' => (string) $layananId,
                'tgl_izin_dari' => $tglDari,
                'tgl_izin_sampai' => $tglSampai,
                'status' => 'm',
                'keterangan' => $keterangan,
                'status_approved' => 1,
            ]);

            // Sinkronkan ke tabel presensi untuk setiap hari dalam rentang tanggal maintenance
            $curr = strtotime($tglDari);
            $end = strtotime($tglSampai);
            while ($curr <= $end) {
                $tgl = date('Y-m-d', $curr);
                $cek = DB::table('presensi')
                    ->where('nik', (string) $layananId)
                    ->where('tgl_presensi', $tgl)
                    ->first();

                if ($cek) {
                    DB::table('presensi')->where('id', $cek->id)->update([
                        'status' => 'm',
                        'kode_izin' => $kodeIzin,
                    ]);
                } else {
                    DB::table('presensi')->insert([
                        'nik' => (string) $layananId,
                        'tgl_presensi' => $tgl,
                        'status' => 'm',
                        'kode_izin' => $kodeIzin,
                        'status_in' => null,
                        'status_out' => null,
                    ]);
                }
                $curr = strtotime('+1 day', $curr);
            }

            DB::commit();
            return redirect('/maintenance')->with(['success' => 'Status Maintenance Layanan berhasil disimpan. Unit dalam status OFF.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->withInput()->with(['error' => 'Gagal menyimpan status maintenance: ' . $e->getMessage()]);
        }
    }

    /**
     * Batalkan / Hapus Data Status Maintenance
     */
    public function delete($kode_izin)
    {
        $user = Auth::guard('karyawan')->user();
        $layananId = $user->id;

        $data = DB::table('pengajuan_izin')
            ->where('kode_izin', $kode_izin)
            ->where('nik', (string) $layananId)
            ->first();

        if (!$data) {
            return Redirect::back()->with(['error' => 'Data maintenance tidak ditemukan atau bukan milik akun ini.']);
        }

        DB::beginTransaction();
        try {
            DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->delete();

            // Kembalikan atau bersihkan status presensi terkait
            $presensiRows = DB::table('presensi')->where('kode_izin', $kode_izin)->get();
            foreach ($presensiRows as $row) {
                if (!empty($row->jam_in)) {
                    DB::table('presensi')->where('id', $row->id)->update([
                        'status' => 'h',
                        'kode_izin' => null,
                    ]);
                } else {
                    DB::table('presensi')->where('id', $row->id)->delete();
                }
            }

            DB::commit();
            return redirect('/maintenance')->with(['success' => 'Status maintenance berhasil dibatalkan / dihapus.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['error' => 'Gagal menghapus status maintenance: ' . $e->getMessage()]);
        }
    }
}

