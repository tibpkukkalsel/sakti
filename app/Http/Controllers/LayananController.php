<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        $kode_cabang = Auth::guard('user')->user()->kode_cabang;
        $user = User::find(Auth::guard('user')->user()->id);

        $query = Layanan::withCount('petugas');
        $query->orderBy('nama_layanan');

        if (!empty($request->nama_layanan)) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_layanan', 'like', '%' . $request->nama_layanan . '%')
                    ->orWhere('username', 'like', '%' . $request->nama_layanan . '%')
                    ->orWhere('nama_pic', 'like', '%' . $request->nama_layanan . '%');
            });
        }

        if (!empty($request->kode_cabang)) {
            $query->where('kode_cabang', $request->kode_cabang);
        }

        $layanan = $query->paginate(10);
        $cabang = DB::table('cabang')->orderBy('nama_cabang')->get();

        return view('layanan.index', compact('layanan', 'cabang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required',
            'foto' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ], [
            'nama_layanan.required' => 'Nama Layanan harus diisi.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format file foto harus PNG, JPG, atau JPEG.',
            'foto.max' => 'Ukuran file foto maksimal 2MB.'
        ]);

        $nama_layanan = trim($request->nama_layanan);

        // Auto-generate username unik di background untuk integritas DB
        $baseUsername = Str::slug($nama_layanan, '_');
        if (empty($baseUsername)) {
            $baseUsername = 'layanan_' . time();
        }
        $username = $baseUsername;
        $counter = 1;
        while (DB::table('layanan')->where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }

        $nama_pic = $request->filled('nama_pic') ? trim($request->nama_pic) : null;
        $nip_pic = $request->filled('nip_pic') ? trim($request->nip_pic) : null;
        $jabatan_pic = $request->filled('jabatan_pic') ? trim($request->jabatan_pic) : null;
        $no_hp = $request->filled('no_hp') ? trim($request->no_hp) : null;
        $kode_cabang = $request->filled('kode_cabang') ? $request->kode_cabang : null;
        $password = Hash::make(Str::random(16));

        $foto = null;
        if ($request->hasFile('foto')) {
            $ext = strtolower($request->file('foto')->getClientOriginalExtension());
            if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
                return Redirect::back()->with(['warning' => 'Format file foto harus PNG, JPG, atau JPEG.']);
            }
            $safeUsername = preg_replace('/[^A-Za-z0-9_\-]/', '', $username);
            $foto = time() . '_' . $safeUsername . '.' . $ext;
        }

        try {
            DB::beginTransaction();

            $data = [
                'nama_layanan' => $nama_layanan,
                'username' => $username,
                'nama_pic' => $nama_pic,
                'nip_pic' => $nip_pic,
                'jabatan_pic' => $jabatan_pic,
                'no_hp' => $no_hp,
                'kode_cabang' => $kode_cabang,
                'foto' => $foto,
                'password' => $password,
                'status_location' => '1',
                'status_jam_kerja' => '1',
                'status_scan' => '1',
                'mode_presensi' => $request->mode_presensi ?: 'bebas',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $insertId = DB::table('layanan')->insertGetId($data);

            if ($insertId) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/karyawan/";
                    $request->file('foto')->storeAs($folderPath, $foto);
                }

                DB::commit();
                return Redirect::back()->with(['success' => 'Data Layanan Berhasil Disimpan']);
            }

            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan: ' . $e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $cabang = DB::table('cabang')->orderBy('nama_cabang')->get();
        $layanan = DB::table('layanan')->where('id', $id)->first();

        return view('layanan.edit', compact('layanan', 'cabang'));
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required',
            'foto' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ], [
            'nama_layanan.required' => 'Nama Layanan harus diisi.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format file foto harus PNG, JPG, atau JPEG.',
            'foto.max' => 'Ukuran file foto maksimal 2MB.'
        ]);

        $id = is_numeric($id) ? (int)$id : Crypt::decrypt($id);
        $nama_layanan = trim($request->nama_layanan);
        $old_foto = $request->old_foto;

        $layanan = DB::table('layanan')->where('id', $id)->first();
        if (!$layanan) {
            return Redirect::back()->with(['warning' => 'Data Layanan tidak ditemukan']);
        }

        if ($request->hasFile('foto')) {
            $ext = strtolower($request->file('foto')->getClientOriginalExtension());
            if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
                return Redirect::back()->with(['warning' => 'Format file foto harus PNG, JPG, atau JPEG.']);
            }
            $safeUsername = preg_replace('/[^A-Za-z0-9_\-]/', '', $layanan->username ?: Str::slug($nama_layanan, '_'));
            $foto = time() . '_' . $safeUsername . '.' . $ext;
        } else {
            $foto = $old_foto;
        }

        try {
            DB::beginTransaction();

            $data = [
                'nama_layanan' => $nama_layanan,
                'foto' => $foto,
                'mode_presensi' => $request->mode_presensi ?: 'bebas',
                'updated_at' => now(),
            ];

            if ($request->filled('nama_pic')) {
                $data['nama_pic'] = trim($request->nama_pic);
            }
            if ($request->filled('nip_pic')) {
                $data['nip_pic'] = trim($request->nip_pic);
            }
            if ($request->filled('jabatan_pic')) {
                $data['jabatan_pic'] = trim($request->jabatan_pic);
            }
            if ($request->filled('no_hp')) {
                $data['no_hp'] = trim($request->no_hp);
            }

            if ($request->filled('kode_cabang')) {
                $data['kode_cabang'] = $request->kode_cabang;
            }

            $update = DB::table('layanan')->where('id', $id)->update($data);

            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/karyawan/";
                if ($old_foto) {
                    Storage::delete($folderPath . $old_foto);
                }
                $request->file('foto')->storeAs($folderPath, $foto);
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Akun Layanan Berhasil Diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            DB::table('jadwal_petugas_layanan')->where('layanan_id', $id)->delete();
            DB::table('petugas_layanan')->where('layanan_id', $id)->delete();
            DB::table('jadwal_lokasi_layanan')->where('layanan_id', $id)->delete();
            $delete = DB::table('layanan')->where('id', $id)->delete();
            DB::commit();

            if ($delete) {
                return Redirect::back()->with(['success' => 'Data Layanan Berhasil Dihapus']);
            } else {
                return Redirect::back()->with(['warning' => 'Data Layanan Gagal Dihapus']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with(['warning' => 'Data Layanan Gagal Dihapus: ' . $e->getMessage()]);
        }
    }

    public function resetpassword($id)
    {
        $id = is_numeric($id) ? (int)$id : Crypt::decrypt($id);
        $password = Hash::make('12345');
        $reset = DB::table('layanan')->where('id', $id)->update([
            'password' => $password,
            'updated_at' => now(),
        ]);

        DB::table('petugas_layanan')->where('layanan_id', $id)->limit(1)->update([
            'password' => $password,
            'updated_at' => now(),
        ]);

        if ($reset) {
            return Redirect::back()->with(['success' => 'Password Akun Layanan Berhasil di Reset (Default: 12345)']);
        } else {
            return Redirect::back()->with(['warning' => 'Password Gagal di Reset']);
        }
    }

    public function lockandunlocklocation($id)
    {
        try {
            $layanan = DB::table('layanan')->where('id', $id)->first();
            $status_location = $layanan->status_location == '1' ? '0' : '1';
            DB::table('layanan')->where('id', $id)->update(['status_location' => $status_location]);
            return Redirect::back()->with(['success' => 'Status Lock Lokasi Berhasil Diubah']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal mengubah status lokasi']);
        }
    }

    public function lockandunlockjamkerja($id)
    {
        try {
            $layanan = DB::table('layanan')->where('id', $id)->first();
            $status_jam_kerja = $layanan->status_jam_kerja == '1' ? '0' : '1';
            DB::table('layanan')->where('id', $id)->update(['status_jam_kerja' => $status_jam_kerja]);
            return Redirect::back()->with(['success' => 'Status Lock Jam Kerja Berhasil Diubah']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal mengubah status jam kerja']);
        }
    }

    public function lockandunlockscan($id)
    {
        try {
            $layanan = DB::table('layanan')->where('id', $id)->first();
            $status_scan = $layanan->status_scan == '1' ? '0' : '1';
            DB::table('layanan')->where('id', $id)->update(['status_scan' => $status_scan]);
            return Redirect::back()->with(['success' => 'Status Lock Scan Berhasil Diubah']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Gagal mengubah status scan']);
        }
    }
}

