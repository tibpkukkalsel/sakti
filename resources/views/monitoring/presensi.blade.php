@extends('layouts.monitoring')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/monitoring/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Monitoring Presensi</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <style>
        .presensi-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .stat-badge-h { background-color: #198754; color: #fff; }
        .stat-badge-x { background-color: #0d6efd; color: #fff; }
        .stat-badge-l { background-color: #fd7e14; color: #fff; }
        .stat-badge-tk { background-color: #dc3545; color: #fff; }

        .thumb-selfie {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #cbd5e1;
            cursor: pointer;
        }
    </style>

    <div class="section" style="padding-top: 70px; padding-bottom: 75px;">
        <!-- Filter Tanggal Presensi -->
        <div class="card mb-3" style="border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div class="card-body p-2">
                <form action="/monitoring/presensi" method="GET" class="d-flex align-items-center gap-2">
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control"
                        style="border-radius: 10px; font-size: 13.5px; height: 38px; border: 1px solid #cbd5e1;">
                    <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 10px; padding: 6px 16px; font-weight: 600;">
                        Tampilkan
                    </button>
                </form>
            </div>
        </div>

        <!-- Legend Rekap Kehadiran -->
        <div class="card mb-3" style="border-radius: 14px; border: 1px solid #e2e8f0;">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between text-center">
                    <div style="flex: 1;">
                        <span class="badge stat-badge-h w-100 py-1" style="font-size: 11px;">Hadir: {{ $statHadir }}</span>
                    </div>
                    <div style="flex: 1; margin: 0 4px;">
                        <span class="badge stat-badge-x w-100 py-1" style="font-size: 11px;">Maint.: {{ $statMaintenance }}</span>
                    </div>
                    <div style="flex: 1; margin-right: 4px;">
                        <span class="badge stat-badge-l w-100 py-1" style="font-size: 11px;">Libur: {{ $statLibur }}</span>
                    </div>
                    <div style="flex: 1;">
                        <span class="badge stat-badge-tk w-100 py-1" style="font-size: 11px;">TK: {{ $statTK }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Presensi Per Layanan -->
        @forelse ($presensiList as $item)
            @php
                $st = strtolower($item->status_presensi ?? '');
                if ($st == 'h' || $st == 'tl' || (!empty($item->jam_in) && $st != 'm' && $st != 'x' && $st != 'l')) {
                    $badgeClass = 'stat-badge-h';
                    $statusLabel = 'H (Hadir)';
                } elseif ($st == 'm' || $st == 'x') {
                    $badgeClass = 'stat-badge-x';
                    $statusLabel = 'X (Maintenance)';
                } elseif ($st == 'l') {
                    $badgeClass = 'stat-badge-l';
                    $statusLabel = 'L (Libur)';
                } else {
                    $badgeClass = 'stat-badge-tk';
                    $statusLabel = 'TK (Belum Absen)';
                }

                $fotoInUrl = !empty($item->foto_in) ? Storage::url('uploads/absensi/' . $item->foto_in) : null;
                $fotoOutUrl = !empty($item->foto_out) ? Storage::url('uploads/absensi/' . $item->foto_out) : null;
            @endphp
            <div class="presensi-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        @if (!empty($item->foto_avatar))
                            <img src="{{ Storage::url('uploads/karyawan/' . $item->foto_avatar) }}"
                                style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; border: 1px solid #e2e8f0;"
                                onerror="this.src='{{ asset('assets/img/avatar_layanan.png') }}'">
                        @else
                            <img src="{{ asset('assets/img/avatar_layanan.png') }}"
                                style="width: 38px; height: 38px; border-radius: 50%; object-fit: cover; background:#fff; border: 1px solid #e2e8f0;">
                        @endif
                        <div>
                            <strong class="text-dark" style="font-size: 14px;">{{ $item->nama_layanan }}</strong>
                            <div class="text-muted small">
                                <ion-icon name="location-outline" style="vertical-align:-1px; color:#2563eb;"></ion-icon> {{ $item->lokasi_tugas }}
                            </div>
                        </div>
                    </div>
                    <span class="badge {{ $badgeClass }} px-2 py-1" style="font-size: 11px;">
                        {{ $statusLabel }}
                    </span>
                </div>

                <div class="row g-2 mt-2 pt-2 border-top">
                    <!-- Masuk -->
                    <div class="col-6">
                        <div style="background: #f8fafc; border-radius: 10px; padding: 8px; border: 1px solid #e2e8f0;">
                            <small class="text-muted d-block" style="font-size: 10.5px;">Jam Masuk</small>
                            <div style="font-weight: 700; font-size: 14px; color: {{ $item->jam_in ? '#10b981' : '#94a3b8' }};">
                                {{ $item->jam_in ? substr($item->jam_in, 0, 5) : '--:--' }}
                            </div>
                            @if ($fotoInUrl)
                                <div class="mt-2">
                                    <img src="{{ $fotoInUrl }}" class="thumb-selfie" alt="Foto Masuk"
                                        onclick="showImageModal('{{ $fotoInUrl }}', 'Foto Masuk: {{ $item->nama_layanan }}')">
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Pulang -->
                    <div class="col-6">
                        <div style="background: #f8fafc; border-radius: 10px; padding: 8px; border: 1px solid #e2e8f0;">
                            <small class="text-muted d-block" style="font-size: 10.5px;">Jam Pulang</small>
                            <div style="font-weight: 700; font-size: 14px; color: {{ $item->jam_out ? '#0284c7' : '#94a3b8' }};">
                                {{ $item->jam_out ? substr($item->jam_out, 0, 5) : '--:--' }}
                            </div>
                            @if ($fotoOutUrl)
                                <div class="mt-2">
                                    <img src="{{ $fotoOutUrl }}" class="thumb-selfie" alt="Foto Pulang"
                                        onclick="showImageModal('{{ $fotoOutUrl }}', 'Foto Pulang: {{ $item->nama_layanan }}')">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted small">
                Tidak ada data presensi pada tanggal ini.
            </div>
        @endforelse
    </div>

    <!-- Modal Lightbox Preview Foto Presensi -->
    <div class="modal fade" id="modalFotoPreview" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 16px; overflow: hidden; border: none;">
                <div class="modal-header bg-primary text-white py-2 px-3">
                    <h6 class="modal-title" id="modalFotoTitle" style="font-size: 13.5px;">Foto Presensi</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 text-center bg-dark">
                    <img src="" id="modalFotoImg" style="width: 100%; max-height: 70vh; object-fit: contain;">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        function showImageModal(src, title) {
            document.getElementById('modalFotoImg').src = src;
            document.getElementById('modalFotoTitle').innerText = title;
            var modal = new bootstrap.Modal(document.getElementById('modalFotoPreview'));
            modal.show();
        }
    </script>
@endpush
