@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Data Lokasi
                </h2>
            </div>
        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
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
                            <div class="col-12">
                                <a href="#" class="btn btn-primary" id="btnTambahLokasi">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 5l0 14"></path>
                                        <path d="M5 12l14 0"></path>
                                    </svg>
                                    Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <form action="/panel-adm14/lokasi" method="GET">
                                    <div class="row g-2">
                                        <div class="col-md-9 col-12">
                                            <div class="form-group">
                                                <input type="text" name="nama_cabang" id="cari_nama_cabang" class="form-control" placeholder="Nama Lokasi / Koordinat" value="{{ Request('nama_cabang') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary flex-fill">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                                    <path d="M21 21l-6 -6"></path>
                                                </svg>
                                                Cari
                                            </button>
                                            @if (Request('nama_cabang'))
                                            <a href="/panel-adm14/lokasi" class="btn btn-outline-secondary">Reset</a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 60px;" class="text-center">No</th>
                                                <th>Nama Lokasi</th>
                                                <th>Titik Koordinat</th>
                                                <th style="width: 130px;">Radius</th>
                                                <th style="width: 100px;" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($cabang as $d)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration + $cabang->firstItem() - 1 }}</td>
                                                <td class="fw-bold">{{ $d->nama_cabang }}</td>
                                                <td><code>{{ $d->lokasi_cabang }}</code></td>
                                                <td><span class="badge bg-info text-white">{{ $d->radius_cabang }} Meter</span></td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <a href="#" class="edit btn btn-info btn-sm" data-id="{{ $d->id }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"></path>
                                                                <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"></path>
                                                                <path d="M16 5l3 3"></path>
                                                            </svg>
                                                        </a>
                                                        <form action="/panel-adm14/lokasi/{{ $d->id }}/delete" method="POST" style="margin-left:5px">
                                                            @csrf
                                                            <a class="btn btn-danger btn-sm delete-confirm">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                    <path d="M4 7l16 0"></path>
                                                                    <path d="M10 11l0 6"></path>
                                                                    <path d="M14 11l0 6"></path>
                                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                                </svg>
                                                            </a>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">Data lokasi tidak ditemukan</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div class="text-muted small">
                                        Menampilkan {{ $cabang->firstItem() ?? 0 }} sampai {{ $cabang->lastItem() ?? 0 }} dari {{ $cabang->total() }} data lokasi
                                    </div>
                                    <div>
                                        {{ $cabang->links('vendor.pagination.bootstrap-5') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Input Lokasi --}}
<div class="modal modal-blur fade" id="modal-inputlokasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/panel-adm14/lokasi/store" method="POST" id="frmLokasi">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Nama Lokasi</label>
                            <input type="text" id="nama_cabang" class="form-control" name="nama_cabang" placeholder="Nama Lokasi">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Titik Koordinat</label>
                            <input type="text" id="lokasi_cabang" class="form-control" name="lokasi_cabang" placeholder="Titik Koordinat">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Radius Presensi</label>
                            <input type="text" id="radius_cabang" class="form-control" name="radius_cabang" placeholder="Radius Presensi">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal modal-blur fade" id="modal-editlokasi" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Lokasi</h5>
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
    $(function() {
        $("#btnTambahLokasi").click(function() {
            $("#modal-inputlokasi").modal("show");
        });

        $(".edit").click(function() {
            var id = $(this).attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/panel-adm14/lokasi/edit',
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(respond) {
                    $("#loadeditform").html(respond);
                    $("#modal-editlokasi").modal("show");
                }
            });
        });

        $(".delete-confirm").click(function(e) {
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

        $("#frmLokasi").submit(function() {
            var nama_cabang = $("#frmLokasi").find("#nama_cabang").val();
            var lokasi_cabang = $("#frmLokasi").find("#lokasi_cabang").val();
            var radius_cabang = $("#frmLokasi").find("#radius_cabang").val();

            if (nama_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Nama Lokasi Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#frmLokasi").find("#nama_cabang").focus();
                });
                return false;
            } else if (lokasi_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Titik Lokasi Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#frmLokasi").find("#lokasi_cabang").focus();
                });
                return false;
            } else if (radius_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Radius Lokasi Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#frmLokasi").find("#radius_cabang").focus();
                });
                return false;
            }
        });
    });
</script>
@endpush
