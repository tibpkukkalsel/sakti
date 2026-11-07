@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Rekap Penerimaan PKB</div>
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

        @if (Session::get('warning'))
            <div class="alert alert-warning mx-3 my-2">
                {{ Session::get('warning') }}
            </div>
        @endif

        <!-- 1. Status Penerimaan Hari Ini (Pembatas Biasa) -->
        <div class="bg-white border-bottom px-3 py-3 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-bold text-uppercase">Penerimaan Hari Ini</span>
                @if ($penerimaanHariIni)
                    <span class="badge bg-success" style="font-size: 11px;">Sudah Input</span>
                @else
                    <span class="badge bg-warning text-dark" style="font-size: 11px;">Belum Input</span>
                @endif
            </div>

            @if ($penerimaanHariIni)
                <div class="d-flex justify-content-between align-items-center mt-1">
                    <div>
                        <div style="font-size: 20px; font-weight: 800; color: #10b981;">
                            Rp {{ number_format($penerimaanHariIni->total_rp, 0, ',', '.') }}
                        </div>
                        <div class="text-muted small">
                            {{ \Carbon\Carbon::parse($penerimaanHariIni->tgl_penerimaan)->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                    @if ($penerimaanHariIni->is_locked)
                        <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-1" style="font-size: 11px; padding: 5px 8px;" title="Data dikunci oleh admin">
                            <ion-icon name="lock-closed" style="font-size: 13px;"></ion-icon> Terkunci
                        </span>
                    @else
                        <div class="dropdown">
                            <a href="javascript:;"
                                class="text-secondary d-inline-flex align-items-center justify-content-center"
                                data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                style="width: 32px; height: 32px; text-decoration: none;">
                                <ion-icon name="ellipsis-vertical" style="font-size: 20px;"></ion-icon>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm"
                                style="min-width: 120px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px 0; z-index: 1050;">
                                <a class="dropdown-item" href="/penerimaan/{{ $penerimaanHariIni->id }}/edit"
                                    style="padding: 8px 16px; font-size: 13px; font-weight: 500;">
                                    Edit
                                </a>
                                <form action="/penerimaan/{{ $penerimaanHariIni->id }}/delete" method="POST"
                                    onsubmit="return confirm('Hapus data penerimaan hari ini?')" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"
                                        style="padding: 8px 16px; font-size: 13px; font-weight: 500; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row text-muted small mt-2 pt-2 border-top">
                    <div class="col-6">
                        <span>R2: <strong>Rp
                                {{ number_format($penerimaanHariIni->jumlah_rp_r2, 0, ',', '.') }}</strong></span>
                        <span class="d-block text-secondary">({{ $penerimaanHariIni->jumlah_unit_r2 }} Unit)</span>
                    </div>
                    <div class="col-6 text-end">
                        <span>R4: <strong>Rp
                                {{ number_format($penerimaanHariIni->jumlah_rp_r4, 0, ',', '.') }}</strong></span>
                        <span class="d-block text-secondary">({{ $penerimaanHariIni->jumlah_unit_r4 }} Unit)</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between text-muted small mt-1 pt-1 border-top">
                    <span>Total Unit: <strong>{{ $penerimaanHariIni->total_unit }}</strong></span>
                    <span>Notice: <strong>{{ $penerimaanHariIni->jumlah_notice }} Lbr</strong></span>
                </div>
            @else
                <div class="text-muted small mt-1 mb-2">
                    Layanan belum menginput realisasi penerimaan PKB hari ini ({{ date('d-m-Y') }}).
                </div>
                <a href="/penerimaan/create" class="btn btn-primary btn-sm btn-block">
                    <ion-icon name="add-circle-outline"></ion-icon> Input Penerimaan Hari Ini
                </a>
            @endif
        </div>

        <!-- 2. Filter (Minggu, Bulan, Tahun, Custom) -->
        <div class="px-3 mt-3 mb-2">
            <ul class="nav nav-tabs style1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $filter == 'minggu' ? 'active' : '' }}" href="/penerimaan?filter=minggu">
                        Minggu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $filter == 'bulan' ? 'active' : '' }}" href="/penerimaan?filter=bulan">
                        Bulan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $filter == 'tahun' ? 'active' : '' }}" href="/penerimaan?filter=tahun">
                        Tahun
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $filter == 'custom' ? 'active' : '' }}" href="/penerimaan?filter=custom">
                        Custom
                    </a>
                </li>
            </ul>

            @if ($filter == 'custom')
                <form action="/penerimaan" method="GET" class="mt-2 pt-1 pb-1">
                    <input type="hidden" name="filter" value="custom">
                    <div class="row g-2 align-items-end">
                        <div class="col-5">
                            <label class="text-muted small mb-1 d-block" style="font-size: 11px;">Dari Tanggal</label>
                            <input type="date" name="dari" value="{{ $dari }}"
                                class="form-control form-control-sm" required
                                style="font-size: 12px; height: 36px; border-radius: 6px;">
                        </div>
                        <div class="col-5">
                            <label class="text-muted small mb-1 d-block" style="font-size: 11px;">Sampai Tanggal</label>
                            <input type="date" name="sampai" value="{{ $sampai }}"
                                class="form-control form-control-sm" required
                                style="font-size: 12px; height: 36px; border-radius: 6px;">
                        </div>
                        <div class="col-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100"
                                style="height: 36px; border-radius: 6px; padding: 0;" title="Terapkan Filter">
                                <ion-icon name="search-outline" style="font-size: 18px; vertical-align: middle;"></ion-icon>
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>

        <!-- 3. Akumulasi Total Sesuai Filter (Pembatas Biasa) -->
        <div class="bg-white border-top border-bottom px-3 py-2 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-bold text-uppercase">{{ $labelAkumulasi }}</span>
                <strong style="color: #2A8BF2; font-size: 15px;">Rp
                    {{ number_format($totalAkumulasi->total_rp ?? 0, 0, ',', '.') }}</strong>
            </div>
            <div class="d-flex justify-content-between text-muted small">
                <span>Total Unit:
                    <strong>{{ number_format($totalAkumulasi->total_unit ?? 0, 0, ',', '.') }}</strong></span>
                <span>Notice Dicetak: <strong>{{ number_format($totalAkumulasi->total_notice ?? 0, 0, ',', '.') }}
                        Lembar</strong></span>
            </div>
        </div>

        <!-- 4. Header Riwayat -->
        <div class="px-3 mt-3 mb-2">
            <div class="text-muted small fw-bold text-uppercase">Riwayat Rekap Penerimaan</div>
        </div>

        <!-- 4. Daftar Riwayat Dengan Pembatas Biasa (Tanpa Card) -->
        <div class="bg-white border-top border-bottom">
            @forelse ($histori as $h)
                <div class="px-3 py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <strong class="text-dark" style="font-size: 14px;">
                                {{ \Carbon\Carbon::parse($h->tgl_penerimaan)->translatedFormat('l, d F Y') }}
                            </strong>
                            <div class="text-muted" style="font-size: 11.5px; display: flex; align-items: center; gap: 4px; margin-top: 1px;">
                                <ion-icon name="location-outline" style="font-size: 13px; color: #ef4444; flex-shrink: 0;"></ion-icon>
                                <span>{{ $h->cabang->nama_cabang ?? 'UPPD SAMSAT Batulicin' }}</span>
                            </div>
                            <div style="font-size: 16px; font-weight: 800; color: #10b981; margin-top: 2px;">
                                Rp {{ number_format($h->total_rp, 0, ',', '.') }}
                            </div>
                        </div>
                        @if ($h->is_locked)
                            <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-1" style="font-size: 11px; padding: 4px 7px;" title="Data dikunci oleh admin">
                                <ion-icon name="lock-closed" style="font-size: 12px;"></ion-icon> Terkunci
                            </span>
                        @else
                            <div class="dropdown">
                                <a href="javascript:;"
                                    class="text-secondary d-inline-flex align-items-center justify-content-center"
                                    data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false" style="width: 32px; height: 32px; text-decoration: none;">
                                    <ion-icon name="ellipsis-vertical" style="font-size: 20px;"></ion-icon>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow-sm"
                                    style="min-width: 120px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px 0; z-index: 1050;">
                                    <a class="dropdown-item" href="/penerimaan/{{ $h->id }}/edit"
                                        style="padding: 8px 16px; font-size: 13px; font-weight: 500;">
                                        Edit
                                    </a>
                                    <form action="/penerimaan/{{ $h->id }}/delete" method="POST"
                                        onsubmit="return confirm('Hapus data penerimaan tanggal ini?')" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"
                                            style="padding: 8px 16px; font-size: 13px; font-weight: 500; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="row text-muted small mt-2">
                        <div class="col-6">
                            <span>R2: <strong>Rp {{ number_format($h->jumlah_rp_r2, 0, ',', '.') }}</strong></span>
                            <span class="d-block text-secondary">({{ $h->jumlah_unit_r2 }} Unit)</span>
                        </div>
                        <div class="col-6 text-end">
                            <span>R4: <strong>Rp {{ number_format($h->jumlah_rp_r4, 0, ',', '.') }}</strong></span>
                            <span class="d-block text-secondary">({{ $h->jumlah_unit_r4 }} Unit)</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between text-muted small mt-1 pt-1 border-top">
                        <span>Total Unit: <strong>{{ $h->total_unit }}</strong></span>
                        <span>Notice: <strong>{{ $h->jumlah_notice }} Lbr</strong></span>
                    </div>

                    @if ($h->keterangan)
                        <div class="text-muted small mt-1 fst-italic">
                            Catatan: {{ $h->keterangan }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <ion-icon name="wallet-outline" style="font-size: 36px; opacity: 0.4;"></ion-icon>
                    <div class="mt-1 small">
                        @if ($filter == 'minggu')
                            Belum ada riwayat penerimaan pada minggu ini.
                        @elseif ($filter == 'bulan')
                            Belum ada riwayat penerimaan pada bulan ini.
                        @elseif ($filter == 'tahun')
                            Belum ada riwayat penerimaan pada tahun ini.
                        @elseif ($filter == 'custom')
                            Belum ada riwayat penerimaan pada rentang tanggal yang dipilih.
                        @else
                            Belum ada riwayat rekap penerimaan.
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <div class="p-3">
            {{ $histori->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endsection
