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

class LayananController extends Controller
{
    public function index(Request $request)
    {
        $kode_cabang = Auth::guard('user')->user()->kode_cabang;
        $user = User::find(Auth::guard('user')->user()->id);

        $query = Layanan::query();
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

        if ($user->hasRole('admin departemen') && !empty($kode_cabang)) {
            $query->where('kode_cabang', $kode_cabang);
        }

        $layanan = $query->paginate(10);
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();

        return view('layanan.index', compact('layanan', 'cabang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required',
            'username' => 'required|unique:layanan,username',
            'nama_pic' => 'required',
        ], [
            'username.unique' => 'Username tersebut sudah digunakan oleh akun layanan lain.',
        ]);

        $nama_layanan = $request->nama_layanan;
        $username = trim($request->username);
        $nama_pic = $request->nama_pic;
        $nip_pic = $request->nip_pic;
        $jabatan_pic = $request->jabatan_pic;
        $no_hp = $request->no_hp;
        $kode_cabang = $request->kode_cabang;
        $password = Hash::make($request->password ?: '12345');

        $foto = null;
        if ($request->hasFile('foto')) {
            $foto = time() . '_' . $username . '.' . $request->file('foto')->getClientOriginalExtension();
        }

        try {
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
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $insertId = DB::table('layanan')->insertGetId($data);

            if ($insertId) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/karyawan/";
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Akun Layanan Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan: ' . $e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $cabang = DB::table('cabang')->orderBy('kode_cabang')->get();
        $layanan = DB::table('layanan')->where('id', $id)->first();

        return view('layanan.edit', compact('layanan', 'cabang'));
    }

    public function update($id, Request $request)
    {
        $id = Crypt::decrypt($id);
        $nama_layanan = $request->nama_layanan;
        $username = trim($request->username);
        $nama_pic = $request->nama_pic;
        $nip_pic = $request->nip_pic;
        $jabatan_pic = $request->jabatan_pic;
        $no_hp = $request->no_hp;
        $kode_cabang = $request->kode_cabang;
        $old_foto = $request->old_foto;

        $cekUsername = DB::table('layanan')
            ->where('username', $username)
            ->where('id', '!=', $id)
            ->count();

        if ($cekUsername > 0) {
            return Redirect::back()->with(['warning' => 'Username sudah digunakan oleh akun layanan lain']);
        }

        if ($request->hasFile('foto')) {
            $foto = time() . '_' . $username . '.' . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $old_foto;
        }

        try {
            $data = [
                'nama_layanan' => $nama_layanan,
                'username' => $username,
                'nama_pic' => $nama_pic,
                'nip_pic' => $nip_pic,
                'jabatan_pic' => $jabatan_pic,
                'no_hp' => $no_hp,
                'kode_cabang' => $kode_cabang,
                'foto' => $foto,
                'updated_at' => now(),
            ];

            if (!empty($request->password)) {
                $data['password'] = Hash::make($request->password);
            }

            $update = DB::table('layanan')->where('id', $id)->update($data);

            if ($update !== false) {
                if ($request->hasFile('foto')) {
                    $folderPath = "public/uploads/karyawan/";
                    if ($old_foto) {
                        Storage::delete($folderPath . $old_foto);
                    }
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Akun Layanan Berhasil Diupdate']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate: ' . $e->getMessage()]);
        }
    }

    public function delete($id)
    {
        $delete = DB::table('layanan')->where('id', $id)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Layanan Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Layanan Gagal Dihapus']);
        }
    }

    public function resetpassword($id)
    {
        $id = Crypt::decrypt($id);
        $password = Hash::make('12345');
        $reset = DB::table('layanan')->where('id', $id)->update([
            'password' => $password
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
