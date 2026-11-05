@extends('layouts.admin.tabler')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Jadwal Lokasi Layanan
                    </h2>
                    <div class="text-muted mt-1">
                        Atur penetapan lokasi operasional harian berdasarkan hari untuk setiap akun layanan (SAMSAT Keliling, dsb).
                    </div>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Jadwal Hari
                        </a>
                        <a href="#" class="btn btn-success d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#modalSetBulk">
                            <i class="bi bi-calendar-week me-1"></i> Set Jadwal Mingguan
                        </a>
                        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#modalTambahJadwal" aria-label="Tambah Jadwal">
                            <i class="bi bi-plus-lg"></i>
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
                    @if (Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>{{ Session::get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (Session::get('warning'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ Session::get('warning') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card mb-3">
                        <div class="card-header border-bottom">
                            <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-daftar" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab">
                                        <i class="bi bi-list-task me-2"></i>Daftar Jadwal
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a href="#tab-matriks" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">
                                        <i class="bi bi-grid-3x3 me-2"></i>Matriks Jadwal Mingguan
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <!-- TAB 1: DAFTAR JADWAL -->
                            <div class="tab-pane active show" id="tab-daftar" role="tabpanel">
                                <div class="card-body">
                                    <!-- Filter Bar -->
                                    <form action="/panel-adm14/jadwallokasi" method="GET" class="mb-3">
                                        <div class="row g-2">
                                            <div class="col-md-3">
                                                <select name="layanan_id" class="form-select">
                                                    <option value="">-- Semua Layanan --</option>
                                                    @foreach ($listLayanan as $l)
                                                        <option value="{{ $l->id }}" {{ Request('layanan_id') == $l->id ? 'selected' : '' }}>
                                                            {{ $l->nama_layanan }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <select name="hari" class="form-select">
                                                    <option value="">-- Semua Hari --</option>
                                                    @foreach ($listHari as $h)
                                                        <option value="{{ $h }}" {{ Request('hari') == $h ? 'selected' : '' }}>
                                                            Hari {{ $h }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select name="kode_cabang" class="form-select">
                                                    <option value="">-- Semua Lokasi --</option>
                                                    @foreach ($listLokasi as $lok)
                                                        <option value="{{ $lok->kode_cabang }}" {{ Request('kode_cabang') == $lok->kode_cabang ? 'selected' : '' }}>
                                                            {{ $lok->nama_cabang }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="bi bi-search me-1"></i> Filter
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Table -->
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover align-middle">
                                            <thead class="table-dark">
                                                <tr class="text-center">
                                                    <th style="width: 50px;">No</th>
                                                    <th>Nama Layanan</th>
                                                    <th style="width: 120px;">Hari</th>
                                                    <th>Lokasi Pelayanan</th>
                                                    <th>Titik Koordinat & Radius</th>
                                                    <th>Keterangan</th>
                                                    <th style="width: 110px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($jadwal as $d)
                                                    @php
                                                        $badgeColor = match($d->hari) {
                                                            'Senin' => 'bg-blue text-blue-fg',
                                                            'Selasa' => 'bg-azure text-azure-fg',
                                                            'Rabu' => 'bg-green text-green-fg',
                                                            'Kamis' => 'bg-warning text-warning-fg',
                                                            'Jumat' => 'bg-teal text-teal-fg',
                                                            'Sabtu' => 'bg-purple text-purple-fg',
                                                            default => 'bg-secondary text-secondary-fg',
                                                        };
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center">{{ $loop->iteration + $jadwal->firstItem() - 1 }}</td>
                                                        <td>
                                                            <div class="fw-bold text-dark">{{ $d->nama_layanan }}</div>
                                                            <small class="text-muted">PIC: {{ $d->nama_pic ?: '-' }}</small>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge {{ $badgeColor }} px-2 py-1 fs-6">
                                                                {{ $d->hari }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="fw-semibold text-primary">
                                                                <i class="bi bi-geo-alt-fill me-1"></i>{{ $d->nama_cabang }}
                                                            </div>
                                                            <small class="text-muted">Kode: {{ $d->kode_cabang }}</small>
                                                        </td>
                                                        <td>
                                                            <div class="small font-monospace text-muted">{{ $d->lokasi_cabang }}</div>
                                                            <span class="badge bg-secondary-lt">Radius: {{ $d->radius_cabang }} meter</span>
                                                        </td>
                                                        <td>
                                                            {{ $d->keterangan ?: '-' }}
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group" role="group">
                                                                <button type="button" class="btn btn-sm btn-outline-warning btnEdit" data-id="{{ $d->id }}" title="Edit Jadwal">
                                                                    <i class="bi bi-pencil-square"></i>
                                                                </button>
                                                                <form action="/panel-adm14/jadwallokasi/{{ $d->id }}/delete" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Jadwal">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center py-4 text-muted">
                                                            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                                                            Belum ada data jadwal lokasi layanan yang ditemukan.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="d-flex justify-content-end mt-3">
                                        {{ $jadwal->links() }}
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 2: MATRIKS JADWAL MINGGUAN -->
                            <div class="tab-pane" id="tab-matriks" role="tabpanel">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle">
                                            <thead class="table-primary text-center">
                                                <tr>
                                                    <th style="min-width: 150px;">Nama Layanan</th>
                                                    @foreach ($listHari as $h)
                                                        <th style="min-width: 140px;">{{ $h }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($listLayanan as $l)
                                                    <tr>
                                                        <td class="fw-bold bg-light">
                                                            <div class="text-dark">{{ $l->nama_layanan }}</div>
                                                            <small class="text-muted">PIC: {{ $l->nama_pic }}</small>
                                                        </td>
                                                        @foreach ($listHari as $h)
                                                            @php
                                                                $item = $matrix[$l->id][$h] ?? null;
                                                            @endphp
                                                            <td class="text-center p-2 {{ $item ? 'bg-blue-lt' : 'text-muted' }}">
                                                                @if ($item)
                                                                    <div class="fw-bold text-dark fs-6">{{ $item->nama_cabang }}</div>
                                                                    @if ($item->keterangan)
                                                                        <small class="text-muted d-block mt-1">{{ Str::limit($item->keterangan, 30) }}</small>
                                                                    @endif
                                                                @else
                                                                    <span class="badge bg-secondary-lt">- Libur / Induk -</span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH JADWAL (Single Day) -->
    <div class="modal fade" id="modalTambahJadwal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/panel-adm14/jadwallokasi/store" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-calendar-plus me-2 text-primary"></i>Tambah Jadwal Lokasi Harian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Akun Layanan <span class="text-danger">*</span></label>
                            <select name="layanan_id" class="form-select" required>
                                <option value="">-- Pilih Layanan --</option>
                                @foreach ($listLayanan as $l)
                                    <option value="{{ $l->id }}">{{ $l->nama_layanan }} (PIC: {{ $l->nama_pic ?: '-' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hari Pelayanan <span class="text-danger">*</span></label>
                            <select name="hari" class="form-select" required>
                                <option value="">-- Pilih Hari --</option>
                                @foreach ($listHari as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Lokasi Pelayanan <span class="text-danger">*</span></label>
                            <select name="kode_cabang" class="form-select" required>
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach ($listLokasi as $lok)
                                    <option value="{{ $lok->kode_cabang }}">{{ $lok->nama_cabang }} (Radius {{ $lok->radius_cabang }}m)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Keterangan / Titik Posko</label>
                            <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Halaman Kantor Kecamatan / Depan Pasar">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Simpan Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL SET JADWAL MINGGUAN (Bulk Weekly Schedule) -->
    <div class="modal fade" id="modalSetBulk" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="/panel-adm14/jadwallokasi/bulk" method="POST">
                    @csrf
                    <div class="modal-header bg-success-lt">
                        <h5 class="modal-title"><i class="bi bi-calendar-week me-2 text-success"></i>Set Jadwal Mingguan Serentak</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Akun Layanan <span class="text-danger">*</span></label>
                            <select name="layanan_id" class="form-select" required>
                                <option value="">-- Pilih Layanan --</option>
                                @foreach ($listLayanan as $l)
                                    <option value="{{ $l->id }}">{{ $l->nama_layanan }} (PIC: {{ $l->nama_pic ?: '-' }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Atur lokasi untuk setiap hari kerja dalam seminggu. Hari yang dikosongkan tidak akan memiliki jadwal khusus.</small>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th style="width: 110px;">Hari</th>
                                        <th>Lokasi Pelayanan</th>
                                        <th>Keterangan / Posko</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listHari as $h)
                                        <tr>
                                            <td class="fw-bold text-center bg-light">{{ $h }}</td>
                                            <td>
                                                <select name="jadwal[{{ $h }}][kode_cabang]" class="form-select form-select-sm">
                                                    <option value="">-- Tidak Ada / Default --</option>
                                                    @foreach ($listLokasi as $lok)
                                                        <option value="{{ $lok->kode_cabang }}">{{ $lok->nama_cabang }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" name="jadwal[{{ $h }}][keterangan]" class="form-control form-control-sm" placeholder="Keterangan opsional">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i> Simpan Seluruh Jadwal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT JADWAL (Via AJAX) -->
    <div class="modal fade" id="modalEditJadwal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" id="loadModalEdit">
                <!-- Konten modal dimuat via AJAX -->
            </div>
        </div>
    </div>
@endsection

@push('myscript')
<script>
    $(function() {
        $(".btnEdit").click(function() {
            var id = $(this).attr('data-id');
            $.ajax({
                type: 'POST',
                url: '/panel-adm14/jadwallokasi/edit',
                cache: false,
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(respond) {
                    $("#loadModalEdit").html(respond);
                    $("#modalEditJadwal").modal("show");
                },
                error: function() {
                    alert('Gagal memuat form edit jadwal.');
                }
            });
        });
    });
</script>
@endpush
