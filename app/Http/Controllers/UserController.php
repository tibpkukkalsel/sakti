<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $departemen = collect([]);
        // Hanya tampilkan role selain administrator (misal: monitoring) di form pembuatan user
        $role = DB::table('roles')->where('guard_name', 'user')->where('name', '!=', 'administrator')->orderBy('id')->get();

        $query = User::query();
        $query->select('users.id', 'users.name', 'users.jabatan', 'email', 'users.no_hp', 'roles.name as role');
        $query->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id');
        $query->join('roles', 'model_has_roles.role_id', '=', 'roles.id');

        // Jangan tampilkan data akun yang berjenis Superadmin / Administrator
        $query->where('roles.name', '!=', 'administrator')
            ->where('users.email', '!=', 'superadmin@presensigps.com')
            ->where('users.id', '!=', 13);

        if (!empty($request->name)) {
            $query->where('users.name', 'like', '%' . $request->name . '%');
        }
        $users = $query->distinct()->paginate(10);
        $users->appends(request()->all());

        return view('users.index', compact('users', 'departemen', 'role'));
    }


    public function store(Request $request)
    {
        $nama_user = $request->nama_user;
        $jabatan = $request->jabatan;
        $email = $request->email;
        $no_hp = $request->no_hp;
        $kode_dept = null;
        $role = 'monitoring';
        $password = bcrypt($request->password);
        $kode_cabang = null;

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $nama_user,
                'jabatan' => $jabatan,
                'email' => $email,
                'no_hp' => $no_hp,
                'kode_dept' => $kode_dept,
                'password' => $password,
                'kode_cabang' => $kode_cabang
            ]);

            $user->assignRole($role);

            DB::commit();

            return Redirect::back()->with(['success' => 'Data User Berhasil Disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal tambah user: ' . $e->getMessage());
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan: ' . $e->getMessage()]);
        }
    }

    public function edit(Request $request)
    {
        $id_user = $request->id_user;

        // Lindungi akun Super Admin dari pengeditan
        if ($id_user == 13) {
            return '<div class="alert alert-warning mb-0"><i class="bi bi-shield-lock-fill me-2"></i> Akun Super Admin dilindungi dan tidak dapat diubah melalui menu ini.</div>';
        }

        $departemen = collect([]);
        $role = DB::table('roles')->where('guard_name', 'user')->where('name', '!=', 'administrator')->orderBy('id')->get();
        $user = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->where('id', $id_user)->first();
        return view('users.edituser', compact('departemen', 'role', 'user'));
    }

    public function update(Request $request, $id_user)
    {
        // Lindungi akun Super Admin dari pengeditan
        if ($id_user == 13) {
            return Redirect::back()->with(['warning' => 'Akun Super Admin dilindungi dan tidak dapat diubah!']);
        }

        $nama_user = $request->nama_user;
        $jabatan = $request->jabatan;
        $email = $request->email;
        $no_hp = $request->no_hp;
        $kode_dept = null;
        $role = 'monitoring';
        $kode_cabang = null;

        $data = [
            'name' => $nama_user,
            'jabatan' => $jabatan,
            'email' => $email,
            'no_hp' => $no_hp,
            'kode_dept' => $kode_dept,
            'kode_cabang' => $kode_cabang
        ];

        if (!empty($request->password)) {
            $data['password'] = bcrypt($request->password);
        }

        DB::beginTransaction();
        try {
            DB::table('users')->where('id', $id_user)->update($data);

            $user = User::find($id_user);
            if ($user) {
                if (is_numeric($role)) {
                    $roleObj = DB::table('roles')->where('id', $role)->first();
                    $roleName = $roleObj ? $roleObj->name : 'monitoring';
                } else {
                    $roleName = $role;
                }
                $user->syncRoles([$roleName]);
            }

            DB::commit();
            return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update user: ' . $e->getMessage());
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate: ' . $e->getMessage()]);
        }
    }


    public function delete($id_user)
    {
        // Lindungi akun Super Admin dari penghapusan
        if ($id_user == 13 || $id_user == 1) {
            return Redirect::back()->with(['warning' => 'Akun Super Admin dilindungi dan tidak dapat dihapus!']);
        }

        $user = User::find($id_user);
        if ($user && ($user->hasRole('administrator') || $user->email == 'superadmin@presensigps.com')) {
            return Redirect::back()->with(['warning' => 'Akun Administrator dilindungi dan tidak dapat dihapus!']);
        }

        try {
            DB::table('users')->where('id', $id_user)->delete();
            return Redirect::back()->with(['success' => 'Data User Berhasil Dihapus']);
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus: ' . $e->getMessage()]);
        }
    }

    public function profile()
    {
        $id = Auth::guard('user')->user()->id;
        $user = DB::table('users')
            ->leftJoin('cabang', 'users.kode_cabang', '=', 'cabang.kode_cabang')
            ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('users.id', $id)
            ->select('users.*', 'cabang.nama_cabang', 'roles.name as role_name')
            ->first();

        return view('users.profile', compact('user'));
    }

    public function updateprofile(Request $request)
    {
        $id = Auth::guard('user')->user()->id;

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini telah digunakan oleh akun lain.',
            'password.min' => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.'
        ];

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed'
        ], $messages);

        $data = [
            'name' => $request->name,
            'email' => $request->email
        ];

        if (!empty($request->password)) {
            $data['password'] = bcrypt($request->password);
        }

        try {
            DB::table('users')->where('id', $id)->update($data);
            return Redirect::back()->with(['success' => 'Data profil berhasil diperbarui!']);
        } catch (\Exception $e) {
            Log::error('Error updateprofile admin: ' . $e->getMessage());
            return Redirect::back()->with(['warning' => 'Gagal memperbarui profil: ' . $e->getMessage()]);
        }
    }
}
