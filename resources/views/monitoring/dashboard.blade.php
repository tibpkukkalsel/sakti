@extends('layouts.monitoring')

@section('content')
    <style>
        .logout-btn-header {
            position: absolute;
            right: 18px;
            top: 24px;
            color: #ffffff !important;
            font-size: 26px;
            line-height: 1;
            z-index: 10;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .logout-btn-header:hover {
            background: rgba(239, 68, 68, 0.8);
            transform: scale(1.05);
        }

        .instansi-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            padding-right: 50px;
        }

        .instansi-logo {
            height: 34px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.15));
        }

        .instansi-text {
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        #user-section {
            min-height: 195px !important;
            height: auto !important;
            padding: 22px 18px 45px 18px !important;
            position: relative;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #38bdf8 100%);
            box-shadow: 0 4px 18px rgba(37, 99, 235, 0.25);
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
            width: 60px !important;
            height: 60px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            border: 3px solid #ffffff !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
            background-color: #ffffff;
        }

        #user-info {
            margin-left: 14px !important;
            line-height: 1.3 !important;
            max-width: calc(100% - 85px);
        }

        #user-info h3 {
            color: #ffffff;
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 4px 0;
            letter-spacing: 0.2px;
        }

        #menu-section {
            margin-top: -30px;
            padding: 0 16px;
            position: relative;
            z-index: 5;
        }

        .menu-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            padding: 14px 8px;
        }

        .list-menu {
            display: flex;
            justify-content: space-around;
            align-items: flex-start;
            text-align: center;
        }

        .item-menu {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #334155;
            transition: transform 0.2s;
        }

        .item-menu:active {
            transform: scale(0.95);
        }

        .menu-icon-box {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #ffffff;
            margin-bottom: 6px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        .item-menu span {
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            color: #1e293b;
        }

        /* Stat Badges */
        .stat-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-top: 10px;
        }

        .stat-pill {
            border-radius: 10px;
            padding: 8px 4px;
            text-align: center;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .stat-pill .num {
            font-size: 18px;
            font-weight: 800;
            line-height: 1;
        }

        .stat-pill .lbl {
            font-size: 10px;
            font-weight: 600;
            opacity: 0.95;
            margin-top: 3px;
        }

        /* Card Section */
        .dash-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 16px;
            margin: 14px 16px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .dash-card-title {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .penerimaan-hero {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border-radius: 14px;
            color: #ffffff;
            padding: 14px;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
        }

        .live-service-card {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-bottom: 8px;
            gap: 12px;
        }
    </style>

    <div class="section" id="user-section">
        <!-- Tombol Logout Cepat -->
        <a href="/proseslogout" class="logout-btn-header" title="Keluar Akun"
            onclick="return confirm('Apakah Anda yakin ingin logout dari akun monitoring?')">
            <ion-icon name="log-out-outline"></ion-icon>
        </a>

        <!-- Header Instansi: Logo & Nama Instansi -->
        <div class="instansi-header">
            <img src="{{ asset('assets/img/Logo Provinsi Kalimantan Selatan.png') }}"
                alt="Logo Provinsi Kalimantan Selatan" class="instansi-logo"
                onerror="this.src='{{ asset('Logo Sakti.png') }}'">
            <div class="instansi-text">
                UPPD SAMSAT Batulicin
            </div>
        </div>

        <div id="user-detail">
            <div class="avatar">
                <img src="{{ asset('assets/img/avatar_layanan.png') }}" alt="Avatar Monitoring"
                    style="border: 2px solid #ffffff;">
            </div>
            <div id="user-info">
                <h3>{{ $user->name }}</h3>
                <span class="badge"
                    style="background: rgba(255, 255, 255, 0.22); color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 5px; font-weight: 600;">
                    <ion-icon name="shield-checkmark" style="vertical-align: -1px; color: #fde047;"></ion-icon> Akun Monitoring
                </span>
                <span style="font-size: 11px; color: rgba(255, 255, 255, 0.92); display: block; margin-top: 3px;">
                    <ion-icon name="calendar-outline" style="vertical-align: -1px;"></ion-icon> {{ $tanggal_indo }}
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Access Menu Grid (Bentuk Khas Tampilan Awal) -->
    <div class="section" id="menu-section">
        <div class="menu-card">
            <div class="list-menu">
                <a href="/monitoring/layanan" class="item-menu">
                    <div class="menu-icon-box" style="background: linear-gradient(135deg, #2563eb, #1d4ed8);">
                        <ion-icon name="bus-outline"></ion-icon>
                    </div>
                    <span>Layanan</span>
                </a>
                <a href="/monitoring/jadwal" class="item-menu">
                    <div class="menu-icon-box" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <ion-icon name="calendar-outline"></ion-icon>
                    </div>
                    <span>Jadwal</span>
                </a>
                <a href="/monitoring/maintenance" class="item-menu">
                    <div class="menu-icon-box" style="background: linear-gradient(135deg, #0284c7, #0369a1);">
                        <ion-icon name="construct-outline"></ion-icon>
                    </div>
                    <span>Maintenance</span>
                </a>
                <a href="/monitoring/presensi" class="item-menu">
                    <div class="menu-icon-box" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <ion-icon name="finger-print-outline"></ion-icon>
                    </div>
                    <span>Presensi</span>
                </a>
                <a href="/monitoring/penerimaan" class="item-menu">
                    <div class="menu-icon-box" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                        <ion-icon name="cash-outline"></ion-icon>
                    </div>
                    <span>Penerimaan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Card 1: Status Kehadiran Layanan Hari Ini -->
    <div class="dash-card">
        <div class="dash-card-title">
            <span><ion-icon name="pulse-outline" style="color:#2563eb; vertical-align:-2px;"></ion-icon> Presensi Layanan Hari Ini</span>
            <small class="text-muted fw-normal">{{ $totalLayanan }} Layanan Terdaftar</small>
        </div>
        <div class="stat-grid-4">
            <!-- Hadir (H - Hijau) -->
            <div class="stat-pill" style="background-color: #198754;">
                <div class="num">{{ $jml_hadir }}</div>
                <div class="lbl">Hadir (H)</div>
            </div>
            <!-- Maintenance (X - Biru) -->
            <div class="stat-pill" style="background-color: #0d6efd;">
                <div class="num">{{ $jml_maintenance }}</div>
                <div class="lbl">Maint. (X)</div>
            </div>
            <!-- Libur (L - Oren) -->
            <div class="stat-pill" style="background-color: #fd7e14;">
                <div class="num">{{ $jml_libur }}</div>
                <div class="lbl">Libur (L)</div>
            </div>
            <!-- Belum Absen (TK - Merah) -->
            <div class="stat-pill" style="background-color: #dc3545;">
                <div class="num">{{ $jml_belum_absen }}</div>
                <div class="lbl">Belum (TK)</div>
            </div>
        </div>
    </div>

    <!-- Card 2: Ringkasan Penerimaan PKB -->
    <div class="dash-card">
        <div class="dash-card-title">
            <span><ion-icon name="wallet-outline" style="color:#0284c7; vertical-align:-2px;"></ion-icon> Penerimaan Pajak (PKB)</span>
            <a href="/monitoring/penerimaan" class="text-primary text-decoration-none" style="font-size: 12px; font-weight: 600;">Lihat Detail &rarr;</a>
        </div>
        <div class="row g-2">
            <div class="col-6">
                <div class="penerimaan-hero">
                    <small style="opacity: 0.85; font-size: 11px;">Hari Ini</small>
                    <div style="font-size: 15px; font-weight: 800; margin: 2px 0;">
                        Rp {{ number_format($penerimaanHariIni, 0, ',', '.') }}
                    </div>
                    <small style="opacity: 0.9; font-size: 11px;">
                        <ion-icon name="car-outline" style="vertical-align:-1px;"></ion-icon> {{ $objekHariIni }} Objek
                    </small>
                </div>
            </div>
            <div class="col-6">
                <div class="penerimaan-hero" style="background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%); box-shadow: 0 4px 12px rgba(67, 56, 202, 0.25);">
                    <small style="opacity: 0.85; font-size: 11px;">Bulan Ini</small>
                    <div style="font-size: 15px; font-weight: 800; margin: 2px 0;">
                        Rp {{ number_format($penerimaanBulanIni, 0, ',', '.') }}
                    </div>
                    <small style="opacity: 0.9; font-size: 11px;">
                        <ion-icon name="car-outline" style="vertical-align:-1px;"></ion-icon> {{ $objekBulanIni }} Objek
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: GRAFIK PENERIMAAN (Sama Seperti di Grafik Dashboard Admin) -->
    <div class="dash-card">
        <div class="dash-card-title">
            <span><ion-icon name="bar-chart-outline" style="color:#6366f1; vertical-align:-2px;"></ion-icon> Grafik Tren Penerimaan ({{ date('Y') }})</span>
            <span class="badge bg-light text-primary border" style="font-size: 11px;">Tahunan</span>
        </div>
        <div style="font-size: 11px; color: #64748b; margin-bottom: 8px;">
            Tren pergerakan pendapatan PKB &amp; SWDKLLJ seluruh layanan keliling tahun {{ date('Y') }}.
        </div>
        <!-- ApexCharts Canvas -->
        <div id="chart-tren-mobile" style="min-height: 280px; width: 100%;"></div>
    </div>

    <!-- Card 4: Status Real-Time Layanan Hari Ini -->
    <div class="dash-card mb-5">
        <div class="dash-card-title">
            <span><ion-icon name="navigate-circle-outline" style="color:#10b981; vertical-align:-2px;"></ion-icon> Posko Layanan Hari Ini</span>
            <span class="badge bg-primary text-white" style="font-size: 11px;">Hari {{ $namahari }}</span>
        </div>
        @forelse ($monitoringLayanan as $row)
            @php
                $st = strtolower($row->status_presensi ?? '');
                if ($st == 'h' || $st == 'tl' || (!empty($row->jam_in) && $st != 'm' && $st != 'x' && $st != 'l')) {
                    $badgeBg = '#198754';
                    $badgeText = 'Hadir';
                } elseif ($st == 'm' || $st == 'x') {
                    $badgeBg = '#0d6efd';
                    $badgeText = 'Maint.';
                } elseif ($st == 'l') {
                    $badgeBg = '#fd7e14';
                    $badgeText = 'Libur';
                } else {
                    $badgeBg = '#dc3545';
                    $badgeText = 'Belum';
                }
            @endphp
            <div class="live-service-card">
                @if (!empty($row->foto))
                    <img src="{{ Storage::url('uploads/karyawan/' . $row->foto) }}"
                        style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 1.5px solid #e2e8f0;"
                        onerror="this.src='{{ asset('assets/img/avatar_layanan.png') }}'">
                @else
                    <img src="{{ asset('assets/img/avatar_layanan.png') }}"
                        style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover; background: #fff; border: 1.5px solid #e2e8f0;">
                @endif
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 700; font-size: 13.5px; color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $row->nama_layanan }}
                    </div>
                    <div style="font-size: 11px; color: #64748b; margin-top: 1px;">
                        <ion-icon name="location-outline" style="vertical-align:-1px; color:#2563eb;"></ion-icon> {{ $row->lokasi }}
                    </div>
                    <div style="font-size: 11px; color: #0369a1; font-weight: 600; margin-top: 2px;">
                        Rp {{ number_format($row->penerimaan_hari_ini ?: 0, 0, ',', '.') }}
                        <span style="font-weight: normal; color: #64748b;">({{ (int)($row->unit_hari_ini ?: 0) }} unit)</span>
                    </div>
                </div>
                <div style="text-align: right; flex-shrink: 0;">
                    <span class="badge" style="background-color: {{ $badgeBg }}; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                        {{ $badgeText }}
                    </span>
                    <div style="font-size: 11px; color: #64748b; font-family: monospace; margin-top: 4px;">
                        {{ $row->jam_in ? substr($row->jam_in, 0, 5) : '--:--' }}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted small">
                Belum ada data layanan terdaftar.
            </div>
        @endforelse
    </div>
@endsection

@push('myscript')
    <!-- ApexCharts Script (Persis sama dengan Dashboard Admin) -->
    <script src="{{ asset('tabler/dist/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function formatRupiah(val) {
                if (val >= 1000000000) {
                    return 'Rp ' + (val / 1000000000).toFixed(1) + ' M';
                } else if (val >= 1000000) {
                    return 'Rp ' + (val / 1000000).toFixed(1) + ' Jt';
                } else {
                    return 'Rp ' + Number(val).toLocaleString('id-ID');
                }
            }

            var optionsMobile = {
                series: [{
                    name: 'Total Penerimaan',
                    type: 'area',
                    data: @json($chartBulanValuesTotal)
                }, {
                    name: 'Pokok R2 (Motor)',
                    type: 'line',
                    data: @json($chartBulanValuesR2)
                }, {
                    name: 'Pokok R4 (Mobil)',
                    type: 'line',
                    data: @json($chartBulanValuesR4)
                }],
                chart: {
                    type: 'area',
                    height: 290,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    parentHeightOffset: 0
                },
                colors: ['#2563eb', '#10b981', '#f59e0b'],
                fill: {
                    type: ['gradient', 'solid', 'solid'],
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 95, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: [3, 2, 2]
                },
                labels: @json($chartBulanLabels),
                xaxis: {
                    labels: { style: { fontSize: '11px' } }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return formatRupiah(val);
                        },
                        style: { fontSize: '10px' }
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return 'Rp ' + Number(val).toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '11px',
                    horizontalAlign: 'center',
                    offsetY: 4
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 3
                }
            };

            var chartEl = document.querySelector("#chart-tren-mobile");
            if (chartEl) {
                var chart = new ApexCharts(chartEl, optionsMobile);
                chart.render();
            }
        });
    </script>
@endpush
