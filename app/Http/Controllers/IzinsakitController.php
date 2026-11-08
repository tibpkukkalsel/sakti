<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IzinsakitController extends Controller
{
    public function create()
    {
        return view('sakit.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'tgl_izin_dari' => 'required|date',
            'tgl_izin_sampai' => 'required|date',
            'keterangan' => 'required|string|max:500',
            'sid' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ], [
            'sid.mimes' => 'Dokumen SID harus berformat PDF, JPG, JPEG, atau PNG.',
            'sid.max' => 'Ukuran dokumen SID maksimal 2MB.'
        ]);

        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_izin_dari = $request->tgl_izin_dari;
        $tgl_izin_sampai = $request->tgl_izin_sampai;
        $status = "s";
        $keterangan = $request->keterangan;

        $bulan = date("m", strtotime($tgl_izin_dari));
        $tahun = date("Y", strtotime($tgl_izin_dari));
        $thn = substr($tahun, 2, 2);
        $lastizin = DB::table('pengajuan_izin')
            ->whereRaw('MONTH(tgl_izin_dari) = ?', [$bulan])
            ->whereRaw('YEAR(tgl_izin_dari) = ?', [$tahun])
            ->orderBy('kode_izin', 'desc')
            ->first();
        $lastkodeizin = $lastizin != null ? $lastizin->kode_izin : "";
        $format = "IZ" . $bulan . $thn;
        $kode_izin = buatkode($lastkodeizin, $format, 3);

        $sid = null;
        if ($request->hasFile('sid')) {
            $ext = strtolower($request->file('sid')->getClientOriginalExtension());
            if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
                return redirect('/presensi/izin')->with(['error' => 'Format dokumen tidak diizinkan.']);
            }
            $sid = $kode_izin . "." . $ext;
        }

        $data = [
            'kode_izin' => $kode_izin,
            'nik' => $nik,
            'tgl_izin_dari' => $tgl_izin_dari,
            'tgl_izin_sampai' => $tgl_izin_sampai,
            'status' => $status,
            'keterangan' => $keterangan,
            'doc_sid' => $sid
        ];

        //Cek Sudah Absen / Belum
        $cekpresensi = DB::table('presensi')
            ->whereBetween('tgl_presensi', [$tgl_izin_dari, $tgl_izin_sampai])
            ->where('nik', $nik);
        //Cek Sudah Di Ajukan / Belum
        $cekpengajuan = DB::table('pengajuan_izin')
            ->where('nik', $nik)
            ->whereRaw('? BETWEEN tgl_izin_dari AND tgl_izin_sampai', [$tgl_izin_dari]);

        $datapresensi = $cekpresensi->get();

        if ($cekpresensi->count() > 0) {
            $blacklistdate = "";
            foreach ($datapresensi as $d) {
                $blacklistdate .= date('d-m-Y', strtotime($d->tgl_presensi)) . ",";
            }
            return redirect('/presensi/izin')->with(['error' => 'Tidak Bisa Melakukan Pengajuan Pada Tanggal ' . $blacklistdate . 'Karena Ada tanggal yang sudah digunkan / sudah melakukan Presensi, Silahkan Ganti Periode Tanggal Pengajuan']);
        } else if ($cekpengajuan->count() > 0) {
            return redirect('/presensi/izin')->with(['error' => 'Tidak Bisa Melakukan Pengajuan Pada Tanggal Tersebut Karena Ada tanggal yang sudah digunakan Sebelumnya !']);
        } else {
            $simpan = DB::table('pengajuan_izin')->insert($data);

            if ($simpan) {
                if ($request->hasFile('sid') && !empty($sid)) {
                    $folderPath = "public/uploads/sid/";
                    $request->file('sid')->storeAs($folderPath, $sid);
                }
                return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Disimpan']);
            } else {
                return redirect('/presensi/izin')->with(['error' => 'Data Gagal Disimpan']);
            }
        }
    }

    public function edit($kode_izin)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $dataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->where('nik', $nik)->first();
        if (!$dataizin) {
            return redirect('/presensi/izin')->with(['error' => 'Data izin tidak ditemukan atau bukan milik Anda']);
        }
        if (isset($dataizin->status_approved) && $dataizin->status_approved == 1) {
            return redirect('/presensi/izin')->with(['error' => 'Izin yang sudah disetujui tidak dapat diedit']);
        }
        return view('sakit.edit', compact('dataizin'));
    }

    public function update($kode_izin, Request $request)
    {
        $request->validate([
            'tgl_izin_dari' => 'required|date',
            'tgl_izin_sampai' => 'required|date',
            'keterangan' => 'required|string|max:500',
            'sid' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ], [
            'sid.mimes' => 'Dokumen SID harus berformat PDF, JPG, JPEG, atau PNG.',
            'sid.max' => 'Ukuran dokumen SID maksimal 2MB.'
        ]);

        $nik = Auth::guard('karyawan')->user()->nik;
        $cekdataizin = DB::table('pengajuan_izin')->where('kode_izin', $kode_izin)->where('nik', $nik)->first();
        if (!$cekdataizin) {
            return redirect('/presensi/izin')->with(['error' => 'Data izin tidak ditemukan atau bukan milik Anda']);
        }
        if (isset($cekdataizin->status_approved) && $cekdataizin->status_approved == 1) {
            return redirect('/presensi/izin')->with(['error' => 'Izin yang sudah disetujui tidak dapat diedit']);
        }

        $tgl_izin_dari = $request->tgl_izin_dari;
        $tgl_izin_sampai = $request->tgl_izin_sampai;
        $keterangan = $request->keterangan;

        if ($request->hasFile('sid')) {
            $ext = strtolower($request->file('sid')->getClientOriginalExtension());
            if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
                return redirect('/presensi/izin')->with(['error' => 'Format dokumen tidak diizinkan.']);
            }
            $sid = $kode_izin . "." . $ext;
        } else {
            $sid = $cekdataizin->doc_sid ?? null;
        }
        $data = [
            'tgl_izin_dari' => $tgl_izin_dari,
            'tgl_izin_sampai' => $tgl_izin_sampai,
            'keterangan' => $keterangan,
            'doc_sid' => $sid
        ];

        try {
            DB::table('pengajuan_izin')
                ->where('kode_izin', $kode_izin)
                ->where('nik', $nik)
                ->update($data);
            if ($request->hasFile('sid') && !empty($sid)) {
                $folderPath = "public/uploads/sid/";
                $request->file('sid')->storeAs($folderPath, $sid);
            }

            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Diupdate']);
        }
    }
}
