@extends('layouts.admin.tabler')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <div class="page-pretitle text-muted fw-semibold small text-uppercase">
                        Presensi Layanan
                    </div>
                    <h2 class="page-title fw-bold text-dark">
                        Maintenance Layanan
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <button class="btn btn-primary" id="btnTambahMaintenance">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 5l0 14"></path>
                            <path d="M5 12l14 0"></path>
                        </svg>
                        Tambah Data Maintenance
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    @if (Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                                </div>
                                <div class="ms-2">{{ Session::get('success') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                        </div>
                    @endif

                    @if (Session::get('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <div class="d-flex">
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 9v4"></path><path d="M12 16v.01"></path></svg>
                                </div>
                                <div class="ms-2">{{ Session::get('warning') }}</div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Filter Card -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card card-outline card-primary shadow-sm">
                        <div class="card-body">
                            <form action="/panel-adm14/presensi/maintenance" method="GET" autocomplete="off">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-3 col-6">
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path>
                                                    <path d="M16 3v4"></path>
                                                    <path d="M8 3v4"></path>
                                                    <path d="M4 11h16"></path>
                                                </svg>
                                            </span>
                                            <input type="date" value="{{ Request('dari') }}" id="dari" class="form-control" placeholder="Dari Tanggal" name="dari">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path>
                                                    <path d="M16 3v4"></path>
                                                    <path d="M8 3v4"></path>
                                                    <path d="M4 11h16"></path>
                                                </svg>
                                            </span>
                                            <input type="date" value="{{ Request('sampai') }}" id="sampai" class="form-control" placeholder="Sampai Tanggal" name="sampai">
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-8">
                                        <select name="layanan_id" id="layanan_id" class="form-select">
                                            <option value="">Semua Layanan</option>
                                            @foreach ($daftarLayanan as $l)
                                                <option value="{{ $l->id }}" {{ Request('layanan_id') == $l->id ? 'selected' : '' }}>
                                                    {{ $l->nama_layanan }} (PIC: {{ $l->nama_pic ?: '-' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-4">
                                        <button class="btn btn-primary w-100" type="submit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                                <path d="M21 21l-6 -6"></path>
                                            </svg>
                                            Cari
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Data Maintenance -->
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary shadow-sm mb-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">No.</th>
                                        <th style="width: 140px;">Kode Status</th>
                                        <th style="width: 220px;">Periode Maintenance</th>
                                        <th>Akun Layanan</th>
                                        <th>PIC Layanan</th>
                                        <th>Keterangan Pemeliharaan / Kendala</th>
                                        <th class="text-center" style="width: 130px;">Status Unit</th>
                                        <th style="width: 90px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($maintenance as $d)
                                        <tr>
                                            <td class="text-center text-muted fw-bold">{{ $loop->iteration + $maintenance->firstItem() - 1 }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border">{{ $d->kode_izin }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">
                                                    {{ date('d-m-Y', strtotime($d->tgl_izin_dari)) }}
                                                    @if ($d->tgl_izin_dari != $d->tgl_izin_sampai)
                                                        <span class="text-muted fw-normal">s/d</span>
                                                        {{ date('d-m-Y', strtotime($d->tgl_izin_sampai)) }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $d->nama_layanan }}</div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ $d->nama_pic ?: '-' }}</div>
                                                @if (!empty($d->jabatan_pic))
                                                    <small class="text-muted">{{ $d->jabatan_pic }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-dark">{{ $d->keterangan }}</div>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $today = date('Y-m-d');
                                                    $isAktif = ($d->tgl_izin_dari <= $today && $d->tgl_izin_sampai >= $today);
                                                @endphp
                                                @if ($isAktif)
                                                    <span class="badge bg-warning text-dark px-2 py-1 fw-bold">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-tools me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 21h4l13 -13a1.5 1.5 0 0 0 -4 -4l-13 13v4"></path><path d="M14.5 5.5l4 4"></path><path d="M12 8l-5 -5l-4 4l5 5"></path><path d="M7 8l-1.5 1.5"></path><path d="M16 12l5 5l-4 4l-5 -5"></path><path d="M16 17l-1.5 1.5"></path></svg>
                                                        Maintenance
                                                    </span>
                                                @elseif ($d->tgl_izin_sampai < $today)
                                                    <span class="badge bg-secondary text-white px-2 py-1">Selesai</span>
                                                @else
                                                    <span class="badge bg-info text-white px-2 py-1">Terjadwal</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <form action="/panel-adm14/presensi/maintenance/{{ $d->kode_izin }}/delete" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-danger btn-action delete-confirm" title="Hapus Status Maintenance">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <path d="M4 7l16 0"></path>
                                                            <path d="M10 11l0 6"></path>
                                                            <path d="M14 11l0 6"></path>
                                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4 text-muted">
                                                Belum ada data status maintenance layanan pada periode yang dipilih.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($maintenance->hasPages())
                            <div class="card-footer d-flex align-items-center">
                                {{ $maintenance->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Status Maintenance -->
    <div class="modal fade" id="modal-tambahmaintenance" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Status Maintenance Layanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/panel-adm14/presensi/maintenance/store" method="POST" id="formMaintenance">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Layanan</label>
                            <select name="layanan_id" id="modal_layanan_id" class="form-select" required>
                                <option value="">-- Pilih Akun Layanan --</option>
                                @foreach ($daftarLayanan as $l)
                                    <option value="{{ $l->id }}">{{ $l->nama_layanan }} (PIC: {{ $l->nama_pic ?: '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Mulai Tanggal</label>
                                <input type="date" name="tgl_izin_dari" id="modal_tgl_dari" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-semibold">Sampai Tanggal</label>
                                <input type="date" name="tgl_izin_sampai" id="modal_tgl_sampai" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan Kendala / Pemeliharaan</label>
                            <textarea name="keterangan" id="modal_keterangan" class="form-control" rows="3" placeholder="Contoh: Servis rutin berkala, Perbaikan transmisi / mesin di bengkel, Kendala genset / jaringan armada mobil keliling, dll." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-auto">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2"></path><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path><path d="M14 4l0 4l-6 0l0 -4"></path></svg>
                            Simpan Status Maintenance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
<script>
    $(function() {
        $("#btnTambahMaintenance").click(function() {
            $("#modal-tambahmaintenance").modal("show");
        });

        $(".delete-confirm").click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Status maintenance akan dihapus dan jadwal presensi layanan dipulihkan kembali.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
