@extends('layouts.monitoring')

@section('content')
    <style>
        .logout {
            position: absolute;
            color: white;
            font-size: 28px;
            text-decoration: none;
            right: 16px;
            top: 22px;
            z-index: 10;
        }

        .logout:hover {
            color: white;
        }

        .instansi-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
            margin-bottom: 14px;
            padding-right: 45px;
            text-align: left;
        }

        .instansi-logo {
            height: 36px;
            width: auto;
            max-width: 30px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
            flex-shrink: 0;
        }

        .instansi-text {
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: 0.2px;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
            text-align: left;
        }

        @media (max-width: 375px) {
            .instansi-header {
                gap: 8px;
                padding-right: 40px;
            }

            .instansi-logo {
                height: 30px;
            }

            .instansi-text {
                font-size: 10.5px;
            }
        }

        #user-section {
            min-height: 190px !important;
            height: auto !important;
            padding: 24px 18px 55px 18px !important;
            position: relative;
            background-color: #0c234b;
            background: linear-gradient(135deg, #1E6FD9 0%, #15438C 50%, #0c234b 100%) !important;
        }

        #user-detail {
            display: flex;
            align-items: center;
            margin-top: 6px !important;
        }

        #user-detail .avatar {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        #user-detail .avatar img {
            width: 64px !important;
            height: 64px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            border: 3px solid #ffffff !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.22), 0 0 0 2px rgba(255, 255, 255, 0.4) !important;
            background-color: #ffffff;
        }

        #user-info {
            margin-left: 14px !important;
            line-height: 1.3 !important;
            max-width: calc(100% - 110px);
        }

        #user-name {
            font-size: 15.5px !important;
            font-weight: 700 !important;
            color: #ffd700 !important;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.45) !important;
            margin-bottom: 3px !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #user-role {
            font-size: 12.5px !important;
            color: rgba(255, 255, 255, 0.92) !important;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #menu-section {
            position: relative !important;
            top: 0 !important;
            margin-top: -42px !important;
            width: 100% !important;
            z-index: 99 !important;
            padding: 0 16px !important;
        }

        #presence-section {
            position: relative !important;
            top: 0 !important;
            width: 100% !important;
            margin-top: 10px !important;
            background-color: transparent !important;
        }

        .todaypresence {
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .todaypresence .row {
            display: flex !important;
            align-items: stretch !important;
        }

        .todaypresence .col-6 {
            display: flex !important;
        }

        .todaypresence .card {
            width: 100% !important;
            border-radius: 16px !important;
            border: none !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08) !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }

        .gradasigreen {
            background-color: #16a34a !important;
            background-image: none !important;
            background: #16a34a !important;
            color: #ffffff !important;
        }

        .gradasired {
            background-color: #dc2626 !important;
            background-image: none !important;
            background: #dc2626 !important;
            color: #ffffff !important;
        }

        .todaypresence .card-body {
            padding: 12px 14px !important;
            display: flex !important;
            align-items: center !important;
            width: 100% !important;
        }

        .todaypresence .presencecontent {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 12px !important;
            width: 100% !important;
        }

        .todaypresence .iconpresence {
            width: 48px !important;
            height: 48px !important;
            min-width: 48px !important;
            min-height: 48px !important;
            border-radius: 50% !important;
            background: rgba(255, 255, 255, 0.22) !important;
            backdrop-filter: blur(4px);
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
            border: 2px solid rgba(255, 255, 255, 0.6) !important;
            padding: 0 !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12) !important;
        }

        .todaypresence .iconpresence ion-icon {
            font-size: 26px !important;
            color: #ffffff !important;
            display: block !important;
        }

        .todaypresence .presencedetail {
            flex: 1 !important;
            min-width: 0 !important;
            line-height: normal !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: center !important;
        }

        .todaypresence .presencetitle {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            margin-bottom: 3px !important;
            line-height: 1.2 !important;
            text-transform: capitalize !important;
        }

        .todaypresence .presencedetail span {
            font-size: 12.5px !important;
            font-weight: 600 !important;
            color: rgba(255, 255, 255, 0.95) !important;
            line-height: 1.2 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            display: block !important;
        }

        /* Rekap Presensi 3 Kolom Seragam Otomatis */
        #rekappresensi .row {
            display: flex;
            align-items: stretch;
        }

        #rekappresensi .col-4 {
            display: flex;
        }

        #rekappresensi .card {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        #rekappresensi .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 12px 4px !important;
            position: relative;
        }

        #rekappresensi .rekap-label {
            min-height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
    </style>

    <!-- Header Section (Identik dengan Akun Layanan) -->
    <div class="section" id="user-section">
        <a href="/proseslogout" class="logout" title="Keluar">
            <ion-icon name="exit-outline"></ion-icon>
        </a>

        <!-- Header Instansi: Logo & Nama Instansi -->
        <div class="instansi-header">
            <img src="{{ asset('assets/img/Logo Provinsi Kalimantan Selatan.png') }}" alt="Logo Provinsi Kalimantan Selatan"
                class="instansi-logo" onerror="this.src='{{ asset('Logo Sakti.png') }}'">
            <div class="instansi-text">
                UPPD SAMSAT Batulicin
            </div>
        </div>

        <div id="user-detail">
            <div class="avatar">
                @if (!empty($user->foto))
                    @php
                        $path = Storage::url('uploads/users/' . $user->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="avatar" class="imaged w64 rounded"
                        style="width:64px; height:64px; object-fit:cover;"
                        onerror="this.src='{{ asset('assets/img/avatar_monitoring.png') }}'">
                @else
                    <img src="{{ asset('assets/img/avatar_monitoring.png') }}?v={{ time() }}" alt="avatar" class="imaged w64 rounded"
                        style="width:64px; height:64px; object-fit:cover;">
                @endif
            </div>
            <div id="user-info">
                <h3 id="user-name" style="color: #ffd700 !important; text-shadow: 0 1px 3px rgba(0, 0, 0, 0.45);">
                    {{ $user->name }}
                </h3>
                <span
                    style="font-size: 12px; color: rgba(255, 255, 255, 0.95); display: block; margin-top: 2px; font-weight: 500;">
                    @if (!empty($user->jabatan))
                        {{ $user->jabatan }}
                    @else
                        UPPD SAMSAT Batulicin
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Menu Section (Floating Menu Identik dengan Akun Layanan) -->
    <div class="section" id="menu-section">
        <div class="card">
            <div class="card-body text-center" style="padding: 12px 6px !important;">
                <div class="list-menu">
                    <!-- 1. Layanan -->
                    <div class="item-menu text-center">
                        <a href="/monitoring/layanan" style="text-decoration: none; color: inherit; display: block;">
                            <div class="menu-icon">
                                <span class="orange" style="font-size: 34px; display: inline-block; line-height: 1;">
                                    <ion-icon name="bus-outline"></ion-icon>
                                </span>
                            </div>
                            <div class="menu-name" style="margin-top: 3px;">
                                <span
                                    style="font-size: 11px; font-weight: 600; color: #475569; display: block; line-height: 1.2;">Layanan</span>
                            </div>
                        </a>
                    </div>
                    <!-- 2. Jadwal -->
                    <div class="item-menu text-center">
                        <a href="/monitoring/jadwal" style="text-decoration: none; color: inherit; display: block;">
                            <div class="menu-icon">
                                <span class="warning" style="font-size: 34px; display: inline-block; line-height: 1;">
                                    <ion-icon name="calendar-number"></ion-icon>
                                </span>
                            </div>
                            <div class="menu-name" style="margin-top: 3px;">
                                <span
                                    style="font-size: 11px; font-weight: 600; color: #475569; display: block; line-height: 1.2;">Jadwal</span>
                            </div>
                        </a>
                    </div>
                    <!-- 3. Presensi -->
                    <div class="item-menu text-center">
                        <a href="/monitoring/presensi" style="text-decoration: none; color: inherit; display: block;">
                            <div class="menu-icon">
                                <span class="danger"
                                    style="font-size: 34px; color: #ef4444; display: inline-block; line-height: 1;">
                                    <ion-icon name="finger-print"></ion-icon>
                                </span>
                            </div>
                            <div class="menu-name" style="margin-top: 3px;">
                                <span
                                    style="font-size: 11px; font-weight: 600; color: #475569; display: block; line-height: 1.2;">Presensi</span>
                            </div>
                        </a>
                    </div>
                    <!-- 4. Penerimaan -->
                    <div class="item-menu text-center">
                        <a href="/monitoring/penerimaan" style="text-decoration: none; color: inherit; display: block;">
                            <div class="menu-icon">
                                <span class="success"
                                    style="font-size: 34px; color: #10b981; display: inline-block; line-height: 1;">
                                    <ion-icon name="cash-outline"></ion-icon>
                                </span>
                            </div>
                            <div class="menu-name" style="margin-top: 3px;">
                                <span
                                    style="font-size: 11px; font-weight: 600; color: #475569; display: block; line-height: 1.2;">Penerimaan</span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rekap Status Armada Hari Ini (Card 3 Kolom di Bawah Menu - Ukuran Seragam Otomatis) -->
    <div class="section mt-2">
        <div id="rekappresensi">
            <h3 style="font-size: 13.5px; font-weight: 700; color: #1e293b; margin-bottom: 8px;">
                Rekap Layanan Hari Ini (Total: {{ $totalLayanan }} Layanan)
            </h3>
            <div class="row align-items-stretch">
                <div class="col-4 d-flex">
                    <div class="card w-100">
                        <div class="card-body text-center">
                            <span class="badge bg-success"
                                style="position: absolute; top:4px; right:6px; font-size:0.65rem; z-index:999">
                                {{ $jml_hadir }}
                            </span>
                            <ion-icon name="checkmark-circle-outline" style="font-size: 1.8rem;"
                                class="text-success mb-1"></ion-icon>
                            <div class="rekap-label">
                                <span style="font-size: 0.75rem; font-weight: 600; color: #1e293b;">Hadir</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4 d-flex">
                    <div class="card w-100">
                        <div class="card-body text-center">
                            <span class="badge bg-warning text-dark"
                                style="position: absolute; top:4px; right:6px; font-size:0.65rem; z-index:999">
                                {{ $jml_maintenance }}
                            </span>
                            <ion-icon name="construct-outline" style="font-size: 1.8rem;"
                                class="text-warning mb-1"></ion-icon>
                            <div class="rekap-label">
                                <span style="font-size: 0.75rem; font-weight: 600; color: #1e293b;">Maintenance</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4 d-flex">
                    <div class="card w-100">
                        <div class="card-body text-center">
                            <span class="badge bg-danger"
                                style="position: absolute; top:4px; right:6px; font-size:0.65rem; z-index:999">
                                {{ $jml_belum_absen + $jml_libur }}
                            </span>
                            <ion-icon name="help-circle-outline" style="font-size: 1.8rem;"
                                class="text-danger mb-1"></ion-icon>
                            <div class="rekap-label">
                                <span
                                    style="font-size: 0.72rem; font-weight: 600; line-height: 1.15; color: #1e293b; text-align: center;">Tanpa
                                    Keterangan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Status Penerimaan PKB Hari Ini (Identik dengan Akun Layanan) -->
    <div class="section mt-2">
        <div class="card"
            style="border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden;">
            <div class="card-body p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <div
                            style="width: 36px; height: 36px; border-radius: 10px; background: rgba(16, 185, 129, 0.12); display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                            <ion-icon name="cash-outline" style="font-size: 22px; color: #10b981;"></ion-icon>
                        </div>
                        <div>
                            <div
                                style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                                Penerimaan PKB Hari Ini</div>
                            <div style="font-size: 11px; color: #94a3b8;">
                                {{ $tanggal_indo }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-2 mt-1" style="background: #f8fafc; border-radius: 10px; border: 1px solid #f1f5f9;">
                    <div style="font-size: 11px; color: #64748b;">Total Realisasi Hari Ini</div>
                    <div style="font-size: 18px; font-weight: 800; color: #0f172a; white-space: nowrap;">
                        Rp {{ number_format($penerimaanHariIni, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Detail Penerimaan: R2 & R4 Satu Baris Satu Baris (Icon, Jumlah Penerimaan Rp, dan Jumlah Unit) -->
                <div class="mt-2">
                    <!-- Baris R2 (Motor) -->
                    <div class="d-flex align-items-center justify-content-between p-2 mb-2"
                        style="background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center">
                            <div
                                style="width: 32px; height: 32px; border-radius: 8px; background: rgba(37, 99, 235, 0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-motorbike"
                                    width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="#2563eb"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M5 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                    <path d="M19 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                    <path d="M7.5 14h5l4 -4H7.5m4 0h4l2 -3h-3"></path>
                                </svg>
                            </div>
                            <div style="font-size: 15px; font-weight: 800; color: #1e293b; white-space: nowrap;">
                                Rp {{ number_format($r2HariIniRp, 0, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            <span class="badge"
                                style="background: rgba(37, 99, 235, 0.1); color: #2563eb; font-size: 12.5px; font-weight: 700; padding: 5px 12px; border-radius: 6px;">
                                <ion-icon name="speedometer-outline"
                                    style="font-size: 13px; vertical-align: -1px; margin-right: 3px;"></ion-icon>{{ number_format($r2HariIniUnit, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Baris R4 (Mobil) -->
                    <div class="d-flex align-items-center justify-content-between p-2 mb-2"
                        style="background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center">
                            <div
                                style="width: 32px; height: 32px; border-radius: 8px; background: rgba(8, 145, 178, 0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-car"
                                    width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="#0891b2"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6"></path>
                                    <path d="M7 9l1 3h8l1 -3"></path>
                                </svg>
                            </div>
                            <div style="font-size: 15px; font-weight: 800; color: #1e293b; white-space: nowrap;">
                                Rp {{ number_format($r4HariIniRp, 0, ',', '.') }}
                            </div>
                        </div>
                        <div>
                            <span class="badge"
                                style="background: rgba(8, 145, 178, 0.1); color: #0891b2; font-size: 12.5px; font-weight: 700; padding: 5px 12px; border-radius: 6px;">
                                <ion-icon name="speedometer-outline"
                                    style="font-size: 13px; vertical-align: -1px; margin-right: 3px;"></ion-icon>{{ number_format($r4HariIniUnit, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Baris Total Unit & Notice Dicetak -->
                    <div class="row align-items-center" style="margin-left: -3px; margin-right: -3px;">
                        <div class="col-6" style="padding-left: 3px; padding-right: 3px;">
                            <div class="p-2 d-flex align-items-center justify-content-center"
                                style="background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; min-height: 40px;"
                                title="Total Unit">
                                <ion-icon name="speedometer-outline"
                                    style="font-size: 18px; color: #10b981; margin-right: 6px;"></ion-icon>
                                <span style="font-size: 14px; font-weight: 800; color: #1e293b;">
                                    {{ number_format($objekHariIni, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6" style="padding-left: 3px; padding-right: 3px;">
                            <div class="p-2 d-flex align-items-center justify-content-center"
                                style="background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; min-height: 40px;"
                                title="Notice Dicetak">
                                <ion-icon name="print-outline"
                                    style="font-size: 18px; color: #f59e0b; margin-right: 6px;"></ion-icon>
                                <span style="font-size: 14px; font-weight: 800; color: #1e293b;">
                                    {{ number_format($noticeHariIni, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Presencetab: Tab 1 (Armada Layanan) & Tab 2 (Grafik Penerimaan) Identik dengan Akun Layanan -->
    <div class="section mt-2">
        <div class="presencetab">
            <div class="tab-pane fade show active" id="pilled" role="tabpanel">
                <ul class="nav nav-tabs style1" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-layanan" role="tab">
                            Presensi Layanan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#tab-grafik" role="tab">
                            Grafik Penerimaan
                        </a>
                    </li>
                </ul>
            </div>
            <div class="tab-content mt-2" style="margin-bottom:20px;">
                <!-- Tab 1: Daftar Layanan Keliling (Identik dengan format card histori Layanan) -->
                <div class="tab-pane fade show active" id="tab-layanan" role="tabpanel">
                    @if ($monitoringLayanan->isEmpty())
                        <div class="card" style="border-radius: 12px; border: 1px dashed #cbd5e1;">
                            <div class="card-body text-center py-4">
                                <ion-icon name="bus-outline" style="font-size: 36px; color: #94a3b8;"></ion-icon>
                                <p class="text-muted mt-2 mb-0" style="font-size: 13px;">Belum ada armada layanan
                                    terdaftar.</p>
                            </div>
                        </div>
                    @endif

                    @foreach ($monitoringLayanan as $row)
                        @php
                            $isHadir = $row->status_presensi == 'h' || !empty($row->jam_in);
                            $isMaint = $row->status_presensi == 'm';
                            $isLibur = $row->status_presensi == 'l';

                            if ($isHadir) {
                                $borderColor = '#bbf7d0';
                                $borderLeft = '#22c55e';
                                $badgeStyle = 'background:#dcfce7; color:#15803d;';
                                $badgeText = 'Hadir';
                                $iconColor = 'text-success';
                                $iconName = 'checkmark-circle-outline';
                            } elseif ($isMaint) {
                                $borderColor = '#bfdbfe';
                                $borderLeft = '#3b82f6';
                                $badgeStyle = 'background:#dbeafe; color:#1d4ed8;';
                                $badgeText = 'Maintenance';
                                $iconColor = 'text-primary';
                                $iconName = 'construct-outline';
                            } elseif ($isLibur) {
                                $borderColor = '#fed7aa';
                                $borderLeft = '#f97316';
                                $badgeStyle = 'background:#ffedd5; color:#c2410c;';
                                $badgeText = 'Libur';
                                $iconColor = 'text-warning';
                                $iconName = 'calendar-outline';
                            } else {
                                $borderColor = '#fecaca';
                                $borderLeft = '#ef4444';
                                $badgeStyle = 'background:#fee2e2; color:#b91c1c;';
                                $badgeText = 'Belum Absen';
                                $iconColor = 'text-danger';
                                $iconName = 'alert-circle-outline';
                            }
                        @endphp
                        <div class="card mb-2"
                            style="border-radius: 12px; border: 1px solid {{ $borderColor }}; border-left: 5px solid {{ $borderLeft }}; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                            <div class="card-body py-2 px-3">
                                <div class="historicontent align-items-center">
                                    <div class="iconpresensi mr-2" style="min-width: 44px;">
                                        <ion-icon name="{{ $iconName }}" style="font-size: 40px;"
                                            class="{{ $iconColor }}"></ion-icon>
                                    </div>
                                    <div class="datapresensi flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h4
                                                style="margin:0px !important; font-weight: 700; color: #1e293b; font-size: 14px;">
                                                {{ $row->nama_layanan }}
                                            </h4>
                                            <span class="badge"
                                                style="font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 6px; {{ $badgeStyle }}">
                                                {{ $badgeText }}
                                            </span>
                                        </div>
                                        <div style="font-size: 12px; color: #64748b; margin-bottom: 3px;">
                                            <span style="font-weight: 600; color: #2A8BF2;">
                                                <ion-icon name="location-outline"
                                                    style="vertical-align: -1px;"></ion-icon> {{ $row->lokasi }}
                                            </span>
                                            <span class="mx-1">&bull;</span>
                                            <span>{{ $row->nama_jam_kerja ?? 'Reguler' }}</span>
                                        </div>
                                        <div style="font-size: 13px; margin-bottom: 4px;">
                                            <span>Scan: </span>
                                            @if (!empty($row->jam_in))
                                                <strong
                                                    style="color:#0f172a;">{{ date('H:i', strtotime($row->jam_in)) }}</strong>
                                            @else
                                                <span class="text-danger font-weight-bold">Belum Scan</span>
                                            @endif
                                            <span> - </span>
                                            @if (!empty($row->jam_out))
                                                <strong
                                                    style="color:#0f172a;">{{ date('H:i', strtotime($row->jam_out)) }}</strong>
                                            @else
                                                <span class="text-muted">Belum Scan</span>
                                            @endif
                                        </div>
                                        <div>
                                            <span style="font-size: 12px; font-weight: 700; color: #10b981;">
                                                <ion-icon name="cash-outline" style="vertical-align: -1px;"></ion-icon>
                                                Rp {{ number_format($row->penerimaan_hari_ini ?? 0, 0, ',', '.') }}
                                                <small
                                                    class="text-muted font-weight-normal">({{ $row->unit_hari_ini ?? 0 }}
                                                    Unit)</small>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tab 2: Grafik Penerimaan (Identik dengan Akun Layanan) -->
                <div class="tab-pane fade" id="tab-grafik" role="tabpanel">
                    <!-- Ringkasan Akumulasi Tahun Berjalan -->
                    <div class="card mb-2" style="border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
                        <div class="card-body p-3">
                            <div class="mb-2">
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                    Total Penerimaan {{ $tahunini }}
                                </div>
                                <div style="color: #2A8BF2; font-size: 18px; font-weight: 800; white-space: nowrap; margin-top: 2px;">
                                    Rp {{ number_format($totalTahunBerjalanRp, 0, ',', '.') }}
                                </div>
                            </div>

                            <!-- Baris R2 (Motor) -->
                            <div class="d-flex align-items-center justify-content-between p-2 mb-2"
                                style="background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                                <div class="d-flex align-items-center">
                                    <div
                                        style="width: 30px; height: 30px; border-radius: 8px; background: rgba(37, 99, 235, 0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-motorbike"
                                            width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="#2563eb"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M5 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                            <path d="M19 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                            <path d="M7.5 14h5l4 -4H7.5m4 0h4l2 -3h-3"></path>
                                        </svg>
                                    </div>
                                    <div style="font-size: 14px; font-weight: 700; color: #1e293b; white-space: nowrap;">
                                        Rp {{ number_format($totalTahunBerjalanR2Rp, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div>
                                    <span class="badge"
                                        style="background: rgba(37, 99, 235, 0.1); color: #2563eb; font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                        <ion-icon name="speedometer-outline"
                                            style="font-size: 12px; vertical-align: -1px; margin-right: 3px;"></ion-icon>{{ number_format($totalTahunBerjalanR2Unit, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Baris R4 (Mobil) -->
                            <div class="d-flex align-items-center justify-content-between p-2 mb-2"
                                style="background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                                <div class="d-flex align-items-center">
                                    <div
                                        style="width: 30px; height: 30px; border-radius: 8px; background: rgba(8, 145, 178, 0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-car"
                                            width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="#0891b2"
                                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6"></path>
                                            <path d="M7 9l1 3h8l1 -3"></path>
                                        </svg>
                                    </div>
                                    <div style="font-size: 14px; font-weight: 700; color: #1e293b; white-space: nowrap;">
                                        Rp {{ number_format($totalTahunBerjalanR4Rp, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div>
                                    <span class="badge"
                                        style="background: rgba(8, 145, 178, 0.1); color: #0891b2; font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                        <ion-icon name="speedometer-outline"
                                            style="font-size: 12px; vertical-align: -1px; margin-right: 3px;"></ion-icon>{{ number_format($totalTahunBerjalanR4Unit, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Baris Total Unit & Notice Dicetak -->
                            <div class="row align-items-center" style="margin-left: -3px; margin-right: -3px;">
                                <div class="col-6" style="padding-left: 3px; padding-right: 3px;">
                                    <div class="p-2 d-flex align-items-center justify-content-center"
                                        style="background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; min-height: 38px;">
                                        <ion-icon name="speedometer-outline"
                                            style="font-size: 16px; color: #10b981; margin-right: 6px;"></ion-icon>
                                        <span class="text-muted me-1" style="font-size: 11px;">Unit:</span>
                                        <span style="font-size: 13.5px; font-weight: 800; color: #1e293b;">
                                            {{ number_format($totalTahunBerjalanUnit, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-6" style="padding-left: 3px; padding-right: 3px;">
                                    <div class="p-2 d-flex align-items-center justify-content-center"
                                        style="background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; min-height: 38px;">
                                        <ion-icon name="print-outline"
                                            style="font-size: 16px; color: #f59e0b; margin-right: 6px;"></ion-icon>
                                        <span class="text-muted me-1" style="font-size: 11px;">Notice:</span>
                                        <span style="font-size: 13.5px; font-weight: 800; color: #1e293b;">
                                            {{ number_format($totalTahunBerjalanNotice, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Grafik Tren Penerimaan Bulanan (ApexCharts) -->
                    <div class="card" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center px-2 pt-2 mb-1">
                                <span class="fw-bold text-dark" style="font-size: 13px;">Tren Perbulan
                                    ({{ $tahunini }})</span>
                            </div>
                            <div id="chart-penerimaan-monitoring" style="min-height: 250px;"></div>
                        </div>
                    </div>

                    <!-- Card Grafik Bulanan Per Layanan (ApexCharts) -->
                    <div class="card mt-2" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center px-2 pt-2 mb-1">
                                <span class="fw-bold text-dark" style="font-size: 13px;">Bulanan Per Layanan
                                    ({{ $tahunini }})</span>
                            </div>
                            <div id="chart-layanan-perbulan-monitoring" style="min-height: 250px;"></div>
                        </div>
                    </div>

                    <!-- Card Chart Pie Total & Persentase Pendapatan Per Layanan (ApexCharts) -->
                    <div class="card mt-2" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center px-2 pt-2 mb-1">
                                <span class="fw-bold text-dark" style="font-size: 13px;">Total & Persentase Pendapatan Per
                                    Layanan ({{ $tahunini }})</span>
                            </div>
                            <div id="chart-pie-layanan-monitoring" style="min-height: 280px;"></div>
                        </div>
                    </div>

                    <!-- Card Chart Pie Persentase Pendapatan Berdasarkan Hari (ApexCharts) -->
                    <div class="card mt-2" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center px-2 pt-2 mb-1">
                                <span class="fw-bold text-dark" style="font-size: 13px;">Persentase Pendapatan Berdasarkan
                                    Hari ({{ $tahunini }})</span>
                            </div>
                            <div id="chart-pie-hari-monitoring" style="min-height: 380px;"></div>

                            <!-- Petunjuk atau Rincian saat Warna Pie Hari Diklik -->
                            <div id="detail-hari-hint" class="text-center py-2 px-1 text-muted"
                                style="font-size: 11px; font-style: italic; border-top: 1px dashed #e2e8f0;">
                                <ion-icon name="finger-print-outline"
                                    style="vertical-align: -2px; font-size: 13px;"></ion-icon>
                                Ketuk melihat rincian
                            </div>

                            <div id="detail-hari-wrapper" class="mt-2 pt-2 border-top" style="display: none;">
                                <div class="px-1 mb-2">
                                    <span style="font-size: 13px; font-weight: 700; color: #1e293b;">
                                        Rincian Hari <span id="label-selected-hari"
                                            style="color: #2A8BF2; font-weight: 700;"></span>
                                    </span>
                                </div>
                                <div id="detail-hari-list"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Copyright Adimas Fitriandi (Identik dengan Akun Layanan) -->
            <div class="text-center pt-2 pb-4"
                style="color: #64748b; font-size: 11.5px; line-height: 1.5; margin-bottom: 75px;">
                <div style="font-weight: 600; color: #475569;">
                    Copyright &copy; {{ date('Y') }} Adimas Fitriandi - Bapenda Prov. Kalsel
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <!-- ApexCharts Script (Identik dengan Akun Layanan & Dashboard Admin) -->
    <script src="{{ asset('tabler/dist/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        if (typeof ApexCharts === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/apexcharts"><\/script>');
        }
    </script>
    <script>
        var chartPenerimaanMonitoring = null;

        function renderChartPenerimaanMonitoring() {
            var el = document.querySelector("#chart-penerimaan-monitoring");
            if (!el) return;

            if (typeof ApexCharts === 'undefined') {
                setTimeout(renderChartPenerimaanMonitoring, 200);
                return;
            }

            if (chartPenerimaanMonitoring) {
                try {
                    chartPenerimaanMonitoring.destroy();
                } catch (e) {}
            }

            var optionsPenerimaan = {
                series: [{
                    name: 'Total (Rp)',
                    data: @json($chartBulanValuesTotal)
                }, {
                    name: 'R2 (Rp)',
                    data: @json($chartBulanValuesR2)
                }, {
                    name: 'R4 (Rp)',
                    data: @json($chartBulanValuesR4)
                }],
                chart: {
                    type: 'bar',
                    height: 270,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: @json($chartBulanLabels),
                    labels: {
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '10px'
                        },
                        formatter: function(val) {
                            if (val >= 1000000000) return (val / 1000000000).toFixed(1) + 'M';
                            if (val >= 1000000) return (val / 1000000).toFixed(0) + 'Jt';
                            return val;
                        }
                    }
                },
                colors: ['#2A8BF2', '#10b981', '#f59e0b'],
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + Number(val).toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '11px',
                    markers: {
                        radius: 12
                    }
                }
            };

            chartPenerimaanMonitoring = new ApexCharts(el, optionsPenerimaan);
            chartPenerimaanMonitoring.render();
        }

        var chartLayananPerBulanMonitoring = null;

        function renderChartLayananPerBulanMonitoring() {
            var el = document.querySelector("#chart-layanan-perbulan-monitoring");
            if (!el) return;

            if (typeof ApexCharts === 'undefined') {
                setTimeout(renderChartLayananPerBulanMonitoring, 200);
                return;
            }

            if (chartLayananPerBulanMonitoring) {
                try {
                    chartLayananPerBulanMonitoring.destroy();
                } catch (e) {}
            }

            var optionsLayanan = {
                series: @json($chartLayananPerBulanSeries),
                chart: {
                    type: 'bar',
                    height: 270,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: @json($chartBulanLabels),
                    labels: {
                        style: {
                            fontSize: '10px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '10px'
                        },
                        formatter: function(val) {
                            if (val >= 1000000000) return (val / 1000000000).toFixed(1) + 'M';
                            if (val >= 1000000) return (val / 1000000).toFixed(0) + 'Jt';
                            return val;
                        }
                    }
                },
                colors: ['#0284c7', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'],
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + Number(val).toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '11px',
                    markers: {
                        radius: 12
                    }
                }
            };

            chartLayananPerBulanMonitoring = new ApexCharts(el, optionsLayanan);
            chartLayananPerBulanMonitoring.render();
        }

        var chartPieLayananMonitoring = null;

        function renderChartPieLayananMonitoring() {
            var el = document.querySelector("#chart-pie-layanan-monitoring");
            if (!el) return;

            if (typeof ApexCharts === 'undefined') {
                setTimeout(renderChartPieLayananMonitoring, 200);
                return;
            }

            if (chartPieLayananMonitoring) {
                try {
                    chartPieLayananMonitoring.destroy();
                } catch (e) {}
            }

            var optionsPie = {
                series: @json($pieLayananValues),
                labels: @json($pieLayananLabels),
                chart: {
                    type: 'pie',
                    height: 290,
                    fontFamily: 'inherit'
                },
                colors: ['#2A8BF2', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + "%";
                    },
                    style: {
                        fontSize: '11px',
                        fontWeight: 700,
                        colors: ['#000000']
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + Number(val).toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'left',
                    fontSize: '11px',
                    formatter: function(seriesName, opts) {
                        var val = opts.w.globals.series[opts.seriesIndex];
                        var total = opts.w.globals.series.reduce(function(a, b) {
                            return a + b;
                        }, 0);
                        var percent = total > 0 ? (val / total * 100).toFixed(1) : 0;
                        return seriesName + ': Rp ' + Number(val).toLocaleString('id-ID') + ' (' + percent + '%)';
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 310
                        },
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'left'
                        }
                    }
                }]
            };

            chartPieLayananMonitoring = new ApexCharts(el, optionsPie);
            chartPieLayananMonitoring.render();
        }

        var chartPieHariMonitoring = null;
        var detailPerHariData = @json($detailPerHariJson);
        var selectedHariMonitoring = null;

        function showDetailHari(hariName) {
            var hint = document.getElementById('detail-hari-hint');
            var wrapper = document.getElementById('detail-hari-wrapper');
            var labelHari = document.getElementById('label-selected-hari');
            var listContainer = document.getElementById('detail-hari-list');

            if (!wrapper || !listContainer) return;

            // Jika hari yang sama diklik kembali dan rincian sedang terbuka, maka tutup
            if (selectedHariMonitoring === hariName && wrapper.style.display !== 'none') {
                wrapper.style.display = 'none';
                if (hint) hint.style.display = 'block';
                selectedHariMonitoring = null;
                return;
            }

            selectedHariMonitoring = hariName;
            if (hint) hint.style.display = 'none';
            wrapper.style.display = 'block';
            if (labelHari) labelHari.textContent = hariName;

            var items = detailPerHariData[hariName] || [];
            if (items.length === 0) {
                listContainer.innerHTML =
                    '<div class="text-center py-2 text-muted" style="font-size: 11.5px;">Tidak ada data penerimaan pada hari ini.</div>';
                return;
            }

            var html = '';
            items.forEach(function(item, idx) {
                var rankColor = (idx === 0) ? '#d97706' : '#64748b';
                var rankBg = (idx === 0) ? '#fef3c7' : '#f1f5f9';
                var rankBadge =
                    '<span style="display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:' +
                    rankBg + '; color:' + rankColor + '; font-size:12px; font-weight:800;">' + (idx + 1) +
                    '</span>';

                html +=
                    '<div class="card mb-3" style="border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,0.04); background: #ffffff;">';
                html += '  <div class="card-body p-3">';
                html += '    <div class="d-flex align-items-start mb-2">';
                html += '      <div style="flex-shrink: 0; margin-right: 14px; margin-top: 1px;">';
                html += rankBadge;
                html += '      </div>';
                html += '      <div style="flex: 1; min-width: 0;">';
                html += '        <div class="d-flex justify-content-between align-items-baseline">';
                html +=
                    '          <div class="fw-bold text-dark text-truncate pe-2" style="font-size: 14px; line-height: 1.3;">' +
                    (item.nama_layanan || '-') + '</div>';
                html +=
                    '          <div class="fw-bold text-nowrap" style="font-size: 14px; color: #10b981; line-height: 1.3;">Rp ' +
                    Number(item.total_rp).toLocaleString('id-ID') + '</div>';
                html += '        </div>';
                html +=
                    '        <div class="text-muted text-truncate mt-1" style="font-size: 11.5px; line-height: 1.4;">';
                html +=
                    '          <ion-icon name="location-outline" style="vertical-align: -1.5px; font-size: 13px; color: #2A8BF2; margin-right: 4px;"></ion-icon>' +
                    (item.nama_lokasi || 'UPPD SAMSAT Batulicin');
                html += '        </div>';
                html += '      </div>';
                html += '    </div>';

                html += '    <div class="pt-2 border-top" style="border-color: #f1f5f9 !important;">';
                // Baris R2 (Motor)
                html +=
                    '      <div class="d-flex align-items-center justify-content-between p-2 mb-2" style="background: #f8fafc; border-radius: 8px; border: 1px solid #f1f5f9;">';
                html += '        <div class="d-flex align-items-center">';
                html +=
                    '          <div style="width: 26px; height: 26px; border-radius: 6px; background: rgba(37, 99, 235, 0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">';
                html +=
                    '            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-motorbike" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#2563eb" fill="none" stroke-linecap="round" stroke-linejoin="round">';
                html += '              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>';
                html += '              <path d="M5 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>';
                html += '              <path d="M19 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>';
                html += '              <path d="M7.5 14h5l4 -4H7.5m4 0h4l2 -3h-3"></path>';
                html += '            </svg>';
                html += '          </div>';
                html += '          <span class="fw-bold text-dark" style="font-size: 12.5px;">Rp ' + Number(item
                    .rp_r2 || 0).toLocaleString('id-ID') + '</span>';
                html += '        </div>';
                html +=
                    '        <span class="badge" style="background: rgba(37, 99, 235, 0.1); color: #2563eb; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px;">';
                html += '          ' + Number(item.unit_r2 || 0).toLocaleString('id-ID') + ' Unit';
                html += '        </span>';
                html += '      </div>';

                // Baris R4 (Mobil)
                html +=
                    '      <div class="d-flex align-items-center justify-content-between p-2 mb-2" style="background: #f8fafc; border-radius: 8px; border: 1px solid #f1f5f9;">';
                html += '        <div class="d-flex align-items-center">';
                html +=
                    '          <div style="width: 26px; height: 26px; border-radius: 6px; background: rgba(8, 145, 178, 0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">';
                html +=
                    '            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-car" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#0891b2" fill="none" stroke-linecap="round" stroke-linejoin="round">';
                html += '              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>';
                html += '              <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>';
                html += '              <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>';
                html += '              <path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6"></path>';
                html += '              <path d="M7 9l1 3h8l1 -3"></path>';
                html += '            </svg>';
                html += '          </div>';
                html += '          <span class="fw-bold text-dark" style="font-size: 12.5px;">Rp ' + Number(item
                    .rp_r4 || 0).toLocaleString('id-ID') + '</span>';
                html += '        </div>';
                html +=
                    '        <span class="badge" style="background: rgba(8, 145, 178, 0.1); color: #0891b2; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px;">';
                html += '          ' + Number(item.unit_r4 || 0).toLocaleString('id-ID') + ' Unit';
                html += '        </span>';
                html += '      </div>';

                // Ringkasan Unit & Notice Dicetak
                html += '      <div class="row g-2 text-center" style="font-size: 11.5px;">';
                html += '        <div class="col-6">';
                html +=
                    '          <div class="p-1 px-2 d-flex align-items-center justify-content-center" style="background: #f1f5f9; border-radius: 6px; min-height: 32px;">';
                html +=
                    '            <ion-icon name="speedometer-outline" style="font-size: 14px; color: #10b981; margin-right: 6px;"></ion-icon>';
                html += '            <span class="text-muted me-1">Unit:</span>';
                html += '            <strong class="text-dark">' + Number(item.total_unit || 0).toLocaleString(
                    'id-ID') + '</strong>';
                html += '          </div>';
                html += '        </div>';
                html += '        <div class="col-6">';
                html +=
                    '          <div class="p-1 px-2 d-flex align-items-center justify-content-center" style="background: #f1f5f9; border-radius: 6px; min-height: 32px;">';
                html +=
                    '            <ion-icon name="print-outline" style="font-size: 14px; color: #f59e0b; margin-right: 6px;"></ion-icon>';
                html += '            <span class="text-muted me-1">Notice:</span>';
                html += '            <strong class="text-dark">' + Number(item.total_notice || item.total_unit || 0)
                    .toLocaleString('id-ID') + '</strong>';
                html += '          </div>';
                html += '        </div>';
                html += '      </div>';
                html += '    </div>';
                html += '  </div>';
                html += '</div>';
            });

            listContainer.innerHTML = html;
        }

        function renderChartPieHariMonitoring() {
            var el = document.querySelector("#chart-pie-hari-monitoring");
            if (!el) return;

            if (typeof ApexCharts === 'undefined') {
                setTimeout(renderChartPieHariMonitoring, 200);
                return;
            }

            if (chartPieHariMonitoring) {
                try {
                    chartPieHariMonitoring.destroy();
                } catch (e) {}
            }

            var hariLabels = @json($pieHariLabels);
            var hariValues = @json($pieHariValues);

            var optionsPieHari = {
                series: hariValues,
                labels: hariLabels,
                chart: {
                    type: 'pie',
                    height: 380,
                    fontFamily: 'inherit',
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            var selectedIndex = config.dataPointIndex;
                            if (selectedIndex !== undefined && hariLabels[selectedIndex]) {
                                showDetailHari(hariLabels[selectedIndex]);
                            }
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        customScale: 1.02,
                        dataLabels: {
                            offset: -5
                        }
                    }
                },
                colors: ['#2A8BF2', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899', '#64748b'],
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + "%";
                    },
                    style: {
                        fontSize: '11px',
                        fontWeight: 700,
                        colors: ['#000000']
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + Number(val).toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'left',
                    fontSize: '11px',
                    formatter: function(seriesName, opts) {
                        var val = opts.w.globals.series[opts.seriesIndex];
                        var total = opts.w.globals.series.reduce(function(a, b) {
                            return a + b;
                        }, 0);
                        var percent = total > 0 ? (val / total * 100).toFixed(1) : 0;
                        return seriesName + ': Rp ' + Number(val).toLocaleString('id-ID') + ' (' + percent + '%)';
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 400
                        },
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'left'
                        }
                    }
                }]
            };

            chartPieHariMonitoring = new ApexCharts(el, optionsPieHari);
            chartPieHariMonitoring.render();
        }

        document.addEventListener('DOMContentLoaded', function() {
            renderChartPenerimaanMonitoring();
            renderChartLayananPerBulanMonitoring();
            renderChartPieLayananMonitoring();
            renderChartPieHariMonitoring();

            // Re-render chart saat tab Grafik diklik
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                if ($(e.target).attr('href') === '#tab-grafik') {
                    setTimeout(function() {
                        renderChartPenerimaanMonitoring();
                        renderChartLayananPerBulanMonitoring();
                        renderChartPieLayananMonitoring();
                        renderChartPieHariMonitoring();
                    }, 150);
                }
            });
        });
    </script>
@endpush
