@extends('layouts.admin.tabler')

@section('content')
    <!-- Page Header & Dashboard Switcher -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-7">
            <h3 class="m-0 fw-bold text-dark">Dashboard Penerimaan PKB</h3>
            <small class="text-muted">Statistik &amp; Analitik Penerimaan Pajak Kendaraan Bermotor UPPD SAMSAT Batu Licin &bull; Periode: <strong>{{ $periodeLabel }}</strong></small>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0">
            <div class="btn-group" role="group">
                <a href="/panel-adm14/dashboardadmin" class="btn btn-outline-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-check" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4"></path>
                        <path d="M15 19l2 2l4 -4"></path>
                    </svg>
                    Presensi Layanan
                </a>
                <a href="/panel-adm14/dashboard-penerimaan" class="btn btn-primary active">
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

    <!-- Top Filter Bar -->
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-3">
            <form action="/panel-adm14/dashboard-penerimaan" method="GET" id="formFilterPenerimaan">
                <input type="hidden" name="periode" id="inputPeriode" value="{{ $periode }}">
                
                <div class="row g-2 align-items-center">
                    <!-- Tombol Cepat Pilihan Periode -->
                    <div class="col-12 col-xl-5">
                        <label class="form-label small text-muted mb-1 d-block">Pilihan Periode Waktu:</label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-sm btn-filter-periode {{ $periode == 'hari_ini' ? 'btn-primary' : 'btn-outline-secondary' }}" data-periode="hari_ini">
                                Hari Ini
                            </button>
                            <button type="button" class="btn btn-sm btn-filter-periode {{ $periode == 'bulan_ini' ? 'btn-primary' : 'btn-outline-secondary' }}" data-periode="bulan_ini">
                                Bulan Ini
                            </button>
                            <button type="button" class="btn btn-sm btn-filter-periode {{ $periode == 'tahun_ini' ? 'btn-primary' : 'btn-outline-secondary' }}" data-periode="tahun_ini">
                                Tahun Ini
                            </button>
                            <button type="button" class="btn btn-sm btn-filter-periode {{ $periode == 'custom' ? 'btn-primary' : 'btn-outline-secondary' }}" data-periode="custom">
                                Custom
                            </button>
                        </div>
                    </div>

                    <!-- Dropdown Filter Akun Layanan -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <label class="form-label small text-muted mb-1">Filter Akun Layanan:</label>
                        <select name="layanan_id" class="form-select form-select-sm" onchange="$('#formFilterPenerimaan').submit()">
                            <option value="all" {{ empty($layanan_id) || $layanan_id == 'all' ? 'selected' : '' }}>-- Semua Layanan --</option>
                            @foreach ($daftarLayanan as $l)
                                <option value="{{ $l->id }}" {{ $layanan_id == $l->id ? 'selected' : '' }}>
                                    {{ $l->nama_layanan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Input Rentang Tanggal (Muncul saat custom) -->
                    <div class="col-12 col-md-6 col-xl-4" id="customDateContainer" style="{{ $periode == 'custom' ? '' : 'display: none;' }}">
                        <label class="form-label small text-muted mb-1">Rentang Tanggal Custom:</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="tgl_dari" class="form-control form-control-sm" value="{{ $startDate }}">
                            <span class="input-group-text">s/d</span>
                            <input type="date" name="tgl_sampai" class="form-control form-control-sm" value="{{ $endDate }}">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                    <path d="M21 21l-6 -6"></path>
                                </svg>
                                Terapkan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row row-deck row-cards mb-3">
        <!-- Card 1: Total Penerimaan -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-coin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                    <path d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1"></path>
                                    <path d="M12 7v10"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-muted">Total Penerimaan</div>
                            <div class="fs-4 font-weight-bold text-dark">Rp {{ number_format($summary->total_rp, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Penerimaan R2 -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-motorbike" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M5 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                    <path d="M19 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                    <path d="M7.5 14h5l4 -4H7.5m4 0h4l2 -3h-3"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-muted">Penerimaan Roda 2 (R2)</div>
                            <div class="fs-4 font-weight-bold text-success">Rp {{ number_format($summary->sum_rp_r2, 0, ',', '.') }}</div>
                            <small class="text-muted">{{ number_format($summary->sum_unit_r2, 0, ',', '.') }} Unit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Penerimaan R4 -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-car" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                    <path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6"></path>
                                    <path d="M7 9l1 3h8l1 -3"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-muted">Penerimaan Roda 4 (R4)</div>
                            <div class="fs-4 font-weight-bold text-info">Rp {{ number_format($summary->sum_rp_r4, 0, ',', '.') }}</div>
                            <small class="text-muted">{{ number_format($summary->sum_unit_r4, 0, ',', '.') }} Unit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Notice Dicetak -->
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-warning text-white avatar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2"></path>
                                </svg>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium text-muted">Notice Pajak Dicetak</div>
                            <div class="fs-4 font-weight-bold text-warning">{{ number_format($summary->sum_notice, 0, ',', '.') }} Lembar</div>
                            <small class="text-muted">Total {{ number_format($summary->sum_total_unit, 0, ',', '.') }} Unit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-3">
        <!-- Grafik 1: Tren Penerimaan Per Bulan -->
        <div class="col-xl-7 col-12 mb-3 mb-xl-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-0">Tren Penerimaan PKB Per Bulan</h4>
                        <small class="text-muted">Grafik tren realisasi penerimaan bulanan tahun {{ $tahunini }}</small>
                    </div>
                    <span class="badge bg-light text-dark border">Tahun {{ $tahunini }}</span>
                </div>
                <div class="card-body">
                    <div id="chart-tren-bulanan" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Bar ke Samping Lokasi / Layanan Penerimaan Tertinggi -->
        <div class="col-xl-5 col-12">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-0">Peringkat Penerimaan Layanan</h4>
                        <small class="text-muted">Grafik bar ke samping penerimaan terbanyak</small>
                    </div>
                    <span class="badge bg-success text-white">Top Layanan</span>
                </div>
                <div class="card-body">
                    @if (count($chartTopLabels) > 0)
                        <div id="chart-top-layanan" style="min-height: 320px;"></div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar-off mb-2 text-secondary" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z"></path>
                                <path d="M14 10v10"></path>
                                <path d="M4 20l16 0"></path>
                                <path d="M3 3l18 18"></path>
                            </svg>
                            <p class="mb-0">Belum ada data penerimaan pada periode yang dipilih.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table (PIC TIDAK DITAMPILKAN sesuai instruksi user) -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-0">Rincian Data Penerimaan Harian PKB</h4>
                        <small class="text-muted">Daftar rekapan transaksi penerimaan harian per akun layanan (tanpa PIC).</small>
                    </div>
                    <a href="/panel-adm14/penerimaan" class="btn btn-sm btn-outline-secondary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-table" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z"></path>
                            <path d="M3 10h18"></path>
                            <path d="M10 3v18"></path>
                        </svg>
                        Buka Kelola Rekap
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th style="width: 120px;">Tanggal Layanan</th>
                                    <th>Akun Layanan</th>
                                    <th class="text-end">Realisasi R2</th>
                                    <th class="text-end">Realisasi R4</th>
                                    <th class="text-end">Total Penerimaan (Rp)</th>
                                    <th class="text-center" style="width: 120px;">Total Unit</th>
                                    <th class="text-center" style="width: 130px;">Notice Dicetak</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataPenerimaan as $row)
                                <tr>
                                    <td class="text-center text-muted fw-bold">{{ $loop->iteration + $dataPenerimaan->firstItem() - 1 }}</td>
                                    <td>
                                        <strong class="text-dark">{{ date('d-m-Y', strtotime($row->tgl_penerimaan)) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fs-6 px-2 py-1">
                                            {{ $row->nama_layanan }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="text-success font-weight-bold">Rp {{ number_format($row->jumlah_rp_r2, 0, ',', '.') }}</div>
                                        <small class="text-muted">{{ number_format($row->jumlah_unit_r2, 0, ',', '.') }} unit</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="text-info font-weight-bold">Rp {{ number_format($row->jumlah_rp_r4, 0, ',', '.') }}</div>
                                        <small class="text-muted">{{ number_format($row->jumlah_unit_r4, 0, ',', '.') }} unit</small>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-dark fs-6">Rp {{ number_format($row->total_rp, 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border">{{ $row->total_unit }} Unit</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark border">{{ $row->jumlah_notice }} Lembar</span>
                                    </td>
                                    <td>
                                        <span class="text-muted small">{{ $row->keterangan ?: '-' }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        Tidak ada data penerimaan pada periode atau filter layanan yang dipilih.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($dataPenerimaan->hasPages())
                <div class="card-footer bg-white py-2 border-top">
                    {{ $dataPenerimaan->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('myscript')
<!-- ApexCharts Script -->
<script src="{{ asset('tabler/dist/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
<script>
    // Format Rupiah Helper
    function formatRupiahChart(val) {
        return 'Rp ' + Number(val).toLocaleString('id-ID');
    }

    $(function() {
        // Handle Tombol Pilihan Periode
        $('.btn-filter-periode').click(function() {
            var periode = $(this).data('periode');
            $('#inputPeriode').val(periode);
            if (periode === 'custom') {
                $('#customDateContainer').slideDown(150);
            } else {
                $('#customDateContainer').slideUp(150);
                $('#formFilterPenerimaan').submit();
            }
        });

        // 1. Inisialisasi Grafik Tren Penerimaan Bulanan
        var optionsBulanan = {
            series: [{
                name: 'Total Penerimaan',
                type: 'column',
                data: @json($chartBulanValuesTotal)
            }, {
                name: 'Penerimaan R2',
                type: 'line',
                data: @json($chartBulanValuesR2)
            }, {
                name: 'Penerimaan R4',
                type: 'line',
                data: @json($chartBulanValuesR4)
            }],
            chart: {
                height: 320,
                type: 'line',
                stacked: false,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            stroke: {
                width: [0, 3, 3],
                curve: 'smooth'
            },
            plotOptions: {
                bar: {
                    columnWidth: '40%',
                    borderRadius: 4
                }
            },
            fill: {
                opacity: [0.85, 1, 1],
            },
            colors: ['#0d6efd', '#198754', '#0dcaf0'],
            labels: @json($chartBulanLabels),
            markers: {
                size: [0, 4, 4]
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        if (val >= 1000000) {
                            return 'Rp ' + (val / 1000000).toFixed(1) + ' jt';
                        }
                        return 'Rp ' + Number(val).toLocaleString('id-ID');
                    }
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(val) {
                        return formatRupiahChart(val);
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right'
            }
        };

        var chartBulanan = new ApexCharts(document.querySelector("#chart-tren-bulanan"), optionsBulanan);
        chartBulanan.render();

        // 2. Inisialisasi Grafik Bar ke Samping (Top Layanan / Lokasi)
        @if (count($chartTopLabels) > 0)
        var optionsTop = {
            series: [{
                name: 'Penerimaan PKB',
                data: @json($chartTopValues)
            }],
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                    barHeight: '55%',
                    dataLabels: {
                        position: 'bottom'
                    }
                }
            },
            colors: ['#198754'],
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                formatter: function(val, opt) {
                    return formatRupiahChart(val);
                },
                offsetX: 0,
                style: {
                    fontSize: '11px',
                    fontWeight: 600,
                    colors: ['#333']
                }
            },
            xaxis: {
                categories: @json($chartTopLabels),
                labels: {
                    formatter: function(val) {
                        if (val >= 1000000) {
                            return (val / 1000000).toFixed(1) + ' jt';
                        }
                        return Number(val).toLocaleString('id-ID');
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontWeight: 600
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return formatRupiahChart(val);
                    }
                }
            }
        };

        var chartTop = new ApexCharts(document.querySelector("#chart-top-layanan"), optionsTop);
        chartTop.render();
        @endif
    });
</script>
@endpush
