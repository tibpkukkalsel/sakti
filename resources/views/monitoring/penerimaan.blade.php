@extends('layouts.monitoring')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/monitoring/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Data Penerimaan PKB</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <style>
        .revenue-summary-card {
            background: #2A8BF2;
            background: linear-gradient(135deg, #2A8BF2 0%, #1A73E8 100%);
            border-radius: 16px;
            color: #ffffff;
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: 0 4px 14px rgba(42, 139, 242, 0.25);
        }

        .revenue-item-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }
    </style>

    <div class="section" style="padding-top: 70px; padding-bottom: 75px;">
        <!-- Filter Bulan & Layanan -->
        <div class="card mb-3" style="border-radius: 14px; border: 1px solid #e2e8f0;">
            <div class="card-body p-3">
                <form action="/monitoring/penerimaan" method="GET">
                    <div class="row g-2">
                        <div class="col-6">
                            <select name="bulan" class="form-select form-select-sm" style="border-radius: 8px;">
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ sprintf('%02d', $m) }}" {{ (int)$bulan == $m ? 'selected' : '' }}>
                                        {{ $namabulan[$m] }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <select name="tahun" class="form-select form-select-sm" style="border-radius: 8px;">
                                @for ($y = date('Y') - 1; $y <= date('Y') + 1; $y++)
                                    <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>
                                        Tahun {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <select name="layanan_id" class="form-select form-select-sm" style="border-radius: 8px;">
                                <option value="all">Semua Layanan Keliling</option>
                                @foreach ($layananList as $l)
                                    <option value="{{ $l->id }}" {{ $layanan_id == $l->id ? 'selected' : '' }}>
                                        {{ $l->nama_layanan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100 font-weight-bold" style="border-radius: 8px; background-color: #2A8BF2; border-color: #2A8BF2;">
                                Filter Data
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Total Hero Card -->
        <div class="revenue-summary-card">
            <small style="opacity: 0.9; font-size: 11.5px;">Total Penerimaan ({{ $namabulan[(int)$bulan] }} {{ $tahun }})</small>
            <div style="font-size: 22px; font-weight: 800; margin: 4px 0;">
                Rp {{ number_format($totalRp, 0, ',', '.') }}
            </div>
            <div class="d-flex justify-content-between pt-2 border-top border-white border-opacity-25" style="font-size: 11.5px;">
                <span>Total Objek: <strong>{{ number_format($totalUnit, 0, ',', '.') }} unit</strong></span>
                <span>R2: <strong>{{ number_format($totalUnitR2, 0, ',', '.') }}</strong> | R4: <strong>{{ number_format($totalUnitR4, 0, ',', '.') }}</strong></span>
            </div>
        </div>

        <!-- Breakdown Ringkasan R2 vs R4 -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="card p-2 text-center" style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff;">
                    <small class="text-muted" style="font-size: 10.5px;">Total Pokok R2 (Motor)</small>
                    <div style="font-weight: 700; font-size: 13.5px; color: #10b981;">
                        Rp {{ number_format($totalR2Rp, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card p-2 text-center" style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff;">
                    <small class="text-muted" style="font-size: 10.5px;">Total Pokok R4 (Mobil)</small>
                    <div style="font-weight: 700; font-size: 13.5px; color: #f59e0b;">
                        Rp {{ number_format($totalR4Rp, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Rincian Data Penerimaan Harian -->
        @forelse ($rekapList as $row)
            <div class="revenue-item-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <strong class="text-dark" style="font-size: 13.5px;">{{ $row->nama_layanan }}</strong>
                    <span class="badge bg-light text-primary border" style="font-size: 11px;">
                        {{ date('d-m-Y', strtotime($row->tgl_penerimaan)) }}
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span style="font-size: 16px; font-weight: 800; color: #2A8BF2;">
                        Rp {{ number_format($row->total_rp, 0, ',', '.') }}
                    </span>
                    <span class="badge bg-success text-white" style="font-size: 11px;">
                        {{ $row->total_unit }} Unit Terlayani
                    </span>
                </div>
                <div class="text-muted small mt-2 pt-2 border-top d-flex justify-content-between">
                    <span>R2: Rp {{ number_format($row->jumlah_rp_r2, 0, ',', '.') }} ({{ $row->jumlah_unit_r2 }})</span>
                    <span>R4: Rp {{ number_format($row->jumlah_rp_r4, 0, ',', '.') }} ({{ $row->jumlah_unit_r4 }})</span>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted small">
                Belum ada data penerimaan pada periode ini.
            </div>
        @endforelse
    </div>
@endsection

