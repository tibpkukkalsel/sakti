@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Rekap Penerimaan Harian PKB
                </h2>
                <div class="text-muted mt-1">
                    Monitoring penerimaan Pajak Kendaraan Bermotor (PKB) harian dari seluruh akun layanan UPPD SAMSAT Batu Licin.
                </div>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="/panel-adm14/penerimaan/cetak?tgl_dari={{ $tgl_dari }}&tgl_sampai={{ $tgl_sampai }}&layanan_id={{ $layanan_id }}"
                       target="_blank" class="btn btn-outline-secondary">
                        <i class="bi bi-printer me-1"></i> Cetak Rekap
                    </a>
                    <button type="button" class="btn btn-primary" id="btnTambahPenerimaan">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Rekap Manual
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        @if (Session::get('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ Session::get('success') }}
            </div>
        @endif
        @if (Session::get('warning'))
            <div class="alert alert-warning alert-dismissible" role="alert">
                {{ Session::get('warning') }}
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="row row-deck row-cards mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm bg-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-white text-primary avatar">
                                    <i class="bi bi-cash-stack fs-3"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">TOTAL PENERIMAAN (RP)</div>
                                <div class="text-white fs-4 fw-bold">Rp {{ number_format($summary->sum_total_rp ?? 0, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar">
                                    <i class="bi bi-bicycle fs-3"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted" style="font-size: 11px; text-transform: uppercase;">PENERIMAAN R2</div>
                                <div class="fw-bold fs-5 text-success">Rp {{ number_format($summary->sum_rp_r2 ?? 0, 0, ',', '.') }}</div>
                                <div class="text-muted small">{{ number_format($summary->sum_unit_r2 ?? 0, 0, ',', '.') }} Unit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-info text-white avatar">
                                    <i class="bi bi-car-front-fill fs-3"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted" style="font-size: 11px; text-transform: uppercase;">PENERIMAAN R4</div>
                                <div class="fw-bold fs-5 text-info">Rp {{ number_format($summary->sum_rp_r4 ?? 0, 0, ',', '.') }}</div>
                                <div class="text-muted small">{{ number_format($summary->sum_unit_r4 ?? 0, 0, ',', '.') }} Unit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <i class="bi bi-file-earmark-text fs-3"></i>
                                </span>
                            </div>
                            <div class="col">
                                <div class="text-muted" style="font-size: 11px; text-transform: uppercase;">NOTICE & TOTAL UNIT</div>
                                <div class="fw-bold fs-5 text-dark">{{ number_format($summary->sum_notice ?? 0, 0, ',', '.') }} Notice</div>
                                <div class="text-muted small">Total: {{ number_format($summary->sum_total_unit ?? 0, 0, ',', '.') }} Unit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="/panel-adm14/penerimaan" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Dari Tanggal</label>
                            <input type="date" name="tgl_dari" class="form-control" value="{{ $tgl_dari }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold">Sampai Tanggal</label>
                            <input type="date" name="tgl_sampai" class="form-control" value="{{ $tgl_sampai }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Akun Layanan</label>
                            <select name="layanan_id" class="form-select">
                                <option value="">-- Semua Layanan --</option>
                                @foreach ($daftarLayanan as $l)
                                    <option value="{{ $l->id }}" {{ $layanan_id == $l->id ? 'selected' : '' }}>
                                        {{ $l->nama_layanan }} (PIC: {{ $l->nama_pic }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-filter me-1"></i> Terapkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table of Records -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-vcenter table-bordered table-striped table-hover mb-0">
                    <thead class="table-dark">
                        <tr class="text-center">
                            <th style="width: 40px;">No</th>
                            <th>Tanggal</th>
                            <th>Layanan & PIC</th>
                            <th>Roda 2 (R2)</th>
                            <th>Roda 4 (R4)</th>
                            <th>Total Penerimaan</th>
                            <th>Total Unit</th>
                            <th>Notice Dicetak</th>
                            <th style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($penerimaan as $d)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + $penerimaan->firstItem() - 1 }}</td>
                                <td class="text-nowrap">
                                    <strong>{{ \Carbon\Carbon::parse($d->tgl_penerimaan)->translatedFormat('d-m-Y') }}</strong>
                                    <small class="text-muted d-block">{{ \Carbon\Carbon::parse($d->tgl_penerimaan)->translatedFormat('l') }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $d->layanan->nama_layanan ?? '-' }}</div>
                                    <small class="text-muted">PIC: {{ $d->layanan->nama_pic ?? '-' }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-success">Rp {{ number_format($d->jumlah_rp_r2, 0, ',', '.') }}</div>
                                    <small class="text-muted">{{ $d->jumlah_unit_r2 }} unit</small>
                                </td>
                                <td class="text-end">
                                    <div class="fw-bold text-info">Rp {{ number_format($d->jumlah_rp_r4, 0, ',', '.') }}</div>
                                    <small class="text-muted">{{ $d->jumlah_unit_r4 }} unit</small>
                                </td>
                                <td class="text-end fw-bold text-dark fs-6 bg-light">
                                    Rp {{ number_format($d->total_rp, 0, ',', '.') }}
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-lt fw-bold">{{ $d->total_unit }} Unit</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning-lt fw-bold">{{ $d->jumlah_notice }} Lembar</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-info editPenerimaan" data-id="{{ $d->id }}" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="/panel-adm14/penerimaan/{{ $d->id }}/delete" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data penerimaan ini?')" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" title="Hapus">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data penerimaan pada rentang tanggal atau filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-end py-2">
                {{ $penerimaan->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Penerimaan Admin -->
<div class="modal modal-blur fade" id="modal-tambahpenerimaan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-1 text-primary"></i> Input Rekap Penerimaan PKB
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/panel-adm14/penerimaan/store" method="POST" id="formAdminPenerimaan">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Akun Layanan</label>
                            <select name="layanan_id" class="form-select" required>
                                <option value="">-- Pilih Layanan --</option>
                                @foreach ($daftarLayanan as $l)
                                    <option value="{{ $l->id }}">{{ $l->nama_layanan }} (PIC: {{ $l->nama_pic }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Tanggal Penerimaan</label>
                            <input type="date" name="tgl_penerimaan" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Penerimaan Roda 2 / R2 (Rp)</label>
                            <input type="text" name="jumlah_rp_r2" id="admin_rp_r2" class="form-control rupiah-admin" placeholder="0" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required">Penerimaan Roda 4 / R4 (Rp)</label>
                            <input type="text" name="jumlah_rp_r4" id="admin_rp_r4" class="form-control rupiah-admin" placeholder="0" required>
                        </div>

                        <div class="col-12">
                            <div class="p-3 bg-light rounded border text-center">
                                <div class="text-muted small">TOTAL PENERIMAAN (R2 + R4)</div>
                                <h3 class="text-success fw-bold mb-0" id="admin_preview_total_rp">Rp 0</h3>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required">Jumlah Unit R2</label>
                            <input type="number" name="jumlah_unit_r2" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Jumlah Unit R4</label>
                            <input type="number" name="jumlah_unit_r4" class="form-control" value="0" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Jumlah Notice Dicetak</label>
                            <input type="number" name="jumlah_notice" class="form-control" value="0" min="0" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Keterangan (Opsional)</label>
                            <textarea name="keterangan" class="form-control" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Penerimaan Admin -->
<div class="modal modal-blur fade" id="modal-editpenerimaan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-1 text-primary"></i> Edit Rekap Penerimaan PKB
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="loadeditpenerimaan">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script>
    function formatRupiahAdmin(angka) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    function hitungTotalAdmin() {
        var r2 = $('#admin_rp_r2').val().replace(/[^0-9]/g, '') || 0;
        var r4 = $('#admin_rp_r4').val().replace(/[^0-9]/g, '') || 0;
        var total = parseInt(r2) + parseInt(r4);
        $('#admin_preview_total_rp').text('Rp ' + formatRupiahAdmin(total.toString()));
    }

    $(document).ready(function() {
        $("#btnTambahPenerimaan").click(function(e) {
            e.preventDefault();
            $("#modal-tambahpenerimaan").modal("show");
        });

        $(".rupiah-admin").on('keyup', function() {
            $(this).val(formatRupiahAdmin($(this).val()));
            hitungTotalAdmin();
        });

        $(".editPenerimaan").click(function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $("#modal-editpenerimaan").modal("show");
            $("#loadeditpenerimaan").html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
            $.ajax({
                type: 'POST',
                url: '/panel-adm14/penerimaan/edit',
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(respond) {
                    $("#loadeditpenerimaan").html(respond);
                },
                error: function() {
                    $("#loadeditpenerimaan").html('<div class="alert alert-danger">Gagal memuat formulir edit.</div>');
                }
            });
        });
    });
</script>
@endpush
