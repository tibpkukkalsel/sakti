@extends('layouts.admin.tabler')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Data Akun Layanan
                    </h2>
                    <div class="text-muted mt-1">Kelola akun layanan (SAMKEL, Samsat Induk, dsb) beserta data PIC untuk absensi dan rekap penerimaan PKB.</div>
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
                                        <div class="alert alert-success alert-dismissible" role="alert">
                                            {{ Session::get('success') }}
                                        </div>
                                    @endif

                                    @if (Session::get('warning'))
                                        <div class="alert alert-warning alert-dismissible" role="alert">
                                            {{ Session::get('warning') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @role('administrator', 'user')
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <a href="#" class="btn btn-primary" id="btnTambahLayanan">
                                            <i class="bi bi-plus-lg me-1"></i> Tambah Layanan Baru
                                        </a>
                                    </div>
                                </div>
                            @endrole

                            <div class="row mb-3">
                                <div class="col-12">
                                    <form action="/panel-adm14/layanan" method="GET">
                                        <div class="row g-2">
                                            <div class="col-md-5">
                                                <input type="text" name="nama_layanan" id="nama_layanan"
                                                    class="form-control" placeholder="Cari Nama Layanan, Username, atau PIC..."
                                                    value="{{ Request('nama_layanan') }}">
                                            </div>
                                            <div class="col-md-4">
                                                <select name="kode_cabang" id="kode_cabang" class="form-select">
                                                    <option value="">-- Semua Lokasi --</option>
                                                    @foreach ($cabang as $c)
                                                        <option value="{{ $c->kode_cabang }}"
                                                            {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}>
                                                            {{ $c->nama_cabang }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="bi bi-search me-1"></i> Cari
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover align-middle">
                                    <thead class="table-dark">
                                        <tr class="text-center">
                                            <th style="width: 50px;">No</th>
                                            <th>Nama Layanan</th>
                                            <th>Username</th>
                                            <th>Data Penanggung Jawab (PIC)</th>
                                            <th>Lokasi</th>
                                            <th>Keamanan Presensi</th>
                                            <th style="width: 140px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($layanan as $d)
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration + $layanan->firstItem() - 1 }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if (!empty($d->foto))
                                                            <img src="{{ Storage::url('uploads/karyawan/' . $d->foto) }}"
                                                                class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;"
                                                                alt="{{ $d->nama_layanan }}">
                                                        @else
                                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold"
                                                                style="width: 40px; height: 40px; font-size: 14px;">
                                                                {{ strtoupper(substr($d->nama_layanan, 0, 2)) }}
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold fs-6 text-dark">{{ $d->nama_layanan }}</div>
                                                            <small class="text-muted">ID: {{ $d->id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-blue-lt px-2 py-1 fs-7">
                                                        <i class="bi bi-person me-1"></i>{{ $d->username }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-dark">{{ $d->nama_pic }}</div>
                                                    <div class="text-muted small">
                                                        <span>NIP: {{ $d->nip_pic ?: '-' }}</span> &bull; 
                                                        <span>Jabatan: {{ $d->jabatan_pic ?: 'Petugas' }}</span>
                                                    </div>
                                                    @if ($d->no_hp)
                                                        <div class="small text-success">
                                                            <i class="bi bi-whatsapp me-1"></i>{{ $d->no_hp }}
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $lok = $cabang->firstWhere('kode_cabang', $d->kode_cabang);
                                                    @endphp
                                                    <span class="badge bg-secondary-lt">
                                                        <i class="bi bi-geo-alt me-1"></i>{{ $lok ? $lok->nama_cabang : ($d->kode_cabang ?: 'UPPD SAMSAT Batu Licin') }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex flex-column gap-1 align-items-center">
                                                        @if ($d->status_location == '1')
                                                            <a href="/panel-adm14/layanan/{{ $d->id }}/lockandunlocklocation"
                                                                class="badge bg-success text-decoration-none" title="Klik untuk mematikan radius lokasi">
                                                                <i class="bi bi-check-circle me-1"></i> Radius ON
                                                            </a>
                                                        @else
                                                            <a href="/panel-adm14/layanan/{{ $d->id }}/lockandunlocklocation"
                                                                class="badge bg-danger text-decoration-none" title="Klik untuk mengaktifkan radius lokasi">
                                                                <i class="bi bi-x-circle me-1"></i> Bebas Lokasi
                                                            </a>
                                                        @endif

                                                        @if ($d->status_jam_kerja == '1')
                                                            <a href="/panel-adm14/layanan/{{ $d->id }}/lockandunlockjamkerja"
                                                                class="badge bg-success text-decoration-none" title="Klik untuk mematikan jadwal jam kerja">
                                                                <i class="bi bi-clock me-1"></i> Jam Kerja ON
                                                            </a>
                                                        @else
                                                            <a href="/panel-adm14/layanan/{{ $d->id }}/lockandunlockjamkerja"
                                                                class="badge bg-danger text-decoration-none" title="Klik untuk mengaktifkan jadwal jam kerja">
                                                                <i class="bi bi-clock-history me-1"></i> Bebas Jam
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="#" class="btn btn-info edit" data-id="{{ $d->id }}" title="Edit Layanan">
                                                            <i class="bi bi-pencil-square"></i>
                                                        </a>
                                                        <a href="/panel-adm14/layanan/{{ Crypt::encrypt($d->id) }}/resetpassword"
                                                            class="btn btn-warning" title="Reset Password ke 12345"
                                                            onclick="return confirm('Apakah Anda yakin ingin mereset password akun {{ $d->nama_layanan }} ke 12345?')">
                                                            <i class="bi bi-key-fill"></i>
                                                        </a>
                                                        <form action="/panel-adm14/layanan/{{ $d->id }}/delete" method="POST"
                                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun layanan {{ $d->nama_layanan }}?')"
                                                            style="display:inline;">
                                                            @csrf
                                                            <button type="submit" class="btn btn-danger" title="Hapus Layanan">
                                                                <i class="bi bi-trash-fill"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    Belum ada data akun layanan. Silakan klik tombol <strong>Tambah Layanan Baru</strong>.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                {{ $layanan->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Layanan -->
    <div class="modal modal-blur fade" id="modal-tambahlayanan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-1 text-primary"></i> Tambah Akun Layanan Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="/panel-adm14/layanan/store" method="POST" id="formLayanan" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i> Akun absensi menggunakan nama layanan (contoh: <strong>SAMKEL 1</strong>, <strong>SAMKEL 2</strong>), login menggunakan <strong>Username</strong>, dan penanggung jawab diisi pada kolom PIC.
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label required">Nama Layanan</label>
                                <input type="text" name="nama_layanan" class="form-control" placeholder="Contoh: SAMKEL 1 / SAMSAT INDUK" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Username Login</label>
                                <input type="text" name="username" class="form-control" placeholder="Contoh: samkel1" required>
                                <small class="text-muted">Digunakan oleh layanan untuk login ke aplikasi.</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Password Default</label>
                                <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika default 12345">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lokasi Kantor / Layanan</label>
                                <select name="kode_cabang" class="form-select">
                                    <option value="">-- Pilih Lokasi --</option>
                                    @foreach ($cabang as $c)
                                        <option value="{{ $c->kode_cabang }}">{{ $c->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12"><hr class="my-1"></div>

                            <h6 class="fw-bold text-secondary mb-0">Informasi Penanggung Jawab (PIC)</h6>

                            <div class="col-md-6">
                                <label class="form-label required">Nama PIC</label>
                                <input type="text" name="nama_pic" class="form-control" placeholder="Nama Lengkap Penanggung Jawab" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIP PIC</label>
                                <input type="text" name="nip_pic" class="form-control" placeholder="Nomor Induk Pegawai (Opsional)">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jabatan PIC</label>
                                <input type="text" name="jabatan_pic" class="form-control" placeholder="Contoh: Petugas Layanan Samsat Keliling">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">No. HP / WhatsApp PIC</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Foto / Logo Layanan</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Layanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Layanan -->
    <div class="modal modal-blur fade" id="modal-editlayanan" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-1 text-primary"></i> Edit Data Akun Layanan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="loadeditform">
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
    $(function() {
        $("#btnTambahLayanan").click(function(e) {
            e.preventDefault();
            $("#modal-tambahlayanan").modal("show");
        });

        $(".edit").click(function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            $("#modal-editlayanan").modal("show");
            $("#loadeditform").html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>');
            $.ajax({
                type: 'POST',
                url: '/panel-adm14/layanan/edit',
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(respond) {
                    $("#loadeditform").html(respond);
                },
                error: function() {
                    $("#loadeditform").html('<div class="alert alert-danger">Gagal memuat formulir edit.</div>');
                }
            });
        });
    });
</script>
@endpush
