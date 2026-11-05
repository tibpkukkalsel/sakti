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
        <div class="right">
            <a href="/maintenance/create" class="headerButton text-light">
                <ion-icon name="add-circle-outline" style="font-size: 26px;"></ion-icon>
            </a>
        </div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <style>
        body {
            background-color: #f8fafc !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        #appCapsule {
            padding-top: 65px !important;
            padding-bottom: 95px !important;
        }

        .maintenance-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        .maintenance-card.active-now {
            border-left: 5px solid #f59e0b;
        }

        .maintenance-card.upcoming {
            border-left: 5px solid #3b82f6;
        }

        .maintenance-card.finished {
            border-left: 5px solid #94a3b8;
        }
    </style>

    <div class="section pt-1 px-3">
        @if (Session::get('success'))
            <div class="alert alert-success mt-2" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2">
                    <ion-icon name="checkmark-circle-outline" style="font-size: 22px;"></ion-icon>
                    <span>{{ Session::get('success') }}</span>
                </div>
            </div>
        @endif

        @if (Session::get('error'))
            <div class="alert alert-danger mt-2" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2">
                    <ion-icon name="alert-circle-outline" style="font-size: 22px;"></ion-icon>
                    <span>{{ Session::get('error') }}</span>
                </div>
            </div>
        @endif

        @if (Session::get('warning'))
            <div class="alert alert-warning mt-2" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2">
                    <ion-icon name="alert-circle-outline" style="font-size: 22px;"></ion-icon>
                    <span>{{ Session::get('warning') }}</span>
                </div>
            </div>
        @endif

        <!-- Card Status Hari Ini -->
        @if ($maintenanceHariIni)
            <div class="card mb-3" style="border-radius: 16px; border: 1.5px solid #f59e0b; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #fff; box-shadow: 0 6px 18px rgba(217, 119, 6, 0.25);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-white text-warning fw-bold px-2 py-1" style="border-radius: 8px;">
                            <ion-icon name="construct" class="me-1"></ion-icon> STATUS HARI INI: OFF / MAINTENANCE
                        </span>
                        <span style="font-size: 11px; opacity: 0.9;">{{ $user->nama_layanan }}</span>
                    </div>
                    <h4 style="font-size: 15px; font-weight: 700; margin-bottom: 4px; color: #fff;">
                        {{ $maintenanceHariIni->keterangan ?: 'Pemeliharaan / Perbaikan Unit Layanan' }}
                    </h4>
                    <div style="font-size: 12px; opacity: 0.95;">
                        <ion-icon name="calendar-outline" class="me-1"></ion-icon>
                        {{ date('d M Y', strtotime($maintenanceHariIni->tgl_izin_dari)) }} s/d {{ date('d M Y', strtotime($maintenanceHariIni->tgl_izin_sampai)) }}
                    </div>
                </div>
            </div>
        @else
            <div class="card mb-3" style="border-radius: 16px; border: 1px solid #bbf7d0; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; box-shadow: 0 6px 18px rgba(16, 185, 129, 0.2);">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="badge bg-white text-success fw-bold px-2 py-1" style="border-radius: 8px;">
                            <ion-icon name="checkmark-circle" class="me-1"></ion-icon> OPERASIONAL NORMAL (ONLINE)
                        </span>
                        <span style="font-size: 11px; opacity: 0.9;">{{ $user->nama_layanan }}</span>
                    </div>
                    <div style="font-size: 13px; font-weight: 600; margin-top: 6px; color: #fff;">
                        Unit layanan aktif bertugas dan siap melayani presensi hari ini.
                    </div>
                </div>
            </div>
        @endif

        <!-- Tombol Tambah Maintenance -->
        <div class="mb-3">
            <a href="/maintenance/create" class="btn btn-warning btn-block text-dark fw-bold d-flex align-items-center justify-content-center" style="border-radius: 12px; padding: 12px; font-size: 14px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);">
                <ion-icon name="add-circle-outline" style="font-size: 20px; margin-right: 6px;"></ion-icon>
                + Buat Jadwal Maintenance (Unit OFF)
            </a>
        </div>

        <!-- Header Riwayat -->
        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
            <h4 class="mb-0 fw-bold" style="font-size: 14px; color: #334155;">Riwayat Status Maintenance</h4>
            <span class="badge bg-secondary text-white" style="border-radius: 6px; font-size: 11px;">{{ $riwayat->total() }} Catatan</span>
        </div>

        <!-- Daftar Riwayat -->
        @forelse ($riwayat as $r)
            @php
                $tglDari = $r->tgl_izin_dari;
                $tglSampai = $r->tgl_izin_sampai;
                $jmlHari = \Carbon\Carbon::parse($tglDari)->diffInDays(\Carbon\Carbon::parse($tglSampai)) + 1;

                if ($hariini >= $tglDari && $hariini <= $tglSampai) {
                    $statusBadge = 'bg-warning text-dark';
                    $statusLabel = 'Sedang Berjalan (OFF)';
                    $cardClass = 'active-now';
                } elseif ($hariini < $tglDari) {
                    $statusBadge = 'bg-primary text-white';
                    $statusLabel = 'Terjadwal (Akan Datang)';
                    $cardClass = 'upcoming';
                } else {
                    $statusBadge = 'bg-secondary text-white';
                    $statusLabel = 'Selesai';
                    $cardClass = 'finished';
                }
            @endphp
            <div class="maintenance-card {{ $cardClass }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <span class="badge {{ $statusBadge }} px-2 py-1 mb-1" style="border-radius: 6px; font-size: 11px; font-weight: 600;">
                            {{ $statusLabel }}
                        </span>
                        <div style="font-size: 14px; font-weight: 700; color: #1e293b;">
                            {{ date('d/m/Y', strtotime($tglDari)) }}
                            @if ($tglDari != $tglSampai)
                                - {{ date('d/m/Y', strtotime($tglSampai)) }}
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-light text-muted border px-2 py-1" style="font-size: 11px;">
                            {{ $jmlHari }} Hari
                        </span>
                        <form action="/maintenance/{{ $r->kode_izin }}/delete" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan/menghapus jadwal maintenance ini?')" class="m-0 ms-1">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="border-radius: 8px;" title="Batalkan Maintenance">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-secondary" style="font-size: 13px; line-height: 1.4;">
                    <strong class="text-dark">Alasan/Kendala:</strong> {{ $r->keterangan }}
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top" style="font-size: 11px; color: #94a3b8;">
                    <span>Kode: <strong>{{ $r->kode_izin }}</strong></span>
                    <span>Status: Disetujui Otomatis</span>
                </div>
            </div>
        @empty
            <div class="card text-center p-4 border-0 shadow-sm" style="border-radius: 16px; background: #ffffff;">
                <div class="text-muted mb-2">
                    <ion-icon name="construct-outline" style="font-size: 48px; color: #cbd5e1;"></ion-icon>
                </div>
                <h5 class="fw-bold text-dark mb-1" style="font-size: 15px;">Belum Ada Catatan Maintenance</h5>
                <p class="text-muted small mb-0">Semua catatan status pemeliharaan / perbaikan unit layanan akan ditampilkan di sini.</p>
            </div>
        @endforelse

        <div class="mt-3">
            {{ $riwayat->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endsection
