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
    <style>
        body {
            background-color: #f1f5f9 !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        #appCapsule {
            padding-top: 60px !important;
            padding-bottom: 95px !important;
        }

        .card-penerimaan {
            border-radius: 18px;
            border: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            margin: 12px 14px;
            background: #ffffff;
            overflow: hidden;
        }

        .card-today {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            border-radius: 20px;
            padding: 20px;
            margin: 14px;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.25);
        }

        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 12px;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(4px);
            padding: 10px 12px;
            border-radius: 12px;
        }

        .stat-label {
            font-size: 11px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 14px;
            font-weight: 700;
            margin-top: 2px;
        }

        .btn-input-today {
            background: #ffffff;
            color: #059669;
            font-weight: 700;
            border-radius: 14px;
            padding: 12px;
            display: block;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 14px;
            transition: all 0.2s;
        }

        .btn-input-today:hover, .btn-input-today:active {
            background: #f0fdf4;
            color: #047857;
        }

        .history-item {
            background: #ffffff;
            border-radius: 16px;
            padding: 15px;
            margin: 0 14px 12px 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            border: 1px solid #f1f5f9;
        }
    </style>

    <div class="section pt-2">
        @if (Session::get('success'))
            <div class="alert alert-success mx-3 mt-2" style="border-radius: 14px;">
                <div class="d-flex align-items-center gap-2">
                    <ion-icon name="checkmark-circle-outline" style="font-size: 22px;"></ion-icon>
                    <span>{{ Session::get('success') }}</span>
                </div>
            </div>
        @endif

        @if (Session::get('warning'))
            <div class="alert alert-warning mx-3 mt-2" style="border-radius: 14px;">
                <div class="d-flex align-items-center gap-2">
                    <ion-icon name="alert-circle-outline" style="font-size: 22px;"></ion-icon>
                    <span>{{ Session::get('warning') }}</span>
                </div>
            </div>
        @endif

        <!-- Card Status Hari Ini -->
        @if ($penerimaanHariIni)
            <div class="card-today">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="badge bg-white text-success px-2 py-1 fw-bold mb-1" style="border-radius: 8px;">
                            <ion-icon name="checkmark-outline"></ion-icon> SUDAH INPUT HARI INI
                        </div>
                        <div style="font-size: 13px; opacity: 0.95;">
                            {{ \Carbon\Carbon::parse($penerimaanHariIni->tgl_penerimaan)->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                    <a href="/penerimaan/{{ $penerimaanHariIni->id }}/edit" class="btn btn-sm btn-light text-success fw-bold" style="border-radius: 10px;">
                        <ion-icon name="create-outline"></ion-icon> Edit
                    </a>
                </div>

                <div class="mt-3">
                    <div class="stat-label">Total Penerimaan Hari Ini</div>
                    <div style="font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">
                        Rp {{ number_format($penerimaanHariIni->total_rp, 0, ',', '.') }}
                    </div>
                </div>

                <div class="stat-grid">
                    <div class="stat-box">
                        <div class="stat-label">Roda 2 (R2)</div>
                        <div class="stat-value">Rp {{ number_format($penerimaanHariIni->jumlah_rp_r2, 0, ',', '.') }}</div>
                        <div style="font-size: 11px; opacity: 0.9;">{{ $penerimaanHariIni->jumlah_unit_r2 }} Unit</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Roda 4 (R4)</div>
                        <div class="stat-value">Rp {{ number_format($penerimaanHariIni->jumlah_rp_r4, 0, ',', '.') }}</div>
                        <div style="font-size: 11px; opacity: 0.9;">{{ $penerimaanHariIni->jumlah_unit_r4 }} Unit</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Total Unit</div>
                        <div class="stat-value">{{ $penerimaanHariIni->total_unit }} Kendaraan</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-label">Notice Dicetak</div>
                        <div class="stat-value">{{ $penerimaanHariIni->jumlah_notice }} Lembar</div>
                    </div>
                </div>
            </div>
        @else
            <div class="card-penerimaan p-3 text-center" style="border-left: 5px solid #f59e0b; background: #fffbeb;">
                <div style="font-size: 36px; color: #f59e0b;">
                    <ion-icon name="cash-outline"></ion-icon>
                </div>
                <h5 class="fw-bold text-dark mt-1 mb-1">Belum Ada Input Hari Ini!</h5>
                <p class="text-muted small mb-2">
                    Silakan input rekap penerimaan PKB (R2, R4, unit & notice) untuk layanan <strong>{{ $user->nama_layanan }}</strong> hari ini ({{ date('d-m-Y') }}).
                </p>
                <a href="/penerimaan/create" class="btn btn-primary w-100 fw-bold" style="border-radius: 12px; background: #059669; border-color: #059669;">
                    <ion-icon name="add-circle-outline" class="me-1"></ion-icon> Input Penerimaan Hari Ini
                </a>
            </div>
        @endif

        <!-- Ringkasan Bulan Ini -->
        <div class="card-penerimaan p-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-dark fs-6">
                    <ion-icon name="stats-chart-outline" class="text-primary me-1"></ion-icon> Akumulasi Bulan Ini
                </span>
                <span class="badge bg-primary text-white">{{ date('F Y') }}</span>
            </div>
            <div class="row g-2 text-center">
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
                            {{ number_format($totalBulanIni->total_unit ?? 0, 0, ',', '.') }} Unit
                        </strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 rounded bg-light border">
                        <small class="text-muted d-block" style="font-size: 10px;">NOTICE</small>
                        <strong class="text-dark" style="font-size: 12px;">
                            {{ number_format($totalBulanIni->total_notice ?? 0, 0, ',', '.') }} Lembar
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tombol Tambah Tanggal Lain -->
        <div class="px-3 mb-2 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold text-secondary mb-0">Riwayat Rekap Penerimaan</h6>
            <a href="/penerimaan/create" class="btn btn-sm btn-outline-primary" style="border-radius: 10px;">
                <ion-icon name="add-outline"></ion-icon> Input Tanggal Lain
            </a>
        </div>

        <!-- Daftar Histori -->
        @forelse ($histori as $h)
            <div class="history-item">
                <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-2">
                    <div>
                        <strong class="text-dark">
                            {{ \Carbon\Carbon::parse($h->tgl_penerimaan)->translatedFormat('d F Y') }}
                        </strong>
                        <small class="text-muted d-block" style="font-size: 11px;">
                            {{ \Carbon\Carbon::parse($h->tgl_penerimaan)->translatedFormat('l') }}
                        </small>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <a href="/penerimaan/{{ $h->id }}/edit" class="btn btn-sm btn-outline-warning py-1 px-2" style="border-radius: 8px;">
                            <ion-icon name="create-outline"></ion-icon>
                        </a>
                        <form action="/penerimaan/{{ $h->id }}/delete" method="POST" onsubmit="return confirm('Hapus data penerimaan tanggal ini?')" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2" style="border-radius: 8px;">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Total Penerimaan:</span>
                    <strong class="text-success fs-6">Rp {{ number_format($h->total_rp, 0, ',', '.') }}</strong>
                </div>

                <div class="row g-2 text-center" style="font-size: 11px;">
                    <div class="col-6">
                        <div class="p-1 rounded bg-light border">
                            <span class="text-muted">R2:</span> <strong>Rp {{ number_format($h->jumlah_rp_r2, 0, ',', '.') }}</strong>
                            <div class="text-secondary small">{{ $h->jumlah_unit_r2 }} Unit</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-1 rounded bg-light border">
                            <span class="text-muted">R4:</span> <strong>Rp {{ number_format($h->jumlah_rp_r4, 0, ',', '.') }}</strong>
                            <div class="text-secondary small">{{ $h->jumlah_unit_r4 }} Unit</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top small text-muted">
                    <span><ion-icon name="car-outline" class="me-1"></ion-icon> Total Unit: <strong>{{ $h->total_unit }}</strong></span>
                    <span><ion-icon name="document-text-outline" class="me-1"></ion-icon> Notice: <strong>{{ $h->jumlah_notice }}</strong> Lembar</span>
                </div>

                @if ($h->keterangan)
                    <div class="mt-2 small text-secondary fst-italic">
                        <ion-icon name="information-circle-outline"></ion-icon> {{ $h->keterangan }}
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <ion-icon name="wallet-outline" style="font-size: 48px; opacity: 0.4;"></ion-icon>
                <div class="mt-2">Belum ada data rekap penerimaan yang diinput.</div>
                <a href="/penerimaan/create" class="btn btn-primary btn-sm mt-2" style="border-radius: 10px;">
                    Mulai Input Sekarang
                </a>
            </div>
        @endforelse

        <div class="px-3 mt-3">
            {{ $histori->links() }}
        </div>
    </div>
@endsection

