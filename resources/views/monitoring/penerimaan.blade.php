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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
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
    </style>

    <div class="section" style="padding-top: 70px; padding-bottom: 75px;">
        <!-- Card Filter Periode & Layanan -->
        <div class="card mb-3" style="border-radius: 14px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
            <div class="card-body p-3">
                <form action="/monitoring/penerimaan" method="GET" id="formFilterPenerimaan">
                    <input type="hidden" name="periode" id="input_periode" value="{{ $periode }}">

                    <!-- Label Periode -->
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                        Pilih Periode
                    </div>

                    <!-- 4 Tombol Pilihan Periode (2x2 Grid) -->
                    <div class="period-btn-grid mb-2">
                        <button type="button" class="period-btn {{ $periode == 'minggu_ini' ? 'active' : '' }}" onclick="selectPeriod('minggu_ini')">
                            Minggu Ini
                        </button>
                        <button type="button" class="period-btn {{ $periode == 'bulan_ini' ? 'active' : '' }}" onclick="selectPeriod('bulan_ini')">
                            Bulan Ini
                        </button>
                        <button type="button" class="period-btn {{ $periode == 'tahun_ini' ? 'active' : '' }}" onclick="selectPeriod('tahun_ini')">
                            Tahun Ini
                        </button>
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

                    <!-- Tombol Filter Data -->
                    <button type="submit" class="btn btn-primary w-100 font-weight-bold" style="border-radius: 10px; background: linear-gradient(135deg, #2A8BF2 0%, #1A73E8 100%); border: none; height: 42px; font-size: 14px; box-shadow: 0 4px 12px rgba(42, 139, 242, 0.3); display: flex; align-items: center; justify-content: center; gap: 6px;">
                        <ion-icon name="filter-outline" style="font-size: 18px;"></ion-icon>
                        <span>Filter Data</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Total Hero Card -->
        <div class="revenue-summary-card">
            <small style="opacity: 0.9; font-size: 11.5px; font-weight: 600;">Total Penerimaan ({{ $labelPeriode }})</small>
            <div style="font-size: 22px; font-weight: 800; margin: 4px 0; white-space: nowrap;">
                Rp {{ number_format($totalRp, 0, ',', '.') }}
            </div>
            <div class="d-flex justify-content-between pt-2 border-top border-white border-opacity-25" style="font-size: 11.5px;">
                <span>Total Unit: <strong>{{ number_format($totalUnit, 0, ',', '.') }}</strong></span>
                <span>Notice: <strong>{{ number_format($totalNotice, 0, ',', '.') }} Lbr</strong></span>
            </div>
        </div>

        <!-- Breakdown Ringkasan R2 vs R4 -->
        <div class="row g-2 mb-3">
            <div class="col-6">
                <div class="card p-2 text-center" style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff;">
                    <small class="text-muted" style="font-size: 10.5px; font-weight: 600;">Total R2 (Motor)</small>
                    <div style="font-weight: 800; font-size: 13.5px; color: #10b981; white-space: nowrap;">
                        Rp {{ number_format($totalR2Rp, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 11px;">{{ number_format($totalUnitR2, 0, ',', '.') }} Unit</small>
                </div>
            </div>
            <div class="col-6">
                <div class="card p-2 text-center" style="border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff;">
                    <small class="text-muted" style="font-size: 10.5px; font-weight: 600;">Total R4 (Mobil)</small>
                    <div style="font-weight: 800; font-size: 13.5px; color: #0891b2; white-space: nowrap;">
                        Rp {{ number_format($totalR4Rp, 0, ',', '.') }}
                    </div>
                    <small class="text-muted" style="font-size: 11px;">{{ number_format($totalUnitR4, 0, ',', '.') }} Unit</small>
                </div>
            </div>
        </div>

        <!-- Rincian Data Penerimaan Harian -->
        @forelse ($rekapList as $row)
            <div class="revenue-item-card">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <strong class="text-dark" style="font-size: 13.5px;">{{ $row->nama_layanan }}</strong>
                    <span class="badge bg-light text-primary border" style="font-size: 11px; padding: 4px 8px; border-radius: 6px;">
                        {{ date('d-m-Y', strtotime($row->tgl_penerimaan)) }}
                    </span>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span style="font-size: 16px; font-weight: 800; color: #2A8BF2; white-space: nowrap;">
                        Rp {{ number_format($row->total_rp, 0, ',', '.') }}
                    </span>
                    <span class="badge" style="background: rgba(16, 185, 129, 0.12); color: #10b981; font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                        {{ number_format($row->total_unit, 0, ',', '.') }} Unit
                    </span>
                </div>
                <div class="text-muted small mt-2 pt-2 border-top d-flex justify-content-between" style="font-size: 12px;">
                    <span style="white-space: nowrap;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-motorbike" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="#2563eb" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M19 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M7.5 14h5l4 -4H7.5m4 0h4l2 -3h-3" /></svg>
                        Rp {{ number_format($row->jumlah_rp_r2, 0, ',', '.') }} ({{ $row->jumlah_unit_r2 }})
                    </span>
                    <span style="white-space: nowrap;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-car" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="#0891b2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6" /><path d="M7 9l1 3h8l1 -3" /></svg>
                        Rp {{ number_format($row->jumlah_rp_r4, 0, ',', '.') }} ({{ $row->jumlah_unit_r4 }})
                    </span>
                </div>
            </div>
        @empty
            <div class="card text-center py-5" style="border-radius: 14px; border: 1px dashed #cbd5e1; background: #ffffff;">
                <div class="card-body">
                    <ion-icon name="receipt-outline" style="font-size: 40px; color: #94a3b8;"></ion-icon>
                    <p class="text-muted mt-2 mb-0" style="font-size: 13px;">Belum ada data penerimaan pada periode ini.</p>
                </div>
            </div>
        @endforelse
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
    </script>
@endpush
