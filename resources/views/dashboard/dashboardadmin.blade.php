@extends('layouts.admin.tabler')

@section('content')
@php
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][date('w')];
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][date('n')];
    $tanggal_indo = $hari . ', ' . date('j') . ' ' . $bulan . ' ' . date('Y');
@endphp

    <!-- Content Header (Page header) -->
    <div class="row mb-3 align-items-center">
        <div class="col-sm-6">
            <h3 class="m-0 fw-bold text-dark">Dashboard</h3>
            <small class="text-muted">Rekapitulasi Presensi Layanan &bull; {{ $tanggal_indo }}</small>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-end mb-0">
                <li class="breadcrumb-item"><a href="/panel-adm14/dashboardadmin"><i class="bi bi-house-door me-1"></i>Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </div>
    </div>

    <!-- Small boxes (Stat box) -->
    <div class="row">
        <!-- Box 1: Jumlah Akun Layanan -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-primary">
                <div class="inner">
                    <h3>{{ $totalpegawai }}</h3>
                    <p>Akun Layanan</p>
                </div>
                <i class="small-box-icon bi bi-layers-fill"></i>
                <a href="/panel-adm14/layanan" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Kelola Akun Layanan <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>

        <!-- Box 2: Layanan Hadir -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-success">
                <div class="inner">
                    <h3>{{ $jmlhadir }}</h3>
                    <p>Layanan Hadir Hari Ini</p>
                </div>
                <i class="small-box-icon bi bi-fingerprint"></i>
                <a href="/panel-adm14/presensi/monitoring" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Monitoring Absensi <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>

        <!-- Box 3: Total Penerimaan PKB Hari Ini -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-info text-white">
                <div class="inner">
                    <h3 style="font-size: 1.6rem;">Rp {{ number_format($penerimaanHariIni->total_rp ?? 0, 0, ',', '.') }}</h3>
                    <p>Penerimaan PKB Hari Ini</p>
                </div>
                <i class="small-box-icon bi bi-cash-stack"></i>
                <a href="/panel-adm14/penerimaan" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                    Rekap Penerimaan PKB <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>

        <!-- Box 4: Total Unit & Notice Hari Ini -->
        <div class="col-lg-3 col-6">
            <div class="small-box text-bg-warning text-dark">
                <div class="inner">
                    <h3>{{ number_format($penerimaanHariIni->total_unit ?? 0, 0, ',', '.') }} <span style="font-size: 1rem;">Unit</span></h3>
                    <p>{{ number_format($penerimaanHariIni->total_notice ?? 0, 0, ',', '.') }} Notice Dicetak</p>
                </div>
                <i class="small-box-icon bi bi-card-checklist"></i>
                <a href="/panel-adm14/penerimaan" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                    Detail Penerimaan <i class="bi bi-arrow-right-circle"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- /.row -->

@endsection
