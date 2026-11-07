@extends('layouts.admin.tabler')

@section('content')
    <!-- Page Header & Dashboard Switcher -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-7">
            <h3 class="m-0 fw-bold text-dark">Penerimaan PKB</h3>
            <small class="text-muted">Statistik &amp; Analitik Penerimaan Pajak Kendaraan Bermotor UPPD SAMSAT Batu Licin
                &bull; Periode: <strong>{{ $periodeLabel }}</strong></small>
        </div>
        <div class="col-md-5 text-md-end mt-2 mt-md-0">
            <div class="btn-group" role="group">
                <a href="/panel-adm14/dashboardadmin" class="btn btn-outline-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-check" width="20"
                        height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4"></path>
                        <path d="M15 19l2 2l4 -4"></path>
                    </svg>
                    Presensi Layanan
                </a>
                <a href="/panel-adm14/dashboard-penerimaan" class="btn btn-primary active">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="20"
                        height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
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
                            <button type="button"
                                class="btn btn-sm btn-filter-periode {{ $periode == 'hari_ini' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                data-periode="hari_ini">
                                Hari Ini
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-filter-periode {{ $periode == 'bulan_ini' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                data-periode="bulan_ini">
                                Bulan Ini
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-filter-periode {{ $periode == 'tahun_ini' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                data-periode="tahun_ini">
                                Tahun Ini
                            </button>
                            <button type="button"
                                class="btn btn-sm btn-filter-periode {{ $periode == 'custom' ? 'btn-primary' : 'btn-outline-secondary' }}"
                                data-periode="custom">
                                Custom
                            </button>
                        </div>
                    </div>

                    <!-- Dropdown Filter Akun Layanan -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <label class="form-label small text-muted mb-1">Filter Akun Layanan:</label>
                        <select name="layanan_id" class="form-select form-select-sm"
                            onchange="$('#formFilterPenerimaan').submit()">
                            <option value="all" {{ empty($layanan_id) || $layanan_id == 'all' ? 'selected' : '' }}>Semua Layanan</option>
                            @foreach ($daftarLayanan as $l)
                                <option value="{{ $l->id }}" {{ $layanan_id == $l->id ? 'selected' : '' }}>
                                    {{ $l->nama_layanan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Input Rentang Tanggal (Muncul saat custom) -->
                    <div class="col-12 col-md-6 col-xl-4" id="customDateContainer"
                        style="{{ $periode == 'custom' ? '' : 'display: none;' }}">
                        <label class="form-label small text-muted mb-1">Rentang Tanggal Custom:</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="tgl_dari" class="form-control form-control-sm"
                                value="{{ $startDate }}">
                            <span class="input-group-text">s/d</span>
                            <input type="date" name="tgl_sampai" class="form-control form-control-sm"
                                value="{{ $endDate }}">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search"
                                    width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
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

    <!-- Summary Cards (Info-Box Style, 1 Baris 2 Card) -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Penerimaan PKB -->
        <div class="col-md-6 col-12">
            <div class="info-box shadow-sm border rounded-3 p-3 bg-white h-100 d-flex align-items-center">
                <span class="info-box-icon text-bg-primary rounded-3 shadow-sm flex-shrink-0"
                    style="width: 72px; height: 72px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-cash-stack" style="font-size: 34px; color: #fff;"></i>
                </span>
                <div class="info-box-content ps-3 flex-grow-1">
                    <span class="info-box-text text-muted text-uppercase fw-semibold"
                        style="font-size: 12px; letter-spacing: 0.5px;">Total Penerimaan (PKB)</span>
                    <span class="info-box-number text-dark fw-bold d-block"
                        style="font-size: 1.75rem; line-height: 1.25; margin-top: 2px;">
                        Rp {{ number_format($summary->total_rp, 0, ',', '.') }}
                    </span>
                    <small class="text-muted d-block mt-1">Total pendapatan PKB periode terpilih</small>
                </div>
            </div>
        </div>

        <!-- Card 2: Penerimaan Roda 2 (R2) -->
        <div class="col-md-6 col-12">
            <div class="info-box shadow-sm border rounded-3 p-3 bg-white h-100 d-flex align-items-center">
                <span class="info-box-icon text-bg-success rounded-3 shadow-sm flex-shrink-0"
                    style="width: 72px; height: 72px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-bicycle" style="font-size: 34px; color: #fff;"></i>
                </span>
                <div class="info-box-content ps-3 flex-grow-1">
                    <span class="info-box-text text-muted text-uppercase fw-semibold"
                        style="font-size: 12px; letter-spacing: 0.5px;">Penerimaan Roda 2 (R2)</span>
                    <span class="info-box-number text-success fw-bold d-block"
                        style="font-size: 1.75rem; line-height: 1.25; margin-top: 2px;">
                        Rp {{ number_format($summary->sum_rp_r2, 0, ',', '.') }}
                    </span>
                    <small class="text-muted d-block mt-1">{{ number_format($summary->sum_unit_r2, 0, ',', '.') }} Unit
                        Kendaraan R2</small>
                </div>
            </div>
        </div>

        <!-- Card 3: Penerimaan Roda 4 (R4) -->
        <div class="col-md-6 col-12">
            <div class="info-box shadow-sm border rounded-3 p-3 bg-white h-100 d-flex align-items-center">
                <span class="info-box-icon text-bg-info text-white rounded-3 shadow-sm flex-shrink-0"
                    style="width: 72px; height: 72px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-car-front-fill" style="font-size: 34px; color: #fff;"></i>
                </span>
                <div class="info-box-content ps-3 flex-grow-1">
                    <span class="info-box-text text-muted text-uppercase fw-semibold"
                        style="font-size: 12px; letter-spacing: 0.5px;">Penerimaan Roda 4 (R4)</span>
                    <span class="info-box-number text-info fw-bold d-block"
                        style="font-size: 1.75rem; line-height: 1.25; margin-top: 2px;">
                        Rp {{ number_format($summary->sum_rp_r4, 0, ',', '.') }}
                    </span>
                    <small class="text-muted d-block mt-1">{{ number_format($summary->sum_unit_r4, 0, ',', '.') }} Unit
                        Kendaraan R4</small>
                </div>
            </div>
        </div>

        <!-- Card 4: Notice Pajak Dicetak -->
        <div class="col-md-6 col-12">
            <div class="info-box shadow-sm border rounded-3 p-3 bg-white h-100 d-flex align-items-center">
                <span class="info-box-icon text-bg-warning text-white rounded-3 shadow-sm flex-shrink-0"
                    style="width: 72px; height: 72px; display: flex; align-items: center; justify-content: center;">
                    <i class="bi bi-file-earmark-check-fill" style="font-size: 34px; color: #fff;"></i>
                </span>
                <div class="info-box-content ps-3 flex-grow-1">
                    <span class="info-box-text text-muted text-uppercase fw-semibold"
                        style="font-size: 12px; letter-spacing: 0.5px;">Notice Pajak Dicetak</span>
                    <span class="info-box-number text-warning fw-bold d-block"
                        style="font-size: 1.75rem; line-height: 1.25; margin-top: 2px;">
                        {{ number_format($summary->sum_notice, 0, ',', '.') }} Lembar
                    </span>
                    <small class="text-muted d-block mt-1">Total
                        {{ number_format($summary->sum_total_unit, 0, ',', '.') }} Unit Kendaraan (R2 + R4)</small>
                </div>
            </div>
        </div>
    </div>
    <!-- Charts Section: 1 Grafik Per Baris -->

    <!-- Grafik 1 (Baris 1): Tren Penerimaan Per Bulan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-0">Tren Penerimaan PKB Per Bulan</h4>
                        <span class="badge bg-light text-dark border">Tahun {{ $tahunini }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-tren-bulanan" style="min-height: 360px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik 1b: Total Pendapatan Layanan Per Bulan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-0">Total Pendapatan Layanan Per Bulan</h4>
                        <span class="badge bg-light text-dark border">Tahun {{ $tahunini }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if (count($chartLayananPerBulanSeries) > 0)
                        <div id="chart-layanan-perbulan" style="min-height: 360px;"></div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <p class="mb-0">Belum ada data pendapatan layanan pada tahun ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table (Rekap Gabungan Berdasarkan Layanan) -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" id="cardTablePenerimaan">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-0">Data Penerimaan PKB Berdasarkan Layanan</h4>
                        <div class="small text-muted mt-1">{{ $periodeLabel }}</div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Akun Layanan</th>
                                    <th class="text-end">Realisasi R2</th>
                                    <th class="text-end">Realisasi R4</th>
                                    <th class="text-end">Total Penerimaan (Rp)</th>
                                    <th class="text-center" style="width: 120px;">Total Unit</th>
                                    <th class="text-center" style="width: 130px;">Notice Dicetak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dataPenerimaan as $row)
                                    <tr>
                                        <td class="text-center text-muted fw-bold">
                                            {{ $loop->iteration + $dataPenerimaan->firstItem() - 1 }}
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border fs-6 px-2 py-1">
                                                {{ $row->nama_layanan }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="text-success font-weight-bold">Rp
                                                {{ number_format($row->jumlah_rp_r2, 0, ',', '.') }}</div>
                                            <small
                                                class="text-muted">{{ number_format($row->jumlah_unit_r2, 0, ',', '.') }}
                                                unit</small>
                                        </td>
                                        <td class="text-end">
                                            <div class="text-info font-weight-bold">Rp
                                                {{ number_format($row->jumlah_rp_r4, 0, ',', '.') }}</div>
                                            <small
                                                class="text-muted">{{ number_format($row->jumlah_unit_r4, 0, ',', '.') }}
                                                unit</small>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-dark fs-6">Rp
                                                {{ number_format($row->total_rp, 0, ',', '.') }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border">{{ number_format($row->total_unit, 0, ',', '.') }}
                                                Unit</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark border">{{ number_format($row->jumlah_notice, 0, ',', '.') }}
                                                Lembar</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Tidak ada data penerimaan pada periode atau filter layanan yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($dataPenerimaan->hasPages())
                    <div class="card-footer bg-white py-2 border-top d-flex align-items-center">
                        {{ $dataPenerimaan->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabel Rekapitulasi Penerimaan Berdasarkan Lokasi (Urutan dari atas penerimaan terbanyak) -->
    <div class="row mt-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" id="cardTablePenerimaanLokasi">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-0">Data Penerimaan PKB Berdasarkan Lokasi</h4>
                        <div class="small text-muted mt-1">{{ $periodeLabel }}</div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;" class="text-center">No</th>
                                    <th>Lokasi Pelayanan</th>
                                    <th class="text-end">Realisasi R2</th>
                                    <th class="text-end">Realisasi R4</th>
                                    <th class="text-end">Total Penerimaan (Rp)</th>
                                    <th class="text-center" style="width: 120px;">Total Unit</th>
                                    <th class="text-center" style="width: 130px;">Notice Dicetak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rekapLokasiTabel as $lok)
                                    <tr>
                                        <td class="text-center text-muted fw-bold">
                                            {{ $loop->iteration + $rekapLokasiTabel->firstItem() - 1 }}
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border fs-6 px-2 py-1">
                                                {{ $lok->nama_cabang }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="text-success font-weight-bold">Rp
                                                {{ number_format($lok->rp_r2, 0, ',', '.') }}</div>
                                            <small class="text-muted">{{ number_format($lok->unit_r2, 0, ',', '.') }}
                                                unit</small>
                                        </td>
                                        <td class="text-end">
                                            <div class="text-info font-weight-bold">Rp
                                                {{ number_format($lok->rp_r4, 0, ',', '.') }}</div>
                                            <small class="text-muted">{{ number_format($lok->unit_r4, 0, ',', '.') }}
                                                unit</small>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-dark fs-6">Rp
                                                {{ number_format($lok->total_rp, 0, ',', '.') }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-light text-dark border">{{ number_format($lok->total_unit, 0, ',', '.') }}
                                                Unit</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-warning text-dark border">{{ number_format($lok->total_notice, 0, ',', '.') }}
                                                Lembar</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            Tidak ada data penerimaan lokasi pada periode yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($rekapLokasiTabel->hasPages())
                    <div class="card-footer bg-white py-2 border-top d-flex align-items-center">
                        {{ $rekapLokasiTabel->links('vendor.pagination.bootstrap-5') }}
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
                    toolbar: {
                        show: false
                    },
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
                            if (val >= 1000000000) {
                                return 'Rp ' + (val / 1000000000).toFixed(2) + ' M';
                            } else if (val >= 1000000) {
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

            // Inisialisasi Grafik Total Pendapatan Layanan Per Bulan
            @if (count($chartLayananPerBulanSeries) > 0)
                var optionsLayananPerBulan = {
                    series: @json($chartLayananPerBulanSeries),
                    chart: {
                        type: 'bar',
                        height: 350,
                        stacked: false,
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'inherit'
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '50%',
                            borderRadius: 4
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    colors: ['#0d6efd', '#20c997', '#fd7e14', '#6f42c1', '#d63384', '#0dcaf0'],
                    xaxis: {
                        categories: @json($chartBulanLabels),
                        labels: {
                            style: {
                                fontWeight: 500
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                if (val >= 1000000000) {
                                    return 'Rp ' + (val / 1000000000).toFixed(2) + ' M';
                                } else if (val >= 1000000) {
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

                var chartLayananPerBulan = new ApexCharts(document.querySelector("#chart-layanan-perbulan"),
                    optionsLayananPerBulan);
                chartLayananPerBulan.render();
            @endif

            // AJAX Pagination untuk Tabel Rincian Data Penerimaan
            function loadDashboardTable(url, pushState) {
                if (pushState === undefined) pushState = true;
                var card = document.getElementById('cardTablePenerimaan');
                if (!card) return;

                $('#cardTablePenerimaan .table-responsive, #cardTablePenerimaan .card-footer').css({
                    'opacity': '0.35',
                    'pointer-events': 'none',
                    'transition': 'opacity 0.2s ease'
                });

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        try {
                            var parser = new DOMParser();
                            var doc = parser.parseFromString(response, 'text/html');
                            var newCard = doc.getElementById('cardTablePenerimaan');

                            if (newCard) {
                                card.innerHTML = newCard.innerHTML;
                                if (pushState && window.history && window.history.pushState) {
                                    window.history.pushState({
                                        path: url
                                    }, '', url);
                                }
                            } else {
                                window.location.href = url;
                            }
                        } catch (err) {
                            window.location.href = url;
                        }
                    },
                    error: function() {
                        window.location.href = url;
                    }
                });
            }

            $(document).on('click', '#cardTablePenerimaan .pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                if (!url || url === '#' || url.indexOf('javascript') === 0) return;
                loadDashboardTable(url, true);
            });

            // AJAX Pagination untuk Tabel Penerimaan Lokasi
            function loadDashboardTableLokasi(url, pushState) {
                if (pushState === undefined) pushState = true;
                var card = document.getElementById('cardTablePenerimaanLokasi');
                if (!card) return;

                $('#cardTablePenerimaanLokasi .table-responsive, #cardTablePenerimaanLokasi .card-footer').css({
                    'opacity': '0.35',
                    'pointer-events': 'none',
                    'transition': 'opacity 0.2s ease'
                });

                $.ajax({
                    url: url,
                    type: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        try {
                            var parser = new DOMParser();
                            var doc = parser.parseFromString(response, 'text/html');
                            var newCard = doc.getElementById('cardTablePenerimaanLokasi');

                            if (newCard) {
                                card.innerHTML = newCard.innerHTML;
                                if (pushState && window.history && window.history.pushState) {
                                    window.history.pushState({
                                        path: url
                                    }, '', url);
                                }
                            } else {
                                window.location.href = url;
                            }
                        } catch (err) {
                            window.location.href = url;
                        }
                    },
                    error: function() {
                        window.location.href = url;
                    }
                });
            }

            $(document).on('click', '#cardTablePenerimaanLokasi .pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                if (!url || url === '#' || url.indexOf('javascript') === 0) return;
                loadDashboardTableLokasi(url, true);
            });

            window.addEventListener('popstate', function() {
                if (window.location.pathname.indexOf('dashboard-penerimaan') !== -1) {
                    loadDashboardTable(window.location.href, false);
                }
            });
        });
    </script>
@endpush
