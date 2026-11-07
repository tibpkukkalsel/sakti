@extends('layouts.admin.tabler')

@section('content')
    <!-- Page Header & Dashboard Switcher -->
    <div class="row mb-3 align-items-center">
        <div class="col-md-7">
            <h3 class="m-0 fw-bold text-dark">Penerimaan PKB</h3>
            <small class="text-muted">Statistik &amp; Analitik Penerimaan Pajak Kendaraan Bermotor UPPD SAMSAT Batulicin
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

    <!-- Grafik 1c & 1d: Pie Chart Total Pendapatan Per Layanan & Persentase Pendapatan Berdasarkan Hari -->
    <div class="row mb-4">
        <!-- Kolom Kiri: Chart Pie Total & Persentase Pendapatan Per Layanan -->
        <div class="col-lg-6 col-12 mb-3 mb-lg-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-0">Total & Persentase Pendapatan Per Layanan</h4>
                        <span class="badge bg-light text-dark border">Tahun {{ $tahunini }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-pie-layanan" style="min-height: 380px;"></div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Chart Pie Persentase Pendapatan Berdasarkan Hari -->
        <div class="col-lg-6 col-12">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title fw-bold text-dark mb-0">Persentase Pendapatan Berdasarkan Hari</h4>
                        <span class="badge bg-light text-dark border">Tahun {{ $tahunini }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart-pie-hari" style="min-height: 380px;"></div>

                    <!-- Petunjuk atau Rincian saat Warna Pie Hari Diklik -->
                    <div id="detail-hari-hint" class="text-center py-2 px-1 text-muted mt-2" style="font-size: 12px; font-style: italic; border-top: 1px dashed #e2e8f0;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pointer me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M14.778 12.222l4 4.222l-4.222 4.222l-4 -4.222l4.222 -4.222z"></path>
                            <path d="M9.222 17.778l-4.222 -4.222l4.222 -4.222l4 4.222l-4 4.222z"></path>
                        </svg>
                        Klik salah satu potongan warna hari di atas untuk melihat rincian layanan & lokasi
                    </div>

                    <div id="detail-hari-wrapper" class="mt-3 pt-3 border-top" style="display: none;">
                        <div class="px-1 mb-2">
                            <span style="font-size: 14px; font-weight: 700; color: #1e293b;">
                                Rincian Hari <span id="label-selected-hari" style="color: #2A8BF2; font-weight: 700;"></span>
                            </span>
                        </div>
                        <div id="detail-hari-list"></div>
                    </div>
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

            // Inisialisasi Chart Pie Total & Persentase Pendapatan Per Layanan
            var optionsPieLayanan = {
                series: @json($pieLayananValues),
                labels: @json($pieLayananLabels),
                chart: {
                    type: 'pie',
                    height: 380,
                    fontFamily: 'inherit'
                },
                colors: ['#2A8BF2', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4'],
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val.toFixed(1) + "%";
                    },
                    style: {
                        fontSize: '11px',
                        fontWeight: 700,
                        colors: ['#000000']
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return 'Rp ' + Number(val).toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'left',
                    fontSize: '11px',
                    formatter: function(seriesName, opts) {
                        var val = opts.w.globals.series[opts.seriesIndex];
                        var total = opts.w.globals.series.reduce(function(a, b) { return a + b; }, 0);
                        var percent = total > 0 ? (val / total * 100).toFixed(1) : 0;
                        return seriesName + ': Rp ' + Number(val).toLocaleString('id-ID') + ' (' + percent + '%)';
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 400
                        },
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'left'
                        }
                    }
                }]
            };

            var chartPieLayanan = new ApexCharts(document.querySelector("#chart-pie-layanan"), optionsPieLayanan);
            chartPieLayanan.render();

            // Inisialisasi Chart Pie Persentase Pendapatan Berdasarkan Hari
            var detailPerHariData = @json($detailPerHariJson);

            function showDetailHari(hariName) {
                var hint = document.getElementById('detail-hari-hint');
                var wrapper = document.getElementById('detail-hari-wrapper');
                var labelHari = document.getElementById('label-selected-hari');
                var listContainer = document.getElementById('detail-hari-list');

                if (!wrapper || !listContainer) return;

                if (hint) hint.style.display = 'none';
                wrapper.style.display = 'block';
                if (labelHari) labelHari.textContent = hariName;

                var items = detailPerHariData[hariName] || [];
                if (items.length === 0) {
                    listContainer.innerHTML = '<div class="text-center py-3 text-muted" style="font-size: 12px;">Tidak ada data penerimaan pada hari ini.</div>';
                    return;
                }

                var html = '';
                items.forEach(function(item, idx) {
                    var rankColor = (idx === 0) ? '#d97706' : '#64748b';
                    var rankBg = (idx === 0) ? '#fef3c7' : '#f1f5f9';
                    var rankBadge = '<span style="display:inline-flex; align-items:center; justify-content:center; width:28px; height:28px; border-radius:8px; background:' + rankBg + '; color:' + rankColor + '; font-size:12px; font-weight:800;">' + (idx + 1) + '</span>';

                    html += '<div class="card mb-3" style="border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 4px rgba(0,0,0,0.04); background: #ffffff;">';
                    html += '  <div class="card-body p-3">';
                    html += '    <div class="d-flex align-items-start mb-2">';
                    html += '      <div style="flex-shrink: 0; margin-right: 14px; margin-top: 1px;">';
                    html +=          rankBadge;
                    html += '      </div>';
                    html += '      <div style="flex: 1; min-width: 0;">';
                    html += '        <div class="d-flex justify-content-between align-items-baseline">';
                    html += '          <div class="fw-bold text-dark text-truncate pe-2" style="font-size: 14px; line-height: 1.3;">' + (item.nama_layanan || '-') + '</div>';
                    html += '          <div class="fw-bold text-nowrap" style="font-size: 14px; color: #10b981; line-height: 1.3;">Rp ' + Number(item.total_rp).toLocaleString('id-ID') + '</div>';
                    html += '        </div>';
                    html += '        <div class="text-muted text-truncate mt-1" style="font-size: 11.5px; line-height: 1.4;">';
                    html += '          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin me-1" width="15" height="15" viewBox="0 0 24 24" stroke-width="2" stroke="#2A8BF2" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path></svg>' + (item.nama_lokasi || 'UPPD SAMSAT Batulicin');
                    html += '        </div>';
                    html += '      </div>';
                    html += '    </div>';

                    html += '    <div class="pt-2 border-top" style="border-color: #f1f5f9 !important;">';
                    // Baris R2 (Motor)
                    html += '      <div class="d-flex align-items-center justify-content-between p-2 mb-2" style="background: #f8fafc; border-radius: 8px; border: 1px solid #f1f5f9;">';
                    html += '        <div class="d-flex align-items-center">';
                    html += '          <div style="width: 26px; height: 26px; border-radius: 6px; background: rgba(37, 99, 235, 0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">';
                    html += '            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-motorbike" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#2563eb" fill="none" stroke-linecap="round" stroke-linejoin="round">';
                    html += '              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>';
                    html += '              <path d="M5 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>';
                    html += '              <path d="M19 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>';
                    html += '              <path d="M7.5 14h5l4 -4H7.5m4 0h4l2 -3h-3"></path>';
                    html += '            </svg>';
                    html += '          </div>';
                    html += '          <span class="fw-bold text-dark" style="font-size: 12.5px;">Rp ' + Number(item.rp_r2 || 0).toLocaleString('id-ID') + '</span>';
                    html += '        </div>';
                    html += '        <span class="badge" style="background: rgba(37, 99, 235, 0.1); color: #2563eb; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px;">';
                    html += '          ' + Number(item.unit_r2 || 0).toLocaleString('id-ID') + ' Unit';
                    html += '        </span>';
                    html += '      </div>';

                    // Baris R4 (Mobil)
                    html += '      <div class="d-flex align-items-center justify-content-between p-2 mb-2" style="background: #f8fafc; border-radius: 8px; border: 1px solid #f1f5f9;">';
                    html += '        <div class="d-flex align-items-center">';
                    html += '          <div style="width: 26px; height: 26px; border-radius: 6px; background: rgba(8, 145, 178, 0.1); display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">';
                    html += '            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-car" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#0891b2" fill="none" stroke-linecap="round" stroke-linejoin="round">';
                    html += '              <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>';
                    html += '              <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>';
                    html += '              <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>';
                    html += '              <path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6"></path>';
                    html += '              <path d="M7 9l1 3h8l1 -3"></path>';
                    html += '            </svg>';
                    html += '          </div>';
                    html += '          <span class="fw-bold text-dark" style="font-size: 12.5px;">Rp ' + Number(item.rp_r4 || 0).toLocaleString('id-ID') + '</span>';
                    html += '        </div>';
                    html += '        <span class="badge" style="background: rgba(8, 145, 178, 0.1); color: #0891b2; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px;">';
                    html += '          ' + Number(item.unit_r4 || 0).toLocaleString('id-ID') + ' Unit';
                    html += '        </span>';
                    html += '      </div>';

                    // Ringkasan Unit & Notice Dicetak
                    html += '      <div class="row g-2 text-center" style="font-size: 11.5px;">';
                    html += '        <div class="col-6">';
                    html += '          <div class="p-1 px-2 d-flex align-items-center justify-content-center" style="background: #f1f5f9; border-radius: 6px; min-height: 32px;">';
                    html += '            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-gauge text-success me-1" width="15" height="15" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path><path d="M13.41 10.59l2.59 -2.59"></path><path d="M7 12a5 5 0 0 1 5 -5"></path></svg>';
                    html += '            <span class="text-muted me-1">Unit:</span>';
                    html += '            <strong class="text-dark">' + Number(item.total_unit || 0).toLocaleString('id-ID') + '</strong>';
                    html += '          </div>';
                    html += '        </div>';
                    html += '        <div class="col-6">';
                    html += '          <div class="p-1 px-2 d-flex align-items-center justify-content-center" style="background: #f1f5f9; border-radius: 6px; min-height: 32px;">';
                    html += '            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer text-warning me-1" width="15" height="15" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path></svg>';
                    html += '            <span class="text-muted me-1">Notice:</span>';
                    html += '            <strong class="text-dark">' + Number(item.total_notice || item.total_unit || 0).toLocaleString('id-ID') + '</strong>';
                    html += '          </div>';
                    html += '        </div>';
                    html += '      </div>';
                    html += '    </div>';
                    html += '  </div>';
                    html += '</div>';
                });

                listContainer.innerHTML = html;
            }

            var hariLabels = @json($pieHariLabels);
            var hariValues = @json($pieHariValues);

            var optionsPieHari = {
                series: hariValues,
                labels: hariLabels,
                chart: {
                    type: 'pie',
                    height: 380,
                    fontFamily: 'inherit',
                    events: {
                        dataPointSelection: function(event, chartContext, config) {
                            var selectedIndex = config.dataPointIndex;
                            if (selectedIndex !== undefined && hariLabels[selectedIndex]) {
                                showDetailHari(hariLabels[selectedIndex]);
                            }
                        }
                    }
                },
                plotOptions: {
                    pie: {
                        customScale: 1.02,
                        dataLabels: {
                            offset: -5
                        }
                    }
                },
                colors: ['#2A8BF2', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899', '#64748b'],
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val.toFixed(1) + "%";
                    },
                    style: {
                        fontSize: '11px',
                        fontWeight: 700,
                        colors: ['#000000']
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return 'Rp ' + Number(val).toLocaleString('id-ID');
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'left',
                    fontSize: '11px',
                    formatter: function(seriesName, opts) {
                        var val = opts.w.globals.series[opts.seriesIndex];
                        var total = opts.w.globals.series.reduce(function(a, b) { return a + b; }, 0);
                        var percent = total > 0 ? (val / total * 100).toFixed(1) : 0;
                        return seriesName + ': Rp ' + Number(val).toLocaleString('id-ID') + ' (' + percent + '%)';
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 400
                        },
                        legend: {
                            position: 'bottom',
                            horizontalAlign: 'left'
                        }
                    }
                }]
            };

            var chartPieHari = new ApexCharts(document.querySelector("#chart-pie-hari"), optionsPieHari);
            chartPieHari.render();

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
