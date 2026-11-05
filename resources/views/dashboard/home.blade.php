@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title fw-bold text-dark">
                    <i class="bi bi-house-door-fill text-primary me-2"></i> Home
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
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-person-badge text-primary me-2"></i> 1. Presensi Layanan
            </h4>
        </div>

        <div class="row row-cards mb-4">
            <!-- 1.1 Dashboard Presensi -->
            <div class="col-md-4 col-sm-6 col-12 mb-3">
                <div class="card card-outline card-primary shadow-sm h-100" style="position: relative; overflow: hidden;">
                    <div style="position: absolute; right: 15px; top: 12px; opacity: 0.12; font-size: 56px; pointer-events: none; line-height: 1;">
                        <i class="bi bi-speedometer2 text-primary"></i>
                    </div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between" style="position: relative; z-index: 2;">
                        <div>
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3" style="width: 58px; height: 58px;">
                                <i class="bi bi-speedometer2 fs-2"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2 d-block">Dashboard</h3>
                            <div class="d-flex justify-content-center gap-1 mb-3 flex-wrap">
                                <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>{{ $jmlHadirHariIni }} Hadir</span>
                                <span class="badge bg-warning text-dark"><i class="bi bi-tools me-1"></i>{{ $jmlMaintenanceHariIni }} Maint.</span>
                                <span class="badge bg-danger"><i class="bi bi-clock-history me-1"></i>{{ $jmlBelumAbsen }} Belum</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/dashboardadmin" class="btn btn-primary w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Buka Dashboard
                        </a>
                    </div>
                </div>
            </div>

            <!-- 1.2 Jadwal Layanan -->
            <div class="col-md-4 col-sm-6 col-12 mb-3">
                <div class="card card-outline card-info shadow-sm h-100" style="position: relative; overflow: hidden;">
                    <div style="position: absolute; right: 15px; top: 12px; opacity: 0.12; font-size: 56px; pointer-events: none; line-height: 1;">
                        <i class="bi bi-calendar2-week text-info"></i>
                    </div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between" style="position: relative; z-index: 2;">
                        <div>
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-info bg-opacity-10 text-info mx-auto mb-3" style="width: 58px; height: 58px;">
                                <i class="bi bi-calendar2-week fs-2"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2 d-block">Jadwal Layanan</h3>
                            <div class="mb-3">
                                <span class="badge bg-info text-dark"><i class="bi bi-geo-alt-fill me-1"></i>{{ $totalJadwalHariIni }} Lokasi Aktif Hari Ini</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/presensi/jadwal-layanan" class="btn btn-outline-primary w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Buka Jadwal Layanan
                        </a>
                    </div>
                </div>
            </div>

            <!-- 1.3 Monitoring Presensi -->
            <div class="col-md-4 col-sm-6 col-12 mb-3">
                <div class="card card-outline card-primary shadow-sm h-100" style="position: relative; overflow: hidden;">
                    <div style="position: absolute; right: 15px; top: 12px; opacity: 0.12; font-size: 56px; pointer-events: none; line-height: 1;">
                        <i class="bi bi-display text-primary"></i>
                    </div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between" style="position: relative; z-index: 2;">
                        <div>
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary mx-auto mb-3" style="width: 58px; height: 58px;">
                                <i class="bi bi-display fs-2"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2 d-block">Monitoring Presensi</h3>
                            <div class="mb-3">
                                <span class="badge bg-primary"><i class="bi bi-broadcast me-1"></i>Real-Time Presensi</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/presensi/monitoring" class="btn btn-outline-primary w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Buka Monitoring Presensi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 2. PENERIMAAN PAJAK KENDARAAN BERMOTOR -->
        <!-- ========================================== -->
        <div class="d-flex align-items-center mb-3 mt-2">
            <h4 class="fw-bold text-dark mb-0">
                <i class="bi bi-cash-stack text-success me-2"></i> 2. Penerimaan Pajak Kendaraan Bermotor
            </h4>
        </div>

        <div class="row row-cards mb-4">
            <!-- 2.1 Dashboard Penerimaan -->
            <div class="col-md-4 col-sm-6 col-12 mb-3">
                <div class="card card-outline card-success shadow-sm h-100" style="position: relative; overflow: hidden;">
                    <div style="position: absolute; right: 15px; top: 12px; opacity: 0.12; font-size: 56px; pointer-events: none; line-height: 1;">
                        <i class="bi bi-graph-up-arrow text-success"></i>
                    </div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between" style="position: relative; z-index: 2;">
                        <div>
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success mx-auto mb-3" style="width: 58px; height: 58px;">
                                <i class="bi bi-graph-up-arrow fs-2"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2 d-block">Dashboard</h3>
                            <div class="mb-3">
                                <span class="badge bg-success"><i class="bi bi-cash-stack me-1"></i>Rp {{ number_format($penerimaanBulanIni->total_rp ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/dashboard-penerimaan" class="btn btn-success w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Buka Dashboard PKB
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2.2 Rekap Layanan -->
            <div class="col-md-4 col-sm-6 col-12 mb-3">
                <div class="card card-outline card-success shadow-sm h-100" style="position: relative; overflow: hidden;">
                    <div style="position: absolute; right: 15px; top: 12px; opacity: 0.12; font-size: 56px; pointer-events: none; line-height: 1;">
                        <i class="bi bi-layers-fill text-success"></i>
                    </div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between" style="position: relative; z-index: 2;">
                        <div>
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success mx-auto mb-3" style="width: 58px; height: 58px;">
                                <i class="bi bi-layers-fill fs-2"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2 d-block">Rekap Layanan</h3>
                            <div class="mb-3">
                                <span class="badge bg-light text-dark border"><i class="bi bi-truck me-1"></i>Rincian Per Akun Layanan</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/penerimaan/rekap-layanan" class="btn btn-outline-success w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Buka Rekap Layanan
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2.3 Rekap Lokasi -->
            <div class="col-md-4 col-sm-6 col-12 mb-3">
                <div class="card card-outline card-success shadow-sm h-100" style="position: relative; overflow: hidden;">
                    <div style="position: absolute; right: 15px; top: 12px; opacity: 0.12; font-size: 56px; pointer-events: none; line-height: 1;">
                        <i class="bi bi-geo-alt-fill text-success"></i>
                    </div>
                    <div class="card-body text-center p-4 d-flex flex-column justify-content-between" style="position: relative; z-index: 2;">
                        <div>
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success mx-auto mb-3" style="width: 58px; height: 58px;">
                                <i class="bi bi-geo-alt-fill fs-2"></i>
                            </div>
                            <h3 class="card-title fw-bold text-dark mb-2 d-block">Rekap Lokasi</h3>
                            <div class="mb-3">
                                <span class="badge bg-light text-dark border"><i class="bi bi-pin-map-fill me-1"></i>Rincian Per Titik Lokasi</span>
                            </div>
                        </div>
                        <a href="/panel-adm14/penerimaan/rekap-lokasi" class="btn btn-outline-success w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i> Buka Rekap Lokasi
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
