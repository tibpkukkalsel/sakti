<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    public function proseslogin(Request $request)
    {
        // Otomatis aktifkan "Remember Me" agar pada device & browser yang sama otomatis login
        $remember = true;
        $roleType = $request->input('role_type', 'layanan');
        $loginInput = trim($request->username ?? $request->email ?? $request->nik ?? '');
        $password = $request->password;

        if (empty($loginInput) || empty($password)) {
            return redirect('/')->with(['warning' => 'Username/Email/No. HP dan Password wajib diisi']);
        }

        // Variasi format no HP (misal: 0853..., +62853..., 62853..., 0853-4823-6515)
        $cleanPhone = preg_replace('/[^0-9]/', '', $loginInput);
        $phoneCandidates = array_values(array_unique(array_filter([
            $loginInput,
            $cleanPhone,
            (!empty($cleanPhone) && str_starts_with($cleanPhone, '62')) ? ('0' . substr($cleanPhone, 2)) : null,
            (!empty($cleanPhone) && str_starts_with($cleanPhone, '0')) ? ('62' . substr($cleanPhone, 1)) : null,
        ])));

        // 1. Autentikasi untuk Akun Monitoring
        if ($roleType === 'monitoring') {
            $user = User::where('email', $loginInput)
                ->orWhere('name', $loginInput)
                ->orWhereIn('no_hp', $phoneCandidates)
                ->first();

            if ($user && Hash::check($password, $user->password)) {
                if ($user->hasRole('monitoring') || $user->hasRole('administrator') || $user->hasRole('admin departemen')) {
                    Auth::guard('user')->login($user, $remember);
                    if ($request->hasSession()) {
                        $request->session()->regenerate();
                    }
                    return redirect('/monitoring/dashboard');
                } else {
                    return redirect('/')->with(['warning' => 'Akun tidak memiliki hak akses Monitoring']);
                }
            } else {
                return redirect('/')->with(['warning' => 'Email/No. HP/Username atau Password Monitoring salah']);
            }
        }

        // 2. Autentikasi untuk Akun Layanan
        // Cari pegawai/layanan berdasarkan NIK (ID), Username, atau No HP
        $karyawan = Layanan::where('username', $loginInput)
            ->orWhere('id', $loginInput)
            ->orWhereIn('no_hp', $phoneCandidates)
            ->first();

        if ($karyawan && Hash::check($password, $karyawan->password)) {
            Auth::guard('karyawan')->login($karyawan, $remember);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }
            return redirect('/dashboard');
        }

        // Fallback cerdas: jika pengguna berada di tab Layanan namun memasukkan akun Monitoring yang valid
        $userAlt = User::where('email', $loginInput)
            ->orWhere('name', $loginInput)
            ->orWhereIn('no_hp', $phoneCandidates)
            ->first();
        if ($userAlt && Hash::check($password, $userAlt->password) && ($userAlt->hasRole('monitoring') || $userAlt->hasRole('administrator'))) {
            Auth::guard('user')->login($userAlt, $remember);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }
            return redirect('/monitoring/dashboard');
        }

        return redirect('/')->with(['warning' => 'Username atau Password Salah']);
    }

    public function proseslogout()
    {
        if (Auth::guard('karyawan')->check()) {
            Auth::guard('karyawan')->logout();
        }
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
        }
        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
        return redirect('/');
    }

    public function proseslogoutadmin()
    {
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
            return redirect('/panel-adm14');
        }
        return redirect('/panel-adm14');
    }

    public function prosesloginadmin(Request $request)
    {
        if (Auth::guard('user')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect('/panel-adm14/home');
        } else {
            return redirect('/panel-adm14')->with(['warning' => 'Username atau Password Salah']);
        }
    }
}
