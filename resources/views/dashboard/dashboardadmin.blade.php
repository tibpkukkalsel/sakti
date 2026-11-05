@extends('layouts.admin.tabler')

@section('content')
@php
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][date('w')];
    $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][date('n')];
    $tanggal_indo = $hari . ', ' . date('j') . ' ' . $bulan . ' ' . date('Y');
@endphp

    <!-- Page Header & Dashboard Switcher -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-7">
            <h3 class="m-0 fw-bold text-dark">Dashboard Presensi Layanan</h3>
            <small class="text-muted">Monitoring Status Kehadiran &amp; Posko Layanan UPPD SAMSAT Batu Licin &bull; {{ $tanggal_indo }}</small>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0">
            <div class="btn-group" role="group">
                <a href="/panel-adm14/dashboardadmin" class="btn btn-primary active">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-check" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4"></path>
                        <path d="M15 19l2 2l4 -4"></path>
                    </svg>
                    Presensi Layanan
                </a>
                <a href="/panel-adm14/dashboard-penerimaan" class="btn btn-outline-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path>
                        <path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path>
                        <path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path>
                        <path d="M4 20l14 0"></path>
                    </svg>
                    Penerimaan PKB
                </a>
            </div>
        </div>
    </div>

    <!-- Small boxes (Stat box) - AdminLTE Style -->
    <div class="row mb-4">
        <!-- Box 1: Jumlah Layanan (Blue) -->
        <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-0">
            <div class="small-box text-bg-primary shadow-sm h-100 d-flex flex-column justify-content-between" style="border-radius: 12px; overflow: hidden; position: relative;">
                <div class="inner p-3" style="position: relative; z-index: 2;">
                    <h3 class="fw-bold mb-1 text-white" style="font-size: 2.3rem; line-height: 1.1;">{{ $totalLayanan }}</h3>
                    <p class="mb-0 fs-6 text-white text-opacity-90 fw-medium">Jumlah Layanan</p>
                </div>
                <div class="small-box-icon" style="position: absolute; right: 15px; top: 12px; z-index: 1; opacity: 0.25; font-size: 72px; line-height: 1; pointer-events: none; color: #fff;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <a href="/panel-adm14/layanan" class="small-box-footer d-flex align-items-center justify-content-center text-white text-decoration-none py-2" style="background-color: rgba(0, 0, 0, 0.15); font-size: 13.5px; font-weight: 500; transition: background-color 0.2s;">
                    Lihat Data Layanan <i class="bi bi-arrow-right-circle ms-1" style="font-size: 15px;"></i>
                </a>
            </div>
        </div>

        <!-- Box 2: Layanan Hadir (Green) -->
        <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-0">
            <div class="small-box text-bg-success shadow-sm h-100 d-flex flex-column justify-content-between" style="border-radius: 12px; overflow: hidden; position: relative;">
                <div class="inner p-3" style="position: relative; z-index: 2;">
                    <h3 class="fw-bold mb-1 text-white" style="font-size: 2.3rem; line-height: 1.1;">{{ $jmlLayananAktif }}</h3>
                    <p class="mb-0 fs-6 text-white text-opacity-90 fw-medium">Layanan Hadir</p>
                </div>
                <div class="small-box-icon" style="position: absolute; right: 15px; top: 12px; z-index: 1; opacity: 0.25; font-size: 72px; line-height: 1; pointer-events: none; color: #fff;">
                    <i class="bi bi-fingerprint"></i>
                </div>
                <a href="/panel-adm14/presensi/monitoring" class="small-box-footer d-flex align-items-center justify-content-center text-white text-decoration-none py-2" style="background-color: rgba(0, 0, 0, 0.15); font-size: 13.5px; font-weight: 500; transition: background-color 0.2s;">
                    Monitoring Presensi <i class="bi bi-arrow-right-circle ms-1" style="font-size: 15px;"></i>
                </a>
            </div>
        </div>

        <!-- Box 3: Layanan Maintenance (Yellow) -->
        <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-0">
            <div class="small-box text-bg-warning shadow-sm h-100 d-flex flex-column justify-content-between" style="border-radius: 12px; overflow: hidden; position: relative; color: #1e293b;">
                <div class="inner p-3" style="position: relative; z-index: 2;">
                    <h3 class="fw-bold mb-1" style="font-size: 2.3rem; line-height: 1.1; color: #1e293b;">{{ $jmlMaintenance }}</h3>
                    <p class="mb-0 fs-6 fw-medium" style="color: #1e293b;">Layanan Maintenance</p>
                </div>
                <div class="small-box-icon" style="position: absolute; right: 15px; top: 12px; z-index: 1; opacity: 0.20; font-size: 72px; line-height: 1; pointer-events: none; color: #000;">
                    <i class="bi bi-tools"></i>
                </div>
                <a href="/panel-adm14/presensi/maintenance" class="small-box-footer d-flex align-items-center justify-content-center text-decoration-none py-2" style="background-color: rgba(0, 0, 0, 0.12); font-size: 13.5px; font-weight: 500; color: #1e293b; transition: background-color 0.2s;">
                    Data Maintenance <i class="bi bi-arrow-right-circle ms-1" style="font-size: 15px;"></i>
                </a>
            </div>
        </div>

        <!-- Box 4: Belum Absen (Red) -->
        <div class="col-xl-3 col-sm-6 col-12 mb-3 mb-xl-0">
            <div class="small-box text-bg-danger shadow-sm h-100 d-flex flex-column justify-content-between" style="border-radius: 12px; overflow: hidden; position: relative;">
                <div class="inner p-3" style="position: relative; z-index: 2;">
                    <h3 class="fw-bold mb-1 text-white" style="font-size: 2.3rem; line-height: 1.1;">{{ $jmlBelumAbsen }}</h3>
                    <p class="mb-0 fs-6 text-white text-opacity-90 fw-medium">Belum Absen</p>
                </div>
                <div class="small-box-icon" style="position: absolute; right: 15px; top: 12px; z-index: 1; opacity: 0.25; font-size: 72px; line-height: 1; pointer-events: none; color: #fff;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <a href="/panel-adm14/presensi/monitoring" class="small-box-footer d-flex align-items-center justify-content-center text-white text-decoration-none py-2" style="background-color: rgba(0, 0, 0, 0.15); font-size: 13.5px; font-weight: 500; transition: background-color 0.2s;">
                    Monitoring Presensi <i class="bi bi-arrow-right-circle ms-1" style="font-size: 15px;"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Monitoring Table (Tanpa Button Aksi) -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-0">Status Kehadiran &amp; Lokasi Layanan Hari Ini</h4>
                        <small class="text-muted">Data real-time presensi per akun layanan beserta lokasi penugasan hari ini.</small>
                    </div>
                    <span class="badge bg-primary text-white">Hari {{ $namahari }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Akun Layanan</th>
                                    <th>Lokasi Pelayanan</th>
                                    <th class="text-center" style="width: 140px;">Jam Masuk</th>
                                    <th class="text-center" style="width: 140px;">Jam Keluar</th>
                                    <th class="text-center" style="width: 150px;">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($monitoringLayanan as $row)
                                <tr>
                                    <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if (!empty($row->foto))
                                                <img src="{{ Storage::url('uploads/karyawan/' . $row->foto) }}" class="rounded-circle me-2" style="width: 38px; height: 38px; object-fit: cover;" alt="">
                                            @else
                                                <span class="avatar rounded-circle me-2 bg-primary text-white fw-bold">{{ strtoupper(substr($row->nama_layanan, 0, 2)) }}</span>
                                            @endif
                                            <div>
                                                <strong class="text-dark fs-6">{{ $row->nama_layanan }}</strong>
                                                
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin text-primary me-2 flex-shrink-0" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path>
                                                <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                                            </svg>
                                            <span class="fw-semibold text-dark">{{ $row->lokasi }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if (!empty($row->jam_in))
                                            <span class="badge bg-success text-white font-monospace fs-6 px-3 py-2">
                                                {{ $row->jam_in }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border">
                                                -- : -- : --
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if (!empty($row->jam_out))
                                            <span class="badge bg-primary text-white font-monospace fs-6 px-3 py-2">
                                                {{ $row->jam_out }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-muted border">
                                                -- : -- : --
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $row->status_badge }} px-3 py-2 font-weight-bold">
                                            {{ $row->status_text }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada akun layanan yang terdaftar di sistem.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
