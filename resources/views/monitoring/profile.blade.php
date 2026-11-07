@extends('layouts.monitoring')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/monitoring/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Profile Akun</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <div class="col" style="padding-top: 75px; padding-bottom: 75px;">
        @if (Session::get('success'))
            <div class="alert alert-success mb-3">
                {{ Session::get('success') }}
            </div>
        @endif

        <div class="text-center mb-3">
            <img src="{{ asset('assets/img/avatar_layanan.png') }}" alt="Avatar" class="imaged rounded-circle shadow-sm"
                style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #e2e8f0; background: #fff;">
            <div class="mt-2 font-weight-bold" style="font-size: 15px; color: #1e293b;">
                {{ $user->name }}
            </div>
            <div class="text-muted" style="font-size: 12px;">Akun Monitoring SAKTI</div>
        </div>

        <form action="/monitoring/profile/update" method="POST">
            @csrf
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 2px;">Nama Lengkap</label>
                    <input type="text" class="form-control" value="{{ $user->name }}" name="name" placeholder="Nama Lengkap" required autocomplete="off">
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 2px;">Email</label>
                    <input type="text" class="form-control" value="{{ $user->email }}" disabled autocomplete="off" style="background: #f8fafc;">
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 2px;">No. HP</label>
                    <input type="text" class="form-control" value="{{ $user->no_hp }}" name="no_hp" placeholder="No. HP" autocomplete="off">
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 2px;">Password Baru</label>
                    <input type="password" class="form-control" name="password" placeholder="Password Baru" autocomplete="off">
                </div>
            </div>

            <div class="form-group mt-3">
                <button type="submit" class="btn btn-primary btn-block btn-lg" style="background-color: #2A8BF2; border-color: #2A8BF2;">
                    Simpan Perubahan
                </button>
            </div>
        </form>

        <div class="mt-4">
            <a href="/proseslogout" class="btn btn-danger btn-block btn-lg" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <ion-icon name="log-out-outline" style="vertical-align: -1px;"></ion-icon> Keluar Akun
            </a>
        </div>
    </div>
@endsection
