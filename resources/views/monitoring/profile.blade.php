@extends('layouts.monitoring')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/monitoring/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Profil Akun Monitoring</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <div class="section" style="padding-top: 75px; padding-bottom: 75px;">
        @if (Session::get('success'))
            <div class="alert alert-success mb-3">
                {{ Session::get('success') }}
            </div>
        @endif

        <div class="card mb-3 text-center p-3" style="border-radius: 16px; border: 1px solid #e2e8f0;">
            <img src="{{ asset('assets/img/avatar_layanan.png') }}"
                style="width: 75px; height: 75px; border-radius: 50%; object-fit: cover; margin: 0 auto; border: 3px solid #2563eb;">
            <h4 class="mt-2 mb-0 font-weight-bold text-dark">{{ $user->name }}</h4>
            <div class="text-muted small">{{ $user->email }}</div>
            <div class="mt-2">
                <span class="badge bg-primary text-white" style="font-size: 11px; padding: 4px 10px; border-radius: 20px;">
                    <ion-icon name="shield-checkmark" style="vertical-align:-1px;"></ion-icon> Role: Monitoring
                </span>
            </div>
        </div>

        <div class="card" style="border-radius: 16px; border: 1px solid #e2e8f0;">
            <div class="card-header bg-white font-weight-bold py-2 border-bottom">
                Ubah Profil / Password
            </div>
            <div class="card-body p-3">
                <form action="/monitoring/profile/update" method="POST">
                    @csrf
                    <div class="form-group boxed mb-2">
                        <label class="small text-muted font-weight-bold">Nama Petugas</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="form-control" required style="border-radius: 8px;">
                    </div>
                    <div class="form-group boxed mb-2">
                        <label class="small text-muted font-weight-bold">Email / Akun</label>
                        <input type="text" value="{{ $user->email }}" class="form-control" disabled style="border-radius: 8px; background: #f8fafc;">
                    </div>
                    <div class="form-group boxed mb-2">
                        <label class="small text-muted font-weight-bold">No. WhatsApp / HP (Bisa untuk Login)</label>
                        <input type="text" name="no_hp" value="{{ $user->no_hp }}" placeholder="Contoh: 081234567890" class="form-control" style="border-radius: 8px;">
                    </div>
                    <div class="form-group boxed mb-3">
                        <label class="small text-muted font-weight-bold">Password Baru (Kosongkan bila tidak diubah)</label>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" class="form-control" style="border-radius: 8px;">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block w-100 font-weight-bold" style="border-radius: 10px;">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>

        <div class="mt-4 px-1">
            <a href="/proseslogout" class="btn btn-danger btn-block w-100 font-weight-bold"
                style="border-radius: 10px; padding: 10px;"
                onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <ion-icon name="log-out-outline" style="vertical-align:-1px;"></ion-icon> Keluar Akun Monitoring
            </a>
        </div>
    </div>
@endsection

