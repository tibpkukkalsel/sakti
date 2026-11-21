@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Petugas Layanan
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="/panel-adm14/petugas-layanan/jadwal" class="btn btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
                            <path d="M16 3l0 4"></path>
                            <path d="M8 3l0 4"></path>
                            <path d="M4 11l16 0"></path>
                            <path d="M8 15h2v2h-2z"></path>
                        </svg>
                        Atur Jadwal & Mode
                    </a>
                    <a href="#" class="btn btn-primary" id="btnTambahPetugas">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M12 5l0 14"></path>
                            <path d="M5 12l14 0"></path>
                        </svg>
                        Tambah Petugas
                    </a>
                </div>
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

                        <!-- Filter Form -->
                        <div class="row mt-1">
                            <div class="col-12">
                                <form action="/panel-adm14/petugas-layanan" method="GET">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group mb-2">
                                                <input type="text" name="nama_petugas" id="nama_petugas" class="form-control" placeholder="Nama Petugas / Username / No. HP" value="{{ Request('nama_petugas') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-2">
                                                <select name="layanan_id" id="filter_layanan_id" class="form-select">
                                                    <option value="">Semua Layanan</option>
                                                    @foreach ($listLayanan as $l)
                                                    <option value="{{ $l->id }}" {{ Request('layanan_id') == $l->id ? 'selected' : '' }}>
                                                        {{ $l->nama_layanan }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-2 d-flex gap-2">
                                                <button type="submit" class="btn btn-primary flex-fill">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                                        <path d="M21 21l-6 -6"></path>
                                                    </svg>
                                                    Cari
                                                </button>
                                                <a href="/panel-adm14/petugas-layanan" class="btn btn-outline-secondary">Reset</a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;" class="text-center">No</th>
                                                <th>Nama Petugas</th>
                                                <th>Layanan</th>
                                                <th>Username</th>
                                                <th>Jabatan</th>
                                                <th>No. HP</th>
                                                <th class="text-center" style="width: 90px;">Status</th>
                                                <th class="text-center" style="width: 130px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($petugas as $p)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration + $petugas->firstItem() - 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if (!empty($p->foto))
                                                            <img src="{{ Storage::url('uploads/karyawan/' . $p->foto) }}" class="rounded-circle me-2 border" style="width: 36px; height: 36px; object-fit: cover;" alt="Foto Petugas" onerror="this.src='{{ asset('assets/img/avatar_layanan.png') }}'">
                                                        @else
                                                            <img src="{{ asset('assets/img/avatar_layanan.png') }}" class="rounded-circle me-2 border" style="width: 36px; height: 36px; object-fit: cover; background:#fff;" alt="Avatar">
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold">{{ $p->nama_petugas }}</div>
                                                            <small class="text-muted">NIP: {{ $p->nip ?: '-' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-blue-lt fw-bold">{{ $p->layanan->nama_layanan ?? '-' }}</span>
                                                </td>
                                                <td>
                                                    <code>{{ $p->username }}</code>
                                                </td>
                                                <td>{{ $p->jabatan ?: 'Petugas Layanan' }}</td>
                                                <td>{{ $p->no_hp ?: '-' }}</td>
                                                <td class="text-center">
                                                    @if ($p->status_aktif)
                                                        <a href="/panel-adm14/petugas-layanan/{{ $p->id }}/toggle" class="badge bg-success text-decoration-none">Aktif</a>
                                                    @else
                                                        <a href="/panel-adm14/petugas-layanan/{{ $p->id }}/toggle" class="badge bg-danger text-decoration-none">Nonaktif</a>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="#" class="btn btn-sm btn-info editPetugas" data-id="{{ $p->id }}" title="Edit">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"></path>
                                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.415v3h3l8.415 -8.415z"></path>
                                                                <path d="M16 5l3 3"></path>
                                                            </svg>
                                                        </a>
                                                        <form action="/panel-adm14/petugas-layanan/{{ Crypt::encrypt($p->id) }}/delete" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data petugas ini?')">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                    <path d="M4 7l16 0"></path>
                                                                    <path d="M10 11l0 6"></path>
                                                                    <path d="M14 11l0 6"></path>
                                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path>
                                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">Data petugas belum tersedia</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $petugas->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Input Petugas --}}
<div class="modal modal-blur fade" id="modal-inputpetugas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Petugas Layanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/panel-adm14/petugas-layanan/store" method="POST" id="frmPetugas" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Layanan</label>
                            <select name="layanan_id" id="layanan_id" class="form-select" required>
                                <option value="">Pilih Layanan</option>
                                @foreach ($listLayanan as $l)
                                <option value="{{ $l->id }}">{{ $l->nama_layanan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Nama Petugas</label>
                            <input type="text" id="input_nama_petugas" class="form-control" name="nama_petugas" placeholder="Nama Petugas" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" id="input_username" class="form-control" name="username" placeholder="Username" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" id="input_password" class="form-control" name="password" placeholder="Password">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">NIP</label>
                            <input type="text" id="input_nip" class="form-control" name="nip" placeholder="NIP">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" id="input_jabatan" class="form-control" name="jabatan" placeholder="Jabatan">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">No. HP</label>
                            <input type="text" id="input_no_hp" class="form-control" name="no_hp" placeholder="No. HP">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" name="foto" class="form-control">
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

{{-- Modal Edit Petugas --}}
<div class="modal modal-blur fade" id="modal-editpetugas" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Petugas Layanan</h5>
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
        $("#btnTambahPetugas").click(function(e) {
            e.preventDefault();
            $("#modal-inputpetugas").modal("show");
        });

        $(".editPetugas").click(function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/panel-adm14/petugas-layanan/edit',
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(respond) {
                    $("#loadeditform").html(respond);
                    $("#modal-editpetugas").modal("show");
                }
            });
        });

        $("#frmPetugas").submit(function() {
            var layanan_id = $("#layanan_id").val();
            var nama_petugas = $("#input_nama_petugas").val();
            var username = $("#input_username").val();

            if (layanan_id == "") {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Layanan Harus Dipilih',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then(() => {
                    $("#layanan_id").focus();
                });
                return false;
            } else if (nama_petugas == "") {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Nama Petugas Harus Diisi',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then(() => {
                    $("#input_nama_petugas").focus();
                });
                return false;
            } else if (username == "") {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Username Harus Diisi',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then(() => {
                    $("#input_username").focus();
                });
                return false;
            }
        });
    });
</script>
@endpush
