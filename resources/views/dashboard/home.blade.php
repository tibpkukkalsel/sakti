@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    UPPD SAMSAT BATU LICIN
                </div>
                <h2 class="page-title">
                    Home - Panel Kontrol Utama
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <span class="badge bg-primary text-white px-3 py-2 fs-6">
                    <i class="bi bi-calendar3 me-1"></i> Hari {{ $namahari }}, {{ date('d F Y', strtotime($hariini)) }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <!-- Banner Selamat Datang -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body py-3 px-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="card-title fw-bold text-dark mb-1">
                            Selamat Datang di SAKTI (Sistem Presensi &amp; Rekap Penerimaan PKB)
                        </h3>
                        <p class="text-muted mb-0 small">
                            Silakan pilih modul menu di bawah untuk mengelola presensi kehadiran layanan atau memantau rekap penerimaan Pajak Kendaraan Bermotor UPPD SAMSAT Batulicin.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                        <span class="badge bg-blue-lt px-3 py-2">
                            <i class="bi bi-layers-fill me-1"></i> {{ $totalLayanan }} Akun Layanan Terdaftar
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- KELOMPOK 1: PRESENSI LAYANAN -->
        <!-- ========================================== -->
        <div class="row mb-2">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-center">
                        <span class="avatar bg-primary text-white me-2" style="width: 32px; height: 32px;">
                            <i class="bi bi-person-badge"></i>
                        </span>
                        <div>
                            <h3 class="mb-0 fw-bold text-dark">1. Presensi Layanan</h3>
                            <small class="text-muted">Modul pengelolaan presensi kehadiran, jadwal lokasi, dan pemantauan real-time layanan</small>
                        </div>
                    </div>
                    <span class="badge bg-primary-lt">Presensi &amp; Operasional</span>
                </div>
            </div>
        </div>

        <div class="row row-deck row-cards mb-4">
            <!-- 1.1 Dashboard Presensi -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm h-100">
                    <div class="card-status-top bg-primary"></div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avatar bg-primary-lt rounded">
                                    <i class="bi bi-speedometer2 fs-3 text-primary"></i>
                                </div>
                                <span class="badge bg-success-lt">{{ $jmlHadirHariIni }} Layanan Hadir</span>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Dashboard Presensi</h3>
                            <p class="text-muted small mb-3">
                                Ringkasan statistik kehadiran layanan hari ini, monitoring presensi hadir, status maintenance unit, dan layanan belum absen.
                            </p>
                        </div>
                        <div>
                            <div class="row g-2 mb-3 text-center small">
                                <div class="col-4">
                                    <div class="border rounded p-1 bg-light">
                                        <div class="text-muted" style="font-size: 11px;">Hadir</div>
                                        <strong class="text-success">{{ $jmlHadirHariIni }}</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-1 bg-light">
                                        <div class="text-muted" style="font-size: 11px;">Maintenance</div>
                                        <strong class="text-warning">{{ $jmlMaintenanceHariIni }}</strong>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-1 bg-light">
                                        <div class="text-muted" style="font-size: 11px;">Belum Absen</div>
                                        <strong class="text-danger">{{ $jmlBelumAbsen }}</strong>
                                    </div>
                                </div>
                            </div>
                            <a href="/panel-adm14/dashboardadmin" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1.2 Jadwal Layanan -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm h-100">
                    <div class="card-status-top bg-info"></div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avatar bg-info-lt rounded">
                                    <i class="bi bi-calendar2-week fs-3 text-info"></i>
                                </div>
                                <span class="badge bg-info-lt">{{ $totalJadwalHariIni }} Jadwal Hari Ini</span>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Jadwal Layanan</h3>
                            <p class="text-muted small mb-3">
                                Matriks dan tabel jadwal penugasan lokasi operasional yang dikelompokkan per layanan per hari, lengkap dengan filter layanan.
                            </p>
                        </div>
                        <div>
                            <div class="p-2 border rounded bg-light mb-3 text-center small">
                                <span class="text-muted">Hari Ini:</span>
                                <strong class="text-dark">Hari {{ $namahari }}</strong>
                                <span class="text-muted">({{ $totalJadwalHariIni }} lokasi aktif)</span>
                            </div>
                            <a href="/panel-adm14/presensi/jadwal-layanan" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-calendar-event me-2"></i> Buka Jadwal Layanan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1.3 Monitoring Presensi -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm h-100">
                    <div class="card-status-top bg-azure"></div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avatar bg-azure-lt rounded">
                                    <i class="bi bi-display fs-3 text-azure"></i>
                                </div>
                                <span class="badge bg-azure-lt">Real-Time</span>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Monitoring Presensi</h3>
                            <p class="text-muted small mb-3">
                                Pemantauan jam presensi masuk, jam pulang, titik koordinat peta geofence, dan verifikasi foto selfie absensi layanan secara langsung.
                            </p>
                        </div>
                        <div>
                            <div class="p-2 border rounded bg-light mb-3 text-center small">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                <span class="text-muted">Dilengkapi Peta Lokasi &amp; Foto Masuk/Pulang</span>
                            </div>
                            <a href="/panel-adm14/presensi/monitoring" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-display me-2"></i> Buka Monitoring Presensi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- KELOMPOK 2: PENERIMAAN PAJAK KENDARAAN BERMOTOR -->
        <!-- ========================================== -->
        <div class="row mb-2">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-center">
                        <span class="avatar bg-success text-white me-2" style="width: 32px; height: 32px;">
                            <i class="bi bi-cash-stack"></i>
                        </span>
                        <div>
                            <h3 class="mb-0 fw-bold text-dark">2. Penerimaan Pajak Kendaraan Bermotor</h3>
                            <small class="text-muted">Modul pelaporan, analisis grafik tren, dan rekapitulasi penerimaan PKB R2 &amp; R4</small>
                        </div>
                    </div>
                    <span class="badge bg-success-lt">Pendapatan &amp; Rekap</span>
                </div>
            </div>
        </div>

        <div class="row row-deck row-cards mb-4">
            <!-- 2.1 Dashboard Penerimaan -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm h-100">
                    <div class="card-status-top bg-success"></div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avatar bg-success-lt rounded">
                                    <i class="bi bi-graph-up-arrow fs-3 text-success"></i>
                                </div>
                                <span class="badge bg-success-lt">Dashboard PKB</span>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Dashboard Penerimaan PKB</h3>
                            <p class="text-muted small mb-3">
                                Visualisasi statistik total penerimaan PKB, rincian perolehan R2 vs R4, grafik tren bulanan, dan ranking lokasi terbanyak.
                            </p>
                        </div>
                        <div>
                            <div class="p-2 border rounded bg-light mb-3 text-center">
                                <div class="text-muted small" style="font-size: 11px;">Penerimaan Bulan Ini:</div>
                                <strong class="text-success fs-5">Rp {{ number_format($penerimaanBulanIni->total_rp ?? 0, 0, ',', '.') }}</strong>
                            </div>
                            <a href="/panel-adm14/dashboard-penerimaan" class="btn btn-success w-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Masuk ke Dashboard PKB
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2.2 Rekap Layanan -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm h-100">
                    <div class="card-status-top bg-teal"></div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avatar bg-teal-lt rounded">
                                    <i class="bi bi-layers-fill fs-3 text-teal"></i>
                                </div>
                                <span class="badge bg-teal-lt">Per Layanan</span>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Rekap Layanan</h3>
                            <p class="text-muted small mb-3">
                                Grafik komparasi dan data tabel rincian realisasi penerimaan PKB per akun unit layanan, dilengkapi filter layanan yang dipilih.
                            </p>
                        </div>
                        <div>
                            <div class="p-2 border rounded bg-light mb-3 text-center small">
                                <i class="bi bi-filter me-1 text-primary"></i>
                                <span class="text-muted">Filter per layanan &amp; komparasi R2 / R4</span>
                            </div>
                            <a href="/panel-adm14/penerimaan/rekap-layanan" class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-table me-2"></i> Buka Rekap Layanan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2.3 Rekap Lokasi -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm h-100">
                    <div class="card-status-top bg-emerald"></div>
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="avatar bg-green-lt rounded">
                                    <i class="bi bi-geo-alt-fill fs-3 text-green"></i>
                                </div>
                                <span class="badge bg-green-lt">Per Lokasi</span>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Rekap Lokasi</h3>
                            <p class="text-muted small mb-3">
                                Grafik dan data tabel rekapitulasi penerimaan PKB per titik lokasi pelayanan (Samsat Induk, Keliling, Corner, dll).
                            </p>
                        </div>
                        <div>
                            <div class="p-2 border rounded bg-light mb-3 text-center small">
                                <i class="bi bi-pin-map-fill me-1 text-danger"></i>
                                <span class="text-muted">Distribusi pendapatan per titik lokasi pelayanan</span>
                            </div>
                            <a href="/panel-adm14/penerimaan/rekap-lokasi" class="btn btn-outline-success w-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-geo me-2"></i> Buka Rekap Lokasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
