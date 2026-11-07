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
        .section, .card, .btn, .period-btn, .presensi-card, input, select, button, label, span, strong, div, small {
            font-family: "Inter", sans-serif;
        }

        .presensi-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .period-btn-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .period-btn {
            border: 1.5px solid #cbd5e1;
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
            padding: 9px 8px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            outline: none;
            line-height: 1.2;
            width: 100%;
        }

        .period-btn:hover {
            background: #f1f5f9;
            color: #1e293b;
            border-color: #94a3b8;
        }

        .period-btn.active {
            background: linear-gradient(135deg, #2A8BF2 0%, #1A73E8 100%);
            border-color: #2A8BF2;
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 3px 10px rgba(42, 139, 242, 0.3);
        }

        .filter-select {
            border-radius: 10px !important;
            font-size: 13px !important;
            height: 42px !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #1e293b !important;
            background-color: #f8fafc !important;
            font-weight: 600 !important;
            padding: 0 12px !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .filter-date-input {
            border-radius: 8px !important;
            font-size: 12.5px !important;
            height: 38px !important;
            border: 1.5px solid #cbd5e1 !important;
            background: #f8fafc !important;
            color: #1e293b !important;
            padding: 0 10px !important;
            width: 100% !important;
            box-sizing: border-box !important;
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
        <!-- Card Filter Periode & Layanan -->
        <div class="card mb-3" style="border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04); background: #ffffff;">
            <div class="card-body p-3">
                <form action="/monitoring/presensi" method="GET" id="formFilterPresensi">
                    <input type="hidden" name="periode" id="input_periode" value="{{ $periode }}">

                    <!-- Label Periode -->
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                        Pilih Periode
                    </div>

                    <!-- 4 Tombol Pilihan Periode (2x2 Grid: Hari Ini, Minggu Ini, Bulan Ini, Tahun Ini) -->
                    <div class="period-btn-grid mb-2">
                        <button type="button" class="period-btn {{ $periode == 'hari_ini' ? 'active' : '' }}" onclick="selectPeriod('hari_ini')">
                            Hari Ini
                        </button>
                        <button type="button" class="period-btn {{ $periode == 'minggu_ini' ? 'active' : '' }}" onclick="selectPeriod('minggu_ini')">
                            Minggu Ini
                        </button>
                        <button type="button" class="period-btn {{ $periode == 'bulan_ini' ? 'active' : '' }}" onclick="selectPeriod('bulan_ini')">
                            Bulan Ini
                        </button>
                        <button type="button" class="period-btn {{ $periode == 'tahun_ini' ? 'active' : '' }}" onclick="selectPeriod('tahun_ini')">
                            Tahun Ini
                        </button>
                    </div>

                    <!-- Tombol Pilihan Custom Periode -->
                    <div class="mb-2">
                        <button type="button" class="period-btn {{ $periode == 'custom' ? 'active' : '' }}" onclick="selectPeriod('custom')">
                            Custom Periode
                        </button>
                    </div>

                    <!-- Input Custom Periode (Dari Tanggal & Sampai Tanggal) -->
                    <div id="customDateContainer" class="mb-2" style="{{ $periode == 'custom' ? 'display: block;' : 'display: none;' }}">
                        <div class="row" style="margin-left: -4px; margin-right: -4px;">
                            <div class="col-6" style="padding-left: 4px; padding-right: 4px;">
                                <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 3px; display: block;">Dari Tanggal</label>
                                <input type="date" name="dari_tanggal" id="dari_tanggal" class="filter-date-input" value="{{ $dari_tanggal }}">
                            </div>
                            <div class="col-6" style="padding-left: 4px; padding-right: 4px;">
                                <label style="font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 3px; display: block;">Sampai Tanggal</label>
                                <input type="date" name="sampai_tanggal" id="sampai_tanggal" class="filter-date-input" value="{{ $sampai_tanggal }}">
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown Layanan -->
                    <div class="mb-3">
                        <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; display: block;">
                            Layanan
                        </label>
                        <select name="layanan_id" class="filter-select">
                            <option value="all">Semua Layanan</option>
                            @foreach ($layananList as $l)
                                <option value="{{ $l->id }}" {{ $layanan_id == $l->id ? 'selected' : '' }}>
                                    {{ $l->nama_layanan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tombol Filter Data (Tanpa Icon Sesuai Arahan) -->
                    <button type="submit" class="btn btn-primary w-100 font-weight-bold" style="border-radius: 10px; background: linear-gradient(135deg, #2A8BF2 0%, #1A73E8 100%); border: none; height: 42px; font-size: 14px; box-shadow: 0 4px 12px rgba(42, 139, 242, 0.3);">
                        Filter Data
                    </button>
                </form>
            </div>
        </div>

        <!-- Legend Rekap Kehadiran (Default White Card Sesuai Standar Sistem) -->
        <div class="card mb-3" style="border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,0.03); background: #ffffff;">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between text-center">
                    <div style="flex: 1;">
                        <span class="badge stat-badge-h w-100 py-1" style="font-size: 11px; border-radius: 6px;">Hadir: {{ $statHadir }}</span>
                    </div>
                    <div style="flex: 1; margin: 0 4px;">
                        <span class="badge stat-badge-x w-100 py-1" style="font-size: 11px; border-radius: 6px;">Maint.: {{ $statMaintenance }}</span>
                    </div>
                    <div style="flex: 1; margin-right: 4px;">
                        <span class="badge stat-badge-l w-100 py-1" style="font-size: 11px; border-radius: 6px;">Libur: {{ $statLibur }}</span>
                    </div>
                    <div style="flex: 1;">
                        <span class="badge stat-badge-tk w-100 py-1" style="font-size: 11px; border-radius: 6px;">TK: {{ $statTK }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Header Daftar Presensi -->
        <div class="mb-2 d-flex justify-content-between align-items-center">
            <span class="text-muted small fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                Daftar Presensi ({{ $labelPeriode }})
            </span>
            <span class="badge" style="background: #e2e8f0; color: #475569; font-size: 10.5px; font-weight: 600; padding: 3px 8px; border-radius: 6px;">
                {{ $presensiList->total() }} Data
            </span>
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
                    <div class="text-end">
                        <span class="badge {{ $badgeClass }} px-2 py-1 mb-1 d-inline-block" style="font-size: 11px; border-radius: 6px;">
                            {{ $statusLabel }}
                        </span>
                        @if (!empty($item->tgl_presensi))
                            <div style="font-size: 11px; color: #64748b; font-weight: 600;">
                                {{ date('d-m-Y', strtotime($item->tgl_presensi)) }}
                            </div>
                        @endif
                    </div>
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
            <div class="card text-center py-5" style="border-radius: 14px; border: 1px dashed #cbd5e1; background: #ffffff;">
                <div class="card-body">
                    <ion-icon name="calendar-outline" style="font-size: 40px; color: #94a3b8;"></ion-icon>
                    <p class="text-muted mt-2 mb-0" style="font-size: 13px;">Tidak ada data presensi pada periode ini.</p>
                </div>
            </div>
        @endforelse

        <!-- Navigasi Pagination 10 Data Per Halaman -->
        @if ($presensiList->hasPages())
            <div class="card p-2 mt-3 mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.03); background: #ffffff;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        @if ($presensiList->onFirstPage())
                            <button class="btn btn-sm" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; color: #94a3b8; font-size: 12px; font-weight: 600; padding: 6px 14px;" disabled>
                                Kembali
                            </button>
                        @else
                            <a href="{{ $presensiList->previousPageUrl() }}" class="btn btn-sm" style="border-radius: 8px; border: 1.5px solid #2A8BF2; background: #ffffff; color: #2A8BF2; font-size: 12px; font-weight: 700; padding: 6px 14px; text-decoration: none;">
                                Kembali
                            </a>
                        @endif
                    </div>

                    <div style="font-size: 11.5px; font-weight: 600; color: #64748b;">
                        Halaman {{ $presensiList->currentPage() }} dari {{ $presensiList->lastPage() }}
                    </div>

                    <div>
                        @if ($presensiList->hasMorePages())
                            <a href="{{ $presensiList->nextPageUrl() }}" class="btn btn-sm btn-primary" style="border-radius: 8px; background: linear-gradient(135deg, #2A8BF2 0%, #1A73E8 100%); border: none; color: #ffffff; font-size: 12px; font-weight: 700; padding: 6px 14px; text-decoration: none; box-shadow: 0 2px 6px rgba(42, 139, 242, 0.3);">
                                Next Page
                            </a>
                        @else
                            <button class="btn btn-sm" style="border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; color: #94a3b8; font-size: 12px; font-weight: 600; padding: 6px 14px;" disabled>
                                Next Page
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
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
        function selectPeriod(period) {
            document.getElementById('input_periode').value = period;

            var buttons = document.querySelectorAll('.period-btn');
            buttons.forEach(function(btn) {
                btn.classList.remove('active');
                if (btn.getAttribute('onclick').indexOf("'" + period + "'") !== -1) {
                    btn.classList.add('active');
                }
            });

            var customContainer = document.getElementById('customDateContainer');
            if (period === 'custom') {
                customContainer.style.display = 'block';
            } else {
                customContainer.style.display = 'none';
            }
        }

        function showImageModal(src, title) {
            document.getElementById('modalFotoImg').src = src;
            document.getElementById('modalFotoTitle').innerText = title;
            var modal = new bootstrap.Modal(document.getElementById('modalFotoPreview'));
            modal.show();
        }
    </script>
@endpush
