@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Maintenance Layanan</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <div style="padding-top: 65px; padding-bottom: 75px;">
        @if (Session::get('success'))
            <div class="alert alert-success mx-3 my-2">
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::get('error'))
            <div class="alert alert-danger mx-3 my-2">
                {{ Session::get('error') }}
            </div>
        @endif

        @if (Session::get('warning'))
            <div class="alert alert-warning mx-3 my-2">
                {{ Session::get('warning') }}
            </div>
        @endif

        <!-- 1. Status Maintenance Hari Ini (Pembatas Biasa) -->
        <div class="bg-white border-bottom px-3 py-3 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-bold text-uppercase">Status Hari Ini ({{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }})</span>
                @if ($maintenanceHariIni)
                    <span class="badge bg-warning text-dark" style="font-size: 11px;">Unit OFF (Maintenance)</span>
                @else
                    <span class="badge bg-success" style="font-size: 11px;">Operasional Normal</span>
                @endif
            </div>

            @if ($maintenanceHariIni)
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <div style="font-size: 17px; font-weight: 700; color: #1e293b;">
                            {{ $maintenanceHariIni->keterangan ?: 'Pemeliharaan / Perbaikan Unit' }}
                        </div>
                        <div class="text-muted small mt-1">
                            <ion-icon name="calendar-outline" style="vertical-align: -1px;"></ion-icon>
                            {{ \Carbon\Carbon::parse($maintenanceHariIni->tgl_izin_dari)->translatedFormat('d F Y') }}
                            @if ($maintenanceHariIni->tgl_izin_dari != $maintenanceHariIni->tgl_izin_sampai)
                                s/d {{ \Carbon\Carbon::parse($maintenanceHariIni->tgl_izin_sampai)->translatedFormat('d F Y') }}
                            @endif
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:;" class="text-secondary d-inline-flex align-items-center justify-content-center" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 32px; height: 32px; text-decoration: none;">
                            <ion-icon name="ellipsis-vertical" style="font-size: 20px;"></ion-icon>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow-sm" style="min-width: 120px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px 0; z-index: 1050;">
                            <form action="/maintenance/{{ $maintenanceHariIni->kode_izin }}/delete" method="POST" onsubmit="return confirm('Hapus/batalkan jadwal maintenance ini?')" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger" style="padding: 8px 16px; font-size: 13px; font-weight: 500; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="/maintenance/create" class="btn btn-primary btn-sm btn-block">
                        <ion-icon name="add-circle-outline"></ion-icon> Input Maintenance Layanan
                    </a>
                </div>
            @else
                <div class="text-muted small mt-1 mb-2">
                    Unit layanan siap beroperasi normal. Ajukan status maintenance jika unit sedang perbaikan atau servis teknis.
                </div>
                <a href="/maintenance/create" class="btn btn-primary btn-sm btn-block">
                    <ion-icon name="add-circle-outline"></ion-icon> Input Maintenance Layanan
                </a>
            @endif
        </div>

        <!-- 2. Header Riwayat -->
        <div class="d-flex justify-content-between align-items-center px-3 mt-3 mb-2">
            <span class="text-muted small fw-bold text-uppercase">Riwayat Status Maintenance</span>
            <span class="text-muted small">{{ $riwayat->total() }} Catatan</span>
        </div>

        <!-- 3. Daftar Riwayat Dengan Pembatas Biasa (Tanpa Card) -->
        <div class="bg-white border-top border-bottom">
            @forelse ($riwayat as $r)
                @php
                    $tglDari = $r->tgl_izin_dari;
                    $tglSampai = $r->tgl_izin_sampai;
                    $jmlHari = \Carbon\Carbon::parse($tglDari)->diffInDays(\Carbon\Carbon::parse($tglSampai)) + 1;

                    if ($hariini >= $tglDari && $hariini <= $tglSampai) {
                        $badgeClass = 'bg-warning text-dark';
                        $statusText = 'Sedang Berjalan';
                    } elseif ($hariini < $tglDari) {
                        $badgeClass = 'bg-primary';
                        $statusText = 'Akan Datang';
                    } else {
                        $badgeClass = 'bg-secondary';
                        $statusText = 'Selesai';
                    }
                @endphp
                <div class="px-3 py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <div class="d-flex align-items-center gap-1 mb-1">
                                <span class="badge {{ $badgeClass }}" style="font-size: 11px;">
                                    {{ $statusText }}
                                </span>
                                <span class="badge bg-light text-muted border" style="font-size: 11px;">
                                    {{ $jmlHari }} Hari
                                </span>
                            </div>
                            <strong class="text-dark" style="font-size: 14px;">
                                {{ \Carbon\Carbon::parse($tglDari)->translatedFormat('d F Y') }}
                                @if ($tglDari != $tglSampai)
                                    - {{ \Carbon\Carbon::parse($tglSampai)->translatedFormat('d F Y') }}
                                @endif
                            </strong>
                        </div>
                        <div class="dropdown">
                            <a href="javascript:;" class="text-secondary d-inline-flex align-items-center justify-content-center" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 32px; height: 32px; text-decoration: none;">
                                <ion-icon name="ellipsis-vertical" style="font-size: 20px;"></ion-icon>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm" style="min-width: 120px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px 0; z-index: 1050;">
                                <form action="/maintenance/{{ $r->kode_izin }}/delete" method="POST" onsubmit="return confirm('Hapus/batalkan jadwal maintenance ini?')" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger" style="padding: 8px 16px; font-size: 13px; font-weight: 500; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="text-secondary small mt-1">
                        <strong class="text-dark">Alasan:</strong> {{ $r->keterangan }}
                    </div>

                    <div class="d-flex justify-content-between text-muted small mt-2 pt-1 border-top" style="font-size: 11px;">
                        <span>Kode: <strong>{{ $r->kode_izin }}</strong></span>
                        <span>Unit: <strong>{{ $user->nama_layanan }}</strong></span>
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-muted small">
                    <ion-icon name="construct-outline" style="font-size: 40px; color: #cbd5e1; display: block; margin: 0 auto 8px;"></ion-icon>
                    Belum ada riwayat maintenance layanan.
                </div>
            @endforelse
        </div>

        @if ($riwayat->hasPages())
            <div class="p-3">
                {{ $riwayat->links('vendor.pagination.bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
