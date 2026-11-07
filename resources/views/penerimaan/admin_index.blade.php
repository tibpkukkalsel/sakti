@extends('layouts.admin.tabler')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Rekap Penerimaan PKB
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <!-- Summary Cards -->
            <div class="row row-deck row-cards mb-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-coin"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                            <path
                                                d="M14.8 9a2 2 0 0 0 -1.8 -1h-2a2 2 0 1 0 0 4h2a2 2 0 1 1 0 4h-2a2 2 0 0 1 -1.8 -1">
                                            </path>
                                            <path d="M12 7v10"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Total Penerimaan</div>
                                    <div class="text-muted">Rp {{ number_format($summary->sum_total_rp ?? 0, 0, ',', '.') }}
                                    </div>
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
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-motorbike" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M5 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                            <path d="M19 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                            <path d="M7.5 14h5l4 -4H7.5m4 0h4l2 -3h-3"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Penerimaan R2</div>
                                    <div class="text-muted">Rp {{ number_format($summary->sum_rp_r2 ?? 0, 0, ',', '.') }}
                                        ({{ number_format($summary->sum_unit_r2 ?? 0, 0, ',', '.') }} Unit)</div>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-car"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6"></path>
                                            <path d="M7 9l1 3h8l1 -3"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Penerimaan R4</div>
                                    <div class="text-muted">Rp {{ number_format($summary->sum_rp_r4 ?? 0, 0, ',', '.') }}
                                        ({{ number_format($summary->sum_unit_r4 ?? 0, 0, ',', '.') }} Unit)</div>
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
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2">
                                            </path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">Notice Dicetak</div>
                                    <div class="text-muted">{{ number_format($summary->sum_notice ?? 0, 0, ',', '.') }}
                                        Notice ({{ number_format($summary->sum_total_unit ?? 0, 0, ',', '.') }} Unit)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    @if (Session::get('success'))
                                        <div class="alert alert-success">
                                            {{ Session::get('success') }}
                                        </div>
                                    @endif

                                    @if (Session::get('warning'))
                                        <div class="alert alert-warning">
                                            {{ Session::get('warning') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 d-flex">
                                    <a href="#" class="btn btn-primary" id="btnTambahPenerimaan">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 5l0 14"></path>
                                            <path d="M5 12l14 0"></path>
                                        </svg>
                                        Tambah Data
                                    </a>
                                    <form action="/panel-adm14/penerimaan/bulk-lock" method="POST" class="d-inline" style="margin-left: 10px;" onsubmit="return confirm('Kunci semua data penerimaan pada periode filter ini? User layanan tidak dapat mengubah atau menghapus data yang terkunci.')">
                                        @csrf
                                        <input type="hidden" name="lock_action" value="lock">
                                        <input type="hidden" name="tgl_dari" value="{{ $tgl_dari }}">
                                        <input type="hidden" name="tgl_sampai" value="{{ $tgl_sampai }}">
                                        <input type="hidden" name="layanan_id" value="{{ $layanan_id }}">
                                        <button type="submit" class="btn btn-warning">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>
                                                <path d="M8 11v-4a4 4 0 0 1 8 0v4"></path>
                                            </svg>
                                            Kunci Semua Data Filter
                                        </button>
                                    </form>
                                    <form action="/panel-adm14/penerimaan/bulk-lock" method="POST" class="d-inline" style="margin-left: 5px;" onsubmit="return confirm('Buka kunci semua data penerimaan pada periode filter ini?')">
                                        @csrf
                                        <input type="hidden" name="lock_action" value="unlock">
                                        <input type="hidden" name="tgl_dari" value="{{ $tgl_dari }}">
                                        <input type="hidden" name="tgl_sampai" value="{{ $tgl_sampai }}">
                                        <input type="hidden" name="layanan_id" value="{{ $layanan_id }}">
                                        <button type="submit" class="btn btn-outline-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock-open" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>
                                                <path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                <path d="M8 11v-5a4 4 0 0 1 8 0"></path>
                                            </svg>
                                            Buka Kunci Filter
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <form action="/panel-adm14/penerimaan" method="GET">
                                        <div class="row">
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <input type="date" name="tgl_dari" id="tgl_dari"
                                                        class="form-control" value="{{ $tgl_dari }}">
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="form-group">
                                                    <input type="date" name="tgl_sampai" id="tgl_sampai"
                                                        class="form-control" value="{{ $tgl_sampai }}">
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="form-group">
                                                    <select name="layanan_id" id="filter_layanan_id" class="form-select">
                                                        <option value="">Semua Layanan</option>
                                                        @foreach ($daftarLayanan as $l)
                                                            <option value="{{ $l->id }}"
                                                                {{ $layanan_id == $l->id ? 'selected' : '' }}>
                                                                {{ $l->nama_layanan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <button type="submit" class="btn btn-primary w-100">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="icon icon-tabler icon-tabler-search" width="24"
                                                            height="24" viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                                            <path d="M21 21l-6 -6"></path>
                                                        </svg>
                                                        Cari
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row mt-2" id="containerTablePenerimaan">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover align-middle">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 40px;">No</th>
                                                    <th>Tanggal</th>
                                                    <th>Layanan</th>
                                                    <th>Roda 2 (R2)</th>
                                                    <th>Roda 4 (R4)</th>
                                                    <th>Total Penerimaan</th>
                                                    <th class="text-center">Total Unit</th>
                                                    <th class="text-center">Notice Dicetak</th>
                                                    <th class="text-center" style="width: 100px;">Status</th>
                                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($penerimaan as $d)
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $loop->iteration + $penerimaan->firstItem() - 1 }}</td>
                                                        <td>
                                                            <strong>{{ date('d-m-Y', strtotime($d->tgl_penerimaan)) }}</strong>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $d->layanan->nama_layanan ?? '-' }}</strong>

                                                        </td>
                                                        <td class="text-end">
                                                            <div class="text-success font-weight-bold">Rp
                                                                {{ number_format($d->jumlah_rp_r2, 0, ',', '.') }}</div>
                                                            <div class="text-muted small">{{ $d->jumlah_unit_r2 }} unit
                                                            </div>
                                                        </td>
                                                        <td class="text-end">
                                                            <div class="text-info font-weight-bold">Rp
                                                                {{ number_format($d->jumlah_rp_r4, 0, ',', '.') }}</div>
                                                            <div class="text-muted small">{{ $d->jumlah_unit_r4 }} unit
                                                            </div>
                                                        </td>
                                                        <td class="text-end font-weight-bold">
                                                            Rp {{ number_format($d->total_rp, 0, ',', '.') }}
                                                        </td>
                                                        <td class="text-center">
                                                            <span
                                                                class="badge bg-light text-dark border">{{ $d->total_unit }}
                                                                Unit</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span
                                                                class="badge bg-warning text-dark border">{{ $d->jumlah_notice }}
                                                                Lembar</span>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($d->is_locked)
                                                                <span class="badge bg-warning text-dark d-inline-flex align-items-center gap-1" title="Data dikunci - User tidak dapat mengedit/menghapus">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                        <path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>
                                                                        <path d="M8 11v-4a4 4 0 0 1 8 0v4"></path>
                                                                    </svg>
                                                                    Terkunci
                                                                </span>
                                                            @else
                                                                <span class="badge bg-success text-white d-inline-flex align-items-center gap-1" title="Data terbuka - User dapat mengedit">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock-open" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                        <path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>
                                                                        <path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                                        <path d="M8 11v-5a4 4 0 0 1 8 0"></path>
                                                                    </svg>
                                                                    Terbuka
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex justify-content-center align-items-center">
                                                                <!-- Button Kunci / Buka Kunci -->
                                                                <form action="/panel-adm14/penerimaan/{{ $d->id }}/toggle-lock" method="POST" class="d-inline" style="margin-right: 5px;">
                                                                    @csrf
                                                                    @if ($d->is_locked)
                                                                        <button type="submit" class="btn btn-warning btn-sm" title="Data Terkunci (Klik untuk Buka Kunci)">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                                <path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>
                                                                                <path d="M8 11v-4a4 4 0 0 1 8 0v4"></path>
                                                                            </svg>
                                                                        </button>
                                                                    @else
                                                                        <button type="submit" class="btn btn-outline-secondary btn-sm" title="Data Terbuka (Klik untuk Kunci Data)">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock-open" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                                <path d="M5 11m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>
                                                                                <path d="M12 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path>
                                                                                <path d="M8 11v-5a4 4 0 0 1 8 0"></path>
                                                                            </svg>
                                                                        </button>
                                                                    @endif
                                                                </form>

                                                                <a href="#" class="edit btn btn-info btn-sm"
                                                                    id_penerimaan="{{ $d->id }}" title="Edit Data">
                                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                                        class="icon icon-tabler icon-tabler-edit"
                                                                        width="18" height="18" viewBox="0 0 24 24"
                                                                        stroke-width="2" stroke="currentColor"
                                                                        fill="none" stroke-linecap="round"
                                                                        stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z"
                                                                            fill="none"></path>
                                                                        <path
                                                                            d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3">
                                                                        </path>
                                                                        <path
                                                                            d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3">
                                                                        </path>
                                                                        <path d="M16 5l3 3"></path>
                                                                    </svg>
                                                                </a>
                                                                <form
                                                                    action="/panel-adm14/penerimaan/{{ $d->id }}/delete"
                                                                    method="POST" style="margin-left:5px">
                                                                    @csrf
                                                                    <a class="btn btn-danger btn-sm delete-confirm" title="Hapus Data">
                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                            class="icon icon-tabler icon-tabler-trash"
                                                                            width="18" height="18"
                                                                            viewBox="0 0 24 24" stroke-width="2"
                                                                            stroke="currentColor" fill="none"
                                                                            stroke-linecap="round"
                                                                            stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none"></path>
                                                                            <path d="M4 7l16 0"></path>
                                                                            <path d="M10 11l0 6"></path>
                                                                            <path d="M14 11l0 6"></path>
                                                                            <path
                                                                                d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12">
                                                                            </path>
                                                                            <path
                                                                                d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3">
                                                                            </path>
                                                                        </svg>
                                                                    </a>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="10" class="text-center py-4 text-muted">
                                                            Tidak ada data penerimaan pada rentang tanggal atau filter yang
                                                            dipilih.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        {{ $penerimaan->links('vendor.pagination.bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Penerimaan --}}
    <div class="modal modal-blur fade" id="modal-inputpenerimaan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Rekap Penerimaan PKB</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/panel-adm14/penerimaan/store" method="POST" id="frmPenerimaan">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <select name="layanan_id" id="layanan_id" class="form-select mb-3">
                                    <option value="">Pilih Layanan</option>
                                    @foreach ($daftarLayanan as $l)
                                        <option value="{{ $l->id }}">{{ $l->nama_layanan }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-calendar" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z">
                                            </path>
                                            <path d="M16 3v4"></path>
                                            <path d="M8 3v4"></path>
                                            <path d="M4 11h16"></path>
                                        </svg>
                                    </span>
                                    <input type="date" id="tgl_penerimaan" class="form-control" name="tgl_penerimaan"
                                        value="{{ date('Y-m-d') }}" placeholder="Tanggal Penerimaan">
                                </div>
                            </div>
                        </div>
                        <!-- Baris Roda 2 (R2): Penerimaan Rp & Jumlah Unit Berdampingan -->
                        <div class="row">
                            <div class="col-7">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-motorbike" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M5 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                            <path d="M19 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                                            <path d="M7.5 14h5l4 -4H7.5m4 0h4l2 -3h-3"></path>
                                        </svg>
                                    </span>
                                    <input type="text" id="admin_rp_r2" class="form-control rupiah-admin"
                                        name="jumlah_rp_r2" placeholder="Penerimaan R2 (Rp)" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-hash"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M5 9l14 0"></path>
                                            <path d="M5 15l14 0"></path>
                                            <path d="M11 4l-4 16"></path>
                                            <path d="M17 4l-4 16"></path>
                                        </svg>
                                    </span>
                                    <input type="number" id="jumlah_unit_r2" class="form-control" name="jumlah_unit_r2"
                                        placeholder="Unit R2" min="0">
                                </div>
                            </div>
                        </div>
                        <!-- Baris Roda 4 (R4): Penerimaan Rp & Jumlah Unit Berdampingan -->
                        <div class="row">
                            <div class="col-7">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-car"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                                            <path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6"></path>
                                            <path d="M7 9l1 3h8l1 -3"></path>
                                        </svg>
                                    </span>
                                    <input type="text" id="admin_rp_r4" class="form-control rupiah-admin"
                                        name="jumlah_rp_r4" placeholder="Penerimaan R4 (Rp)" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-hash"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M5 9l14 0"></path>
                                            <path d="M5 15l14 0"></path>
                                            <path d="M11 4l-4 16"></path>
                                            <path d="M17 4l-4 16"></path>
                                        </svg>
                                    </span>
                                    <input type="number" id="jumlah_unit_r4" class="form-control" name="jumlah_unit_r4"
                                        placeholder="Unit R4" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="p-2 bg-light border rounded text-center">
                                    <small class="text-muted">Total Estimasi Penerimaan</small>
                                    <div class="font-weight-bold text-success fs-3" id="admin_preview_total_rp">Rp 0</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                            class="icon icon-tabler icon-tabler-receipt" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2">
                                            </path>
                                        </svg>
                                    </span>
                                    <input type="number" id="jumlah_notice" class="form-control" name="jumlah_notice"
                                        placeholder="Jumlah Notice Dicetak" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-icon mb-3">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-notes"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path
                                                d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z">
                                            </path>
                                            <path d="M9 7l6 0"></path>
                                            <path d="M9 11l6 0"></path>
                                            <path d="M9 15l4 0"></path>
                                        </svg>
                                    </span>
                                    <input type="text" id="keterangan" class="form-control" name="keterangan"
                                        placeholder="Keterangan (Opsional)">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="is_locked" value="1">
                                    <span class="form-check-label fw-bold">Kunci Data Ini (User Layanan Tidak Dapat Mengedit/Menghapus)</span>
                                </label>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="form-group">
                                    <button class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send"
                                            width="24" height="24" viewBox="0 0 24 24" stroke-width="2"
                                            stroke="currentColor" fill="none" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M10 14l11 -11"></path>
                                            <path
                                                d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5">
                                            </path>
                                        </svg>
                                        Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit Penerimaan --}}
    <div class="modal modal-blur fade" id="modal-editpenerimaan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Rekap Penerimaan PKB</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="loadeditform">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        function formatRupiah(angka) {
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
            $('#admin_preview_total_rp').text('Rp ' + formatRupiah(total.toString()));
        }

        $(function() {
            $("#btnTambahPenerimaan").click(function() {
                $("#frmPenerimaan")[0].reset();
                $("#admin_rp_r2").val("");
                $("#admin_rp_r4").val("");
                $("#jumlah_unit_r2").val("");
                $("#jumlah_unit_r4").val("");
                $("#jumlah_notice").val("");
                $("#admin_preview_total_rp").text("Rp 0");
                $("#modal-inputpenerimaan").modal("show");
            });

            $(".rupiah-admin").on('keyup', function() {
                $(this).val(formatRupiah($(this).val()));
                hitungTotalAdmin();
            });

            $(document).on('click', '.edit', function(e) {
                e.preventDefault();
                var id = $(this).attr('id_penerimaan');
                $.ajax({
                    type: 'POST',
                    url: '/panel-adm14/penerimaan/edit',
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    success: function(respond) {
                        $("#loadeditform").html(respond);
                        $("#modal-editpenerimaan").modal("show");
                    }
                });
            });

            $(document).on('click', '.delete-confirm', function(e) {
                var form = $(this).closest('form');
                e.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda Yakin Data Ini Mau Di Hapus ?',
                    text: "Jika Ya Maka Data Akan Terhapus Permanent",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Hapus Saja!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            // AJAX Pagination untuk Tabel Rekap Penerimaan Admin
            function loadAdminTable(url, pushState) {
                if (pushState === undefined) pushState = true;
                var container = document.getElementById('containerTablePenerimaan');
                if (!container) return;

                $('#containerTablePenerimaan').css({
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
                            var newElem = doc.getElementById('containerTablePenerimaan');

                            if (newElem) {
                                container.innerHTML = newElem.innerHTML;
                                $('#containerTablePenerimaan').css({
                                    'opacity': '1',
                                    'pointer-events': 'auto'
                                });
                                if (pushState && window.history && window.history.pushState) {
                                    window.history.pushState({ path: url }, '', url);
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

            $(document).on('click', '#containerTablePenerimaan .pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                if (!url || url === '#' || url.indexOf('javascript') === 0) return;
                loadAdminTable(url, true);
            });

            window.addEventListener('popstate', function() {
                if (window.location.pathname.indexOf('/penerimaan') !== -1) {
                    loadAdminTable(window.location.href, false);
                }
            });

            $("#frmPenerimaan").submit(function() {
                var layanan_id = $("#layanan_id").val();
                var tgl_penerimaan = $("#tgl_penerimaan").val();
                var admin_rp_r2 = $("#admin_rp_r2").val();
                var admin_rp_r4 = $("#admin_rp_r4").val();

                if (layanan_id == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Layanan Harus Dipilih !',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        $("#layanan_id").focus();
                    });
                    return false;
                } else if (tgl_penerimaan == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Tanggal Penerimaan Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        $("#tgl_penerimaan").focus();
                    });
                    return false;
                } else if (admin_rp_r2 == "" && admin_rp_r4 == "") {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Penerimaan R2 atau R4 Harus Diisi !',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        $("#admin_rp_r2").focus();
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
