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

    <!-- Summary Cards -->
    <div class="row row-deck row-cards mb-3">
        <!-- Card 1: Jumlah Akun Layanan -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layers-intersect" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M8 4m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>
                                    <path d="M4 8m0 2a2 2 0 0 1 2 -2h8a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-8a2 2 0 0 1 -2 -2z"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Jumlah Layanan</div>
                            <div class="text-muted fs-3 font-weight-bold text-dark">{{ $totalLayanan }} Akun</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Layanan Aktif Hari Ini -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                    <path d="M9 12l2 2l4 -4"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Layanan Aktif Hari Ini</div>
                            <div class="text-success fs-3 font-weight-bold">{{ $jmlLayananAktif }} Layanan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Layanan Sudah Pulang -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2"></path>
                                    <path d="M19 12h2l-9 -9l-9 9h2v7a2 2 0 0 0 2 2h5.5"></path>
                                    <path d="M15 19l2 2l4 -4"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Sudah Absen Pulang</div>
                            <div class="text-info fs-3 font-weight-bold">{{ $jmlLayananPulang }} Layanan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Belum Absen -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-warning text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock-hour-4" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                    <path d="M12 12l3 2"></path>
                                    <path d="M12 7v5"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium">Belum Presensi</div>
                            <div class="text-warning fs-3 font-weight-bold">{{ $jmlBelumAbsen }} Layanan</div>
                        </div>
                    </div>
                </div>
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
                                                @if (!empty($row->nama_pic))
                                                    <div class="text-muted small">PIC: {{ $row->nama_pic }}</div>
                                                @endif
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
