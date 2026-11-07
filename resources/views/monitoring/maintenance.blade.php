@extends('layouts.monitoring')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/monitoring/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Maintenance Layanan</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <style>
        .maint-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .maint-badge {
            background-color: #0d6efd;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
        }
    </style>

    <div class="section" style="padding-top: 70px; padding-bottom: 75px;">
        <!-- Status Unit Sedang Maintenance Hari Ini -->
        <div class="mb-3">
            <h5 class="font-weight-bold text-dark px-1 mb-2 d-flex align-items-center justify-content-between">
                <span><ion-icon name="construct" style="color:#0d6efd; vertical-align:-2px;"></ion-icon> Maintenance Hari Ini</span>
                <span class="badge maint-badge">{{ $maintenanceHariIni->count() }} Unit</span>
            </h5>

            @forelse ($maintenanceHariIni as $item)
                <div class="maint-card border-primary" style="border-left: 4px solid #0d6efd !important;">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ asset('assets/img/avatar_layanan.png') }}"
                            style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 1.5px solid #e2e8f0;">
                        <div style="flex: 1;">
                            <h4 class="mb-0 font-weight-bold text-dark" style="font-size: 14px;">{{ $item->nama_layanan }}</h4>
                            <div class="text-danger small font-weight-bold mt-1">
                                <ion-icon name="warning-outline" style="vertical-align:-1px;"></ion-icon> Status: Sedang Maintenance (X)
                            </div>
                            <div class="text-muted small">
                                PIC: {{ $item->nama_pic ?: '-' }} | No HP: {{ $item->no_hp ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="maint-card text-center py-3">
                    <ion-icon name="checkmark-done-circle" style="font-size: 36px; color: #10b981;"></ion-icon>
                    <div class="text-dark font-weight-bold mt-1" style="font-size: 13.5px;">Seluruh Armada Layanan Beroperasi Baik</div>
                    <div class="text-muted small">Tidak ada unit layanan yang tercatat dalam status perbaikan hari ini.</div>
                </div>
            @endforelse
        </div>

        <!-- Riwayat Maintenance Layanan -->
        <div class="mt-4">
            <h5 class="font-weight-bold text-dark px-1 mb-2">
                <ion-icon name="time-outline" style="color:#64748b; vertical-align:-2px;"></ion-icon> Riwayat Maintenance Terbaru
            </h5>

            @forelse ($riwayatMaintenance as $row)
                <div class="maint-card">
                    <div class="d-flex align-items-center justify-content-between">
                        <strong class="text-dark" style="font-size: 13.5px;">{{ $row->nama_layanan }}</strong>
                        <span class="badge maint-badge">Maintenance (X)</span>
                    </div>
                    <div class="text-muted small mt-1">
                        <ion-icon name="calendar-outline" style="vertical-align:-1px;"></ion-icon> Tanggal: {{ date('d-m-Y', strtotime($row->tgl_presensi)) }}
                    </div>
                    <div class="text-muted small">
                        PIC: {{ $row->nama_pic ?: '-' }}
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-muted small">
                    Belum ada riwayat perbaikan / maintenance layanan.
                </div>
            @endforelse
        </div>
    </div>
@endsection

