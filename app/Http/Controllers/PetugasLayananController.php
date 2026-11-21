<?php

namespace App\Http\Controllers;

use App\Models\JadwalPetugasLayanan;
use App\Models\Layanan;
use App\Models\PetugasLayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PetugasLayananController extends Controller
{
    protected $listHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    public function index(Request $request)
    {
        $query = PetugasLayanan::with('layanan')->orderBy('nama_petugas');

        if ($request->filled('nama_petugas')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_petugas', 'like', '%' . $request->nama_petugas . '%')
                    ->orWhere('username', 'like', '%' . $request->nama_petugas . '%')
                    ->orWhere('no_hp', 'like', '%' . $request->nama_petugas . '%');
            });
        }

        if ($request->filled('layanan_id')) {
            $query->where('layanan_id', $request->layanan_id);
        }

        $petugas = $query->paginate(10)->withQueryString();
        $listLayanan = Layanan::orderBy('nama_layanan')->get();

        return view('petugas_layanan.index', compact('petugas', 'listLayanan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'layanan_id' => 'required',
            'nama_petugas' => 'required',
            'username' => 'required|unique:petugas_layanan,username',
            'foto' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ], [
            'username.unique' => 'Username sudah digunakan oleh akun petugas lain.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format file foto harus PNG, JPG, atau JPEG.',
            'foto.max' => 'Ukuran file foto maksimal 2MB.'
        ]);

        $foto = null;
        if ($request->hasFile('foto')) {
            $ext = strtolower($request->file('foto')->getClientOriginalExtension());
            $safeUsername = preg_replace('/[^A-Za-z0-9_\-]/', '', trim($request->username));
            $foto = time() . '_' . $safeUsername . '.' . $ext;
            $request->file('foto')->storeAs('public/uploads/karyawan/', $foto);
        }

        try {
            PetugasLayanan::create([
                'layanan_id' => $request->layanan_id,
                'nama_petugas' => $request->nama_petugas,
                'nip' => $request->nip,
                'jabatan' => $request->jabatan ?: 'Petugas Layanan',
                'no_hp' => $request->no_hp,
                'username' => trim($request->username),
                'password' => Hash::make($request->password ?: '12345'),
                'foto' => $foto,
                'status_aktif' => 1,
            ]);

            return Redirect::back()->with(['success' => 'Data Petugas Berhasil Disimpan']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan: ' . $e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $petugas = PetugasLayanan::findOrFail($id);
        $listLayanan = Layanan::orderBy('nama_layanan')->get();

        return view('petugas_layanan.edit', compact('petugas', 'listLayanan'));
    }

    public function update($id, Request $request)
    {
        $id = Crypt::decrypt($id);
        $petugas = PetugasLayanan::findOrFail($id);

        $request->validate([
            'layanan_id' => 'required',
            'nama_petugas' => 'required',
            'username' => 'required|unique:petugas_layanan,username,' . $id,
            'foto' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $foto = $petugas->foto;
        if ($request->hasFile('foto')) {
            $ext = strtolower($request->file('foto')->getClientOriginalExtension());
            $safeUsername = preg_replace('/[^A-Za-z0-9_\-]/', '', trim($request->username));
            $foto = time() . '_' . $safeUsername . '.' . $ext;
            $request->file('foto')->storeAs('public/uploads/karyawan/', $foto);
        }

        $data = [
            'layanan_id' => $request->layanan_id,
            'nama_petugas' => $request->nama_petugas,
            'nip' => $request->nip,
            'jabatan' => $request->jabatan ?: 'Petugas Layanan',
            'no_hp' => $request->no_hp,
            'username' => trim($request->username),
            'foto' => $foto,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $petugas->update($data);

        return Redirect::back()->with(['success' => 'Data Petugas Berhasil Diperbarui']);
    }

    public function delete($id)
    {
        $id = Crypt::decrypt($id);
        $petugas = PetugasLayanan::findOrFail($id);
        $petugas->delete();

        return Redirect::back()->with(['success' => 'Data Petugas Berhasil Dihapus']);
    }

    public function toggleStatus($id)
    {
        $petugas = PetugasLayanan::findOrFail($id);
        $petugas->status_aktif = $petugas->status_aktif ? 0 : 1;
        $petugas->save();

        return Redirect::back()->with(['success' => 'Status Akun Petugas Berhasil Diubah']);
    }

    /**
     * Tampilan Kelola Jadwal Mingguan Petugas Layanan
     */
    public function jadwal(Request $request)
    {
        $layananId = $request->layanan_id;
        $listLayanan = Layanan::with('petugas')->orderBy('nama_layanan')->get();

        if (empty($layananId) && $listLayanan->count() > 0) {
            $layananId = $listLayanan->first()->id;
        }

        $layananSelected = Layanan::with(['petugas', 'lokasi'])->find($layananId);
        $listPetugas = PetugasLayanan::where('layanan_id', $layananId)->where('status_aktif', 1)->get();
        $listHari = $this->listHari;

        // Ambil jadwal petugas yang sudah tersimpan
        $jadwalRaw = JadwalPetugasLayanan::where('layanan_id', $layananId)->get();
        $matrixJadwal = [];
        foreach ($jadwalRaw as $j) {
            $matrixJadwal[$j->petugas_id][$j->hari] = true;
        }

        return view('petugas_layanan.jadwal', compact('listLayanan', 'layananSelected', 'listPetugas', 'listHari', 'matrixJadwal', 'layananId'));
    }

    /**
     * Simpan Pengaturan Jadwal Mingguan Petugas Layanan
     */
    public function storeJadwal(Request $request)
    {
        $layananId = $request->layanan_id;
        $modePresensi = $request->mode_presensi ?: 'bebas';

        // Update mode presensi pada layanan
        Layanan::where('id', $layananId)->update([
            'mode_presensi' => $modePresensi
        ]);

        // Simpan jadwal petugas (petugas_id => [hari1, hari2, ...])
        $jadwalInput = $request->input('jadwal', []);

        DB::beginTransaction();
        try {
            // Hapus jadwal rutin sebelumnya untuk layanan ini
            JadwalPetugasLayanan::where('layanan_id', $layananId)->whereNull('tanggal')->delete();

            foreach ($jadwalInput as $petugasId => $hariList) {
                if (is_array($hariList)) {
                    foreach ($hariList as $hari) {
                        JadwalPetugasLayanan::create([
                            'layanan_id' => $layananId,
                            'petugas_id' => $petugasId,
                            'hari' => $hari,
                            'status' => 1,
                        ]);
                    }
                }
            }

            DB::commit();
            return Redirect::to('/panel-adm14/petugas-layanan/jadwal?layanan_id=' . $layananId)->with(['success' => 'Pengaturan Mode & Jadwal Petugas Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::to('/panel-adm14/petugas-layanan/jadwal?layanan_id=' . $layananId)->with(['warning' => 'Gagal Menyimpan Jadwal: ' . $e->getMessage()]);
        }
    }
}
