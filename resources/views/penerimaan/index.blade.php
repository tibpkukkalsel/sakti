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
        <div class="right">
            <a href="/penerimaan/create" class="headerButton text-light">
                <ion-icon name="add-circle-outline" style="font-size: 26px;"></ion-icon>
            </a>
        </div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <div style="padding-top: 75px; padding-bottom: 75px;">
        <div class="col">
            @if (Session::get('success'))
                <div class="alert alert-success mb-2">
                    {{ Session::get('success') }}
                </div>
            @endif

            @if (Session::get('warning'))
                <div class="alert alert-warning mb-2">
                    {{ Session::get('warning') }}
                </div>
            @endif

            <!-- Card Status Hari Ini -->
            @if ($penerimaanHariIni)
                <div class="card mb-2" style="border-radius: 12px; border-left: 5px solid #10b981;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div>
                                <span class="badge bg-success" style="font-size: 11px;">Sudah Input Hari Ini</span>
                                <div class="text-muted small mt-1">
                                    {{ \Carbon\Carbon::parse($penerimaanHariIni->tgl_penerimaan)->translatedFormat('l, d F Y') }}
                                </div>
                            </div>
                            <a href="/penerimaan/{{ $penerimaanHariIni->id }}/edit" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                                <ion-icon name="create-outline"></ion-icon> Edit
                            </a>
                        </div>
                        <div class="mt-2">
                            <div class="text-muted small">Total Realisasi Hari Ini</div>
                            <div style="font-size: 22px; font-weight: 800; color: #10b981;">
                                Rp {{ number_format($penerimaanHariIni->total_rp, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="row mt-2 text-center" style="font-size: 12px;">
                            <div class="col-6">
                                <div class="p-2 rounded bg-light border">
                                    <span class="text-muted d-block small">R2 ({{ $penerimaanHariIni->jumlah_unit_r2 }} Unit)</span>
                                    <strong>Rp {{ number_format($penerimaanHariIni->jumlah_rp_r2, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded bg-light border">
                                    <span class="text-muted d-block small">R4 ({{ $penerimaanHariIni->jumlah_unit_r4 }} Unit)</span>
                                    <strong>Rp {{ number_format($penerimaanHariIni->jumlah_rp_r4, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between mt-2 pt-2 border-top text-muted small">
                            <span>Total Unit: <strong>{{ $penerimaanHariIni->total_unit }}</strong></span>
                            <span>Notice: <strong>{{ $penerimaanHariIni->jumlah_notice }} Lbr</strong></span>
                        </div>
                    </div>
                </div>
            @else
                <div class="card mb-2" style="border-radius: 12px; border-left: 5px solid #f59e0b;">
                    <div class="card-body p-3 text-center">
                        <div class="text-warning mb-1" style="font-size: 32px;">
                            <ion-icon name="alert-circle-outline"></ion-icon>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">Belum Input Penerimaan Hari Ini</h5>
                        <p class="text-muted small mb-2">
                            Silakan input realisasi penerimaan PKB untuk layanan <strong>{{ $user->nama_layanan }}</strong> hari ini.
                        </p>
                        <a href="/penerimaan/create" class="btn btn-primary btn-block">
                            <ion-icon name="add-circle-outline"></ion-icon> Input Penerimaan Hari Ini
                        </a>
                    </div>
                </div>
            @endif

            <!-- Ringkasan Akumulasi Bulan Ini -->
            <div class="card mb-2" style="border-radius: 12px;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-dark" style="font-size: 13px;">
                            <ion-icon name="stats-chart-outline" class="text-primary me-1"></ion-icon>
                            Akumulasi Bulan Ini
                        </span>
                        <span class="badge bg-primary text-white">{{ date('F Y') }}</span>
                    </div>
                    <div class="row text-center" style="font-size: 12px;">
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <small class="text-muted d-block" style="font-size: 10px;">TOTAL RP</small>
                                <strong class="text-success" style="font-size: 12px;">
                                    Rp {{ number_format($totalBulanIni->total_rp ?? 0, 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <small class="text-muted d-block" style="font-size: 10px;">TOTAL UNIT</small>
                                <strong class="text-primary" style="font-size: 12px;">
                                    {{ number_format($totalBulanIni->total_unit ?? 0, 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 rounded bg-light border">
                                <small class="text-muted d-block" style="font-size: 10px;">NOTICE</small>
                                <strong class="text-dark" style="font-size: 12px;">
                                    {{ number_format($totalBulanIni->total_notice ?? 0, 0, ',', '.') }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Riwayat Penerimaan -->
            <div class="d-flex justify-content-between align-items-center mt-3 mb-2 px-1">
                <h6 class="fw-bold text-dark mb-0">Riwayat Penerimaan</h6>
                <a href="/penerimaan/create" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                    <ion-icon name="add-outline"></ion-icon> Tambah
                </a>
            </div>

            <!-- Daftar Histori -->
            @forelse ($histori as $h)
                <div class="card mb-2" style="border-radius: 12px; border: 1px solid #e2e8f0; border-left: 5px solid #2A8BF2;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-2">
                            <div>
                                <strong class="text-dark" style="font-size: 14px;">
                                    {{ \Carbon\Carbon::parse($h->tgl_penerimaan)->translatedFormat('d F Y') }}
                                </strong>
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    {{ \Carbon\Carbon::parse($h->tgl_penerimaan)->translatedFormat('l') }}
                                </small>
                            </div>
                            <div class="d-flex align-items-center gap-1">
                                <a href="/penerimaan/{{ $h->id }}/edit" class="btn btn-sm btn-outline-warning py-1 px-2" style="border-radius: 6px;">
                                    <ion-icon name="create-outline"></ion-icon>
                                </a>
                                <form action="/penerimaan/{{ $h->id }}/delete" method="POST" onsubmit="return confirm('Hapus data penerimaan tanggal ini?')" class="m-0 d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="border-radius: 6px;">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Total Realisasi:</span>
                            <strong class="text-success fs-6">Rp {{ number_format($h->total_rp, 0, ',', '.') }}</strong>
                        </div>

                        <div class="row text-center" style="font-size: 11px;">
                            <div class="col-6">
                                <div class="p-1 rounded bg-light border">
                                    <span class="text-muted">R2:</span> <strong>Rp {{ number_format($h->jumlah_rp_r2, 0, ',', '.') }}</strong>
                                    <div class="text-muted small">({{ $h->jumlah_unit_r2 }} Unit)</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-1 rounded bg-light border">
                                    <span class="text-muted">R4:</span> <strong>Rp {{ number_format($h->jumlah_rp_r4, 0, ',', '.') }}</strong>
                                    <div class="text-muted small">({{ $h->jumlah_unit_r4 }} Unit)</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top small text-muted">
                            <span><ion-icon name="car-outline" class="me-1"></ion-icon> Total Unit: <strong>{{ $h->total_unit }}</strong></span>
                            <span><ion-icon name="receipt-outline" class="me-1"></ion-icon> Notice: <strong>{{ $h->jumlah_notice }} Lbr</strong></span>
                        </div>

                        @if ($h->keterangan)
                            <div class="mt-2 small text-muted fst-italic">
                                <ion-icon name="information-circle-outline"></ion-icon> {{ $h->keterangan }}
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card mb-2" style="border-radius: 12px; border: 1px dashed #cbd5e1;">
                    <div class="card-body text-center py-4 text-muted">
                        <ion-icon name="wallet-outline" style="font-size: 40px; color: #94a3b8;"></ion-icon>
                        <div class="mt-2" style="font-size: 13px;">Belum ada catatan penerimaan PKB.</div>
                        <a href="/penerimaan/create" class="btn btn-primary btn-sm mt-2" style="border-radius: 8px;">
                            Input Penerimaan Sekarang
                        </a>
                    </div>
                </div>
            @endforelse

            <div class="mt-3">
                {{ $histori->links() }}
            </div>
        </div>
    </div>
@endsection
