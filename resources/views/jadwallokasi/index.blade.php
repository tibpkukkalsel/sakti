@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Data Jadwal Lokasi
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
                            <div class="col-12 d-flex">
                                <a href="#" class="btn btn-primary" id="btnTambahJadwal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 5l0 14"></path>
                                        <path d="M5 12l14 0"></path>
                                    </svg>
                                    Tambah Data
                                </a>
                                <a href="#" class="btn btn-success" id="btnSetBulk" style="margin-left: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-event" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z"></path>
                                        <path d="M16 3l0 4"></path>
                                        <path d="M8 3l0 4"></path>
                                        <path d="M4 11l16 0"></path>
                                        <path d="M8 15h2v2h-2z"></path>
                                    </svg>
                                    Set Jadwal Mingguan
                                </a>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12">
                                <form action="/panel-adm14/jadwallokasi" method="GET">
                                    <div class="row">
                                        <div class="col-4">
                                            <div class="form-group">
                                                <select name="layanan_id" class="form-select">
                                                    <option value="">Semua Layanan</option>
                                                    @foreach ($listLayanan as $l)
                                                    <option value="{{ $l->id }}" {{ Request('layanan_id') == $l->id ? 'selected' : '' }}>
                                                        {{ $l->nama_layanan }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <select name="hari" class="form-select">
                                                    <option value="">Semua Hari</option>
                                                    @foreach ($listHari as $h)
                                                    <option value="{{ $h }}" {{ Request('hari') == $h ? 'selected' : '' }}>
                                                        Hari {{ $h }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <select name="kode_cabang" class="form-select">
                                                    <option value="">Semua Lokasi</option>
                                                    @foreach ($listLokasi as $lok)
                                                    <option value="{{ $lok->kode_cabang }}" {{ Request('kode_cabang') == $lok->kode_cabang ? 'selected' : '' }}>
                                                        {{ $lok->nama_cabang }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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

                        <div class="row mt-2">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 50px;" class="text-center">No</th>
                                                <th>Nama Layanan</th>
                                                <th style="width: 120px;" class="text-center">Hari</th>
                                                <th>Lokasi Pelayanan</th>
                                                <th>Titik Koordinat & Radius</th>
                                                <th>Keterangan</th>
                                                <th style="width: 100px;" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($jadwal as $d)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration + $jadwal->firstItem() - 1 }}</td>
                                                <td>
                                                    <strong>{{ $d->nama_layanan }}</strong>
                                                    <div class="small text-muted">PIC: {{ $d->nama_pic ?: '-' }}</div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-blue-lt">{{ $d->hari }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $d->nama_cabang }}</strong>
                                                    <div class="small text-muted">Kode: {{ $d->kode_cabang }}</div>
                                                </td>
                                                <td>
                                                    <div class="small font-monospace">{{ $d->lokasi_cabang }}</div>
                                                    <span class="badge bg-secondary-lt">{{ $d->radius_cabang }} Meter</span>
                                                </td>
                                                <td>{{ $d->keterangan ?: '-' }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-center">
                                                        <a href="#" class="edit btn btn-info btn-sm" id_jadwal="{{ $d->id }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"></path>
                                                                <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"></path>
                                                                <path d="M16 5l3 3"></path>
                                                            </svg>
                                                        </a>
                                                        <form action="/panel-adm14/jadwallokasi/{{ $d->id }}/delete" method="POST" style="margin-left:5px">
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
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3">
                                    {{ $jadwal->links() }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Input Jadwal Harian --}}
<div class="modal modal-blur fade" id="modal-inputjadwal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Jadwal Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/panel-adm14/jadwallokasi/store" method="POST" id="frmJadwal">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <select name="layanan_id" id="layanan_id" class="form-select mb-3">
                                <option value="">Pilih Layanan</option>
                                @foreach ($listLayanan as $l)
                                <option value="{{ $l->id }}">{{ $l->nama_layanan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <select name="hari" id="hari" class="form-select mb-3">
                                <option value="">Pilih Hari</option>
                                @foreach ($listHari as $h)
                                <option value="{{ $h }}">Hari {{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <select name="kode_cabang" id="kode_cabang" class="form-select mb-3">
                                <option value="">Pilih Lokasi Pelayanan</option>
                                @foreach ($listLokasi as $lok)
                                <option value="{{ $lok->kode_cabang }}">{{ $lok->nama_cabang }} (Radius {{ $lok->radius_cabang }}m)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-notes" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>
                                        <path d="M9 7l6 0"></path>
                                        <path d="M9 11l6 0"></path>
                                        <path d="M9 15l4 0"></path>
                                    </svg>
                                </span>
                                <input type="text" id="keterangan" class="form-control" name="keterangan" placeholder="Keterangan / Posko (Opsional)">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="form-group">
                                <button class="btn btn-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M10 14l11 -11"></path>
                                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
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

{{-- Modal Set Bulk Mingguan --}}
<div class="modal modal-blur fade" id="modal-bulk" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Jadwal Lokasi Mingguan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/panel-adm14/jadwallokasi/bulk" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Akun Layanan</label>
                            <select name="layanan_id" class="form-select" required>
                                <option value="">Pilih Layanan</option>
                                @foreach ($listLayanan as $l)
                                <option value="{{ $l->id }}">{{ $l->nama_layanan }} (PIC: {{ $l->nama_pic ?: '-' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 120px;" class="text-center">Hari</th>
                                    <th>Lokasi Pelayanan</th>
                                    <th>Keterangan / Posko</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($listHari as $h)
                                <tr>
                                    <td class="text-center font-weight-bold">{{ $h }}</td>
                                    <td>
                                        <select name="jadwal[{{ $h }}][kode_cabang]" class="form-select">
                                            <option value="">-- Lokasi Default / Libur --</option>
                                            @foreach ($listLokasi as $lok)
                                            <option value="{{ $lok->kode_cabang }}">{{ $lok->nama_cabang }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="jadwal[{{ $h }}][keterangan]" class="form-control" placeholder="Keterangan opsional">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button class="btn btn-success w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M5 12l5 5l10 -10"></path>
                                </svg>
                                Simpan Jadwal Mingguan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit --}}
<div class="modal modal-blur fade" id="modal-editjadwal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Jadwal Lokasi</h5>
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
        $("#btnTambahJadwal").click(function() {
            $("#modal-inputjadwal").modal("show");
        });

        $("#btnSetBulk").click(function() {
            $("#modal-bulk").modal("show");
        });

        $(".edit").click(function() {
            var id = $(this).attr('id_jadwal');
            $.ajax({
                type: 'POST',
                url: '/panel-adm14/jadwallokasi/edit',
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(respond) {
                    $("#loadeditform").html(respond);
                    $("#modal-editjadwal").modal("show");
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

        $("#frmJadwal").submit(function() {
            var layanan_id = $("#layanan_id").val();
            var hari = $("#hari").val();
            var kode_cabang = $("#kode_cabang").val();

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
            } else if (hari == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Hari Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#hari").focus();
                });
                return false;
            } else if (kode_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Lokasi Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#kode_cabang").focus();
                });
                return false;
            }
        });
    });
</script>
@endpush
