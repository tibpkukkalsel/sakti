@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Home
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

        <!-- ========================================== -->
        <!-- 1. PRESENSI LAYANAN -->
        <!-- ========================================== -->
        <div class="d-flex align-items-center mb-3">
            <i class="bi bi-person-badge text-primary fs-2 me-2"></i>
            <h3 class="mb-0 fw-bold text-dark">1. Presensi Layanan</h3>
        </div>

        <div class="row row-deck row-cards mb-4">
            <!-- Dashboard Presensi -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm">
                    <div class="card-status-top bg-primary"></div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="avatar bg-primary-lt rounded-circle mx-auto mb-3" style="width: 56px; height: 56px;">
                                <i class="bi bi-speedometer2 fs-2 text-primary"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Dashboard</h3>
                            <div class="d-flex justify-content-center gap-1 mb-3">
                                <span class="badge bg-success-lt">{{ $jmlHadirHariIni }} Hadir</span>
                                <span class="badge bg-warning-lt">{{ $jmlMaintenanceHariIni }} Maintenance</span>
                                <span class="badge bg-danger-lt">{{ $jmlBelumAbsen }} Belum Absen</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/dashboardadmin" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Buka Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- Jadwal Layanan -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm">
                    <div class="card-status-top bg-info"></div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="avatar bg-info-lt rounded-circle mx-auto mb-3" style="width: 56px; height: 56px;">
                                <i class="bi bi-calendar2-week fs-2 text-info"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Jadwal Layanan</h3>
                            <div class="mb-3">
                                <span class="badge bg-info-lt">{{ $totalJadwalHariIni }} Lokasi Aktif Hari Ini</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/presensi/jadwal-layanan" class="btn btn-outline-primary w-100">
                            <i class="bi bi-calendar-event me-1"></i> Buka Jadwal Layanan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Monitoring Presensi -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm">
                    <div class="card-status-top bg-azure"></div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="avatar bg-azure-lt rounded-circle mx-auto mb-3" style="width: 56px; height: 56px;">
                                <i class="bi bi-display fs-2 text-azure"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Monitoring Presensi</h3>
                            <div class="mb-3">
                                <span class="badge bg-azure-lt">Real-Time Presensi</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/presensi/monitoring" class="btn btn-outline-primary w-100">
                            <i class="bi bi-display me-1"></i> Buka Monitoring Presensi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 2. PENERIMAAN PAJAK KENDARAAN BERMOTOR -->
        <!-- ========================================== -->
        <div class="d-flex align-items-center mb-3">
            <i class="bi bi-cash-stack text-success fs-2 me-2"></i>
            <h3 class="mb-0 fw-bold text-dark">2. Penerimaan Pajak Kendaraan Bermotor</h3>
        </div>

        <div class="row row-deck row-cards mb-4">
            <!-- Dashboard Penerimaan -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm">
                    <div class="card-status-top bg-success"></div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="avatar bg-success-lt rounded-circle mx-auto mb-3" style="width: 56px; height: 56px;">
                                <i class="bi bi-graph-up-arrow fs-2 text-success"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Dashboard</h3>
                            <div class="mb-3">
                                <span class="badge bg-success-lt">Rp {{ number_format($penerimaanBulanIni->total_rp ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/dashboard-penerimaan" class="btn btn-success w-100">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Buka Dashboard PKB
                        </a>
                    </div>
                </div>
            </div>

            <!-- Rekap Layanan -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm">
                    <div class="card-status-top bg-teal"></div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="avatar bg-teal-lt rounded-circle mx-auto mb-3" style="width: 56px; height: 56px;">
                                <i class="bi bi-layers-fill fs-2 text-teal"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Rekap Layanan</h3>
                            <div class="mb-3">
                                <span class="badge bg-teal-lt">Filter &amp; Data Per Layanan</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/penerimaan/rekap-layanan" class="btn btn-outline-success w-100">
                            <i class="bi bi-table me-1"></i> Buka Rekap Layanan
                        </a>
                    </div>
                </div>
            </div>

            <!-- Rekap Lokasi -->
            <div class="col-md-4 col-sm-6 col-12">
                <div class="card card-stacked shadow-sm">
                    <div class="card-status-top bg-emerald"></div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="avatar bg-green-lt rounded-circle mx-auto mb-3" style="width: 56px; height: 56px;">
                                <i class="bi bi-geo-alt-fill fs-2 text-green"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2">Rekap Lokasi</h3>
                            <div class="mb-3">
                                <span class="badge bg-green-lt">Data &amp; Grafik Per Lokasi</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/penerimaan/rekap-lokasi" class="btn btn-outline-success w-100">
                            <i class="bi bi-geo me-1"></i> Buka Rekap Lokasi
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
