@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle text-muted fw-semibold small text-uppercase">
                    Penerimaan Pajak Kendaraan Bermotor
                </div>
                <h2 class="page-title fw-bold text-dark">
                    Rekap Penerimaan Per Lokasi
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="/panel-adm14/home" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Home
                </a>
                <a href="/panel-adm14/dashboard-penerimaan" class="btn btn-primary">
                    <i class="bi bi-graph-up-arrow me-1"></i> Dashboard PKB
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        <!-- Filter Form -->
        <div class="card card-outline card-success shadow-sm mb-3">
            <div class="card-body">
                <form action="/panel-adm14/penerimaan/rekap-lokasi" method="GET" id="formFilter" autocomplete="off">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4 col-sm-6 col-12">
                            <label class="form-label fw-bold small text-muted">Pilih Lokasi Pelayanan:</label>
                            <select name="kode_cabang" class="form-select">
                                <option value="all">-- Semua Lokasi Pelayanan --</option>
                                @foreach ($daftarLokasi as $lok)
                                    <option value="{{ $lok->kode_cabang }}" {{ $kode_cabang == $lok->kode_cabang ? 'selected' : '' }}>
                                        {{ $lok->nama_cabang }} ({{ $lok->kode_cabang }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-6 col-12">
                            <label class="form-label fw-bold small text-muted">Periode Waktu:</label>
                            <select name="periode" id="selectPeriode" class="form-select">
                                <option value="bulan_ini" {{ $periode == 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="hari_ini" {{ $periode == 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                                <option value="tahun_ini" {{ $periode == 'tahun_ini' ? 'selected' : '' }}>Tahun Ini</option>
                                <option value="custom" {{ $periode == 'custom' ? 'selected' : '' }}>Kustom Tanggal</option>
                            </select>
                        </div>
                        <div class="col-md-3 col-sm-8 col-12 custom-date-box" style="{{ $periode == 'custom' ? '' : 'display: none;' }}">
                            <label class="form-label fw-bold small text-muted">Rentang Tanggal:</label>
                            <div class="input-group">
                                <input type="date" name="tgl_dari" class="form-control" value="{{ $tgl_dari }}">
                                <span class="input-group-text">s/d</span>
                                <input type="date" name="tgl_sampai" class="form-control" value="{{ $tgl_sampai }}">
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-4 col-12 ms-auto">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-filter me-1"></i> Filter
                                </button>
                                <a href="/panel-adm14/penerimaan/rekap-lokasi" class="btn btn-outline-secondary" title="Reset Filter">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Banner Periode Aktif -->
        <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center justify-content-between" style="border-radius: 8px;">
            <div>
                <i class="bi bi-info-circle-fill me-1"></i>
                Menampilkan data rekap untuk periode: <strong>{{ $periodeLabel }}</strong>
                @if ($lokasiTerpilih)
                    | Filter Lokasi: <span class="badge bg-primary text-white">{{ $lokasiTerpilih->nama_cabang }}</span>
                @else
                    | Filter Lokasi: <span class="badge bg-secondary text-white">Semua Titik Lokasi</span>
                @endif
            </div>
            <span class="text-muted small">Total Data: {{ count($rekapLokasi) }} Titik Lokasi</span>
        </div>

        <!-- Summary Cards -->
        <div class="row row-cards mb-3">
            <div class="col-md-3 col-sm-6 col-12 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-success text-white">
                                    <i class="bi bi-cash-stack fs-3"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-dark fw-bold fs-4">
                                    Rp {{ number_format($grandTotal->total_rp, 0, ',', '.') }}
                                </div>
                                <div class="text-muted small">Total Penerimaan PKB</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-primary text-white">
                                    <i class="bi bi-bicycle fs-3"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-dark fw-bold fs-4">
                                    Rp {{ number_format($grandTotal->rp_r2, 0, ',', '.') }}
                                </div>
                                <div class="text-muted small">R2: {{ number_format($grandTotal->unit_r2) }} Unit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-warning text-dark">
                                    <i class="bi bi-car-front fs-3"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-dark fw-bold fs-4">
                                    Rp {{ number_format($grandTotal->rp_r4, 0, ',', '.') }}
                                </div>
                                <div class="text-muted small">R4: {{ number_format($grandTotal->unit_r4) }} Unit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6 col-12 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="avatar bg-info text-white">
                                    <i class="bi bi-receipt fs-3"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-dark fw-bold fs-4">
                                    {{ number_format($grandTotal->total_notice) }}
                                </div>
                                <div class="text-muted small">Notice Pajak Dicetak (Lbr)</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- GRAFIK PENERIMAAN PER LOKASI -->
        <div class="card card-outline card-success shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-bar-chart-fill text-success me-2"></i>
                        @if ($lokasiTerpilih)
                            Grafik Penerimaan: {{ $lokasiTerpilih->nama_cabang }}
                        @else
                            Grafik Perbandingan Penerimaan Antar Titik Lokasi
                        @endif
                    </h3>
                    <small class="text-muted">Peringkat perolehan penerimaan Pajak Kendaraan Bermotor per lokasi pelayanan.</small>
                </div>
            </div>
            <div class="card-body">
                @if (count($chartLabels) > 0)
                    <div id="chart-rekap-lokasi" style="min-height: 350px;"></div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bar-chart fs-1 mb-2 d-block"></i>
                        Tidak ada data penerimaan untuk ditampilkan pada grafik dalam periode ini.
                    </div>
                @endif
            </div>
        </div>

        <!-- TABEL REKAP PENERIMAAN PER LOKASI -->
        <div class="card card-outline card-success shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-table text-primary me-2"></i>
                        Data Tabel Rekap Penerimaan PKB Per Titik Lokasi
                    </h3>
                    <small class="text-muted">Rincian perolehan penerimaan R2, R4, unit kendaraan, notice pajak dan persentase kontribusi.</small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr class="text-center align-middle">
                            <th style="width: 50px;">No</th>
                            <th style="min-width: 180px;" class="text-start">Titik Lokasi Pelayanan</th>
                            <th style="min-width: 140px;">Penerimaan R2 (Rp)</th>
                            <th style="width: 90px;">Unit R2</th>
                            <th style="min-width: 140px;">Penerimaan R4 (Rp)</th>
                            <th style="width: 90px;">Unit R4</th>
                            <th style="min-width: 160px;" class="table-light text-dark fw-bold">Total Penerimaan (Rp)</th>
                            <th style="width: 90px;">Total Unit</th>
                            <th style="width: 100px;">Notice (Lbr)</th>
                            <th style="width: 90px;">Kontribusi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rekapLokasi as $row)
                        @php
                            $persen = $grandTotal->total_rp > 0 ? ($row->total_rp / $grandTotal->total_rp) * 100 : 0;
                        @endphp
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $loop->iteration }}</td>
                            <td>
                                <strong class="text-dark d-block">{{ $row->nama_cabang }}</strong>
                                <span class="text-muted small">Kode: {{ $row->kode_cabang }}</span>
                            </td>
                            <td class="text-end">Rp {{ number_format($row->rp_r2, 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($row->unit_r2) }}</td>
                            <td class="text-end">Rp {{ number_format($row->rp_r4, 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($row->unit_r4) }}</td>
                            <td class="text-end fw-bold text-success">
                                Rp {{ number_format($row->total_rp, 0, ',', '.') }}
                            </td>
                            <td class="text-center fw-bold">{{ number_format($row->total_unit) }}</td>
                            <td class="text-center">{{ number_format($row->total_notice) }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary fw-bold">{{ number_format($persen, 1) }}%</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                Tidak ada data penerimaan untuk lokasi dan periode yang dipilih.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light fw-bold text-dark" style="border-top: 2px solid #dee2e6;">
                        <tr class="align-middle">
                            <td colspan="2" class="text-center text-dark fw-bold">TOTAL KESELURUHAN</td>
                            <td class="text-end text-dark">Rp {{ number_format($grandTotal->rp_r2, 0, ',', '.') }}</td>
                            <td class="text-center text-dark">{{ number_format($grandTotal->unit_r2) }}</td>
                            <td class="text-end text-dark">Rp {{ number_format($grandTotal->rp_r4, 0, ',', '.') }}</td>
                            <td class="text-center text-dark">{{ number_format($grandTotal->unit_r4) }}</td>
                            <td class="text-end text-success fw-bold fs-6">
                                Rp {{ number_format($grandTotal->total_rp, 0, ',', '.') }}
                            </td>
                            <td class="text-center text-dark">{{ number_format($grandTotal->total_unit) }}</td>
                            <td class="text-center text-dark">{{ number_format($grandTotal->total_notice) }}</td>
                            <td class="text-center text-dark">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>

@push('myscript')
<script src="{{ asset('tabler/dist/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var selectPeriode = document.getElementById("selectPeriode");
        var customDateBox = document.querySelector(".custom-date-box");

        if (selectPeriode && customDateBox) {
            selectPeriode.addEventListener("change", function () {
                if (this.value === "custom") {
                    customDateBox.style.display = "";
                } else {
                    customDateBox.style.display = "none";
                }
            });
        }

        @if (count($chartLabels) > 0)
        var optionsChart = {
            series: [{
                name: 'Total Penerimaan (Rp)',
                data: @json($chartValuesTotal)
            }],
            chart: {
                type: 'bar',
                height: 350,
                toolbar: { show: true }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: true,
                    distributed: false,
                    dataLabels: { position: 'bottom' }
                }
            },
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: { colors: ['#fff'] },
                formatter: function (val) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                },
                offsetX: 0,
                dropShadow: { enabled: true }
            },
            colors: ['#059669'],
            xaxis: {
                categories: @json($chartLabels),
                labels: {
                    formatter: function (val) {
                        if (val >= 1000000000) return (val / 1000000000).toFixed(1) + ' M';
                        if (val >= 1000000) return (val / 1000000).toFixed(1) + ' Jt';
                        if (val >= 1000) return (val / 1000).toFixed(0) + ' Rb';
                        return val;
                    }
                }
            },
            yaxis: {
                labels: {
                    style: { fontSize: '13px', fontWeight: 600 }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart-rekap-lokasi"), optionsChart);
        chart.render();
        @endif
    });
</script>
@endpush
@endsection
