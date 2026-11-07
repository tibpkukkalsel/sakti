@extends('layouts.admin.tabler')

@section('content')
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Monitoring Presensi
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
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

                            <!-- Filter Data (Sama dengan App Penerimaan PKB) -->
                            <div class="row">
                                <div class="col-12">
                                    <form action="/panel-adm14/presensi/monitoring" method="GET">
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

                            <!-- Table Container -->
                            <div class="row mt-3" id="containerTablePresensi">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 40px;">No</th>
                                                    <th style="width: 110px;">Tanggal</th>
                                                    <th>Akun Layanan</th>
                                                    <th>Lokasi Pelayanan</th>
                                                    <th>Jadwal Kerja</th>
                                                    <th class="text-center" style="width: 115px;">Jam Masuk</th>
                                                    <th class="text-center" style="width: 70px;">Foto Masuk</th>
                                                    <th class="text-center" style="width: 115px;">Jam Pulang</th>
                                                    <th class="text-center" style="width: 70px;">Foto Pulang</th>
                                                    <th class="text-center" style="width: 100px;">Status</th>
                                                    <th class="text-center" style="width: 100px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($presensi as $d)
                                                    @php
                                                        $foto_in = !empty($d->foto_in) ? Storage::url('uploads/absensi/' . $d->foto_in) . '?v=' . time() : null;
                                                        $foto_out = !empty($d->foto_out) ? Storage::url('uploads/absensi/' . $d->foto_out) . '?v=' . time() : null;
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center text-muted fw-bold">
                                                            {{ $loop->iteration + $presensi->firstItem() - 1 }}
                                                        </td>
                                                        <td>
                                                            <strong class="text-dark">{{ date('d-m-Y', strtotime($d->tgl_presensi)) }}</strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark border fs-6 px-2 py-1" style="color: #0f172a !important; background-color: #f1f5f9 !important;">
                                                                {{ $d->nama_layanan }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-light text-dark border" style="color: #0f172a !important; background-color: #f1f5f9 !important;">
                                                                {{ $d->lokasi ?: 'Kantor Pusat' }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if (!empty($d->nama_jam_kerja))
                                                                <div class="fw-semibold text-dark">{{ $d->nama_jam_kerja }}</div>
                                                                <div class="small text-muted font-monospace">
                                                                    {{ substr($d->jam_masuk, 0, 5) }} - {{ substr($d->jam_pulang, 0, 5) }}
                                                                </div>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($d->jam_in != null)
                                                                <span class="badge bg-success text-white fs-6">{{ substr($d->jam_in, 0, 5) }}</span>
                                                                @if ($d->status_in == 'izin_terlambat')
                                                                    <br><span class="badge bg-warning text-dark mt-1" style="color: #000000 !important;">Izin Terlambat</span>
                                                                @elseif ($d->status_in == 'terlambat' || (!empty($d->jam_masuk) && $d->jam_in > $d->jam_masuk))
                                                                    <br><span class="badge bg-danger text-white mt-1">Terlambat {{ hitungSelisihAdmin($d->jam_masuk, $d->jam_in) }}</span>
                                                                @endif
                                                            @else
                                                                <span class="badge bg-danger text-white">Belum Absen</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($foto_in != null)
                                                                <img src="{{ url($foto_in) }}" class="avatar rounded border shadow-sm" alt="Foto Masuk" style="width: 40px; height: 40px; object-fit: cover;">
                                                            @else
                                                                <span class="text-muted small">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($d->jam_out != null)
                                                                <span class="badge bg-info text-white fs-6" style="background-color: #0284c7 !important; color: #ffffff !important;">{{ substr($d->jam_out, 0, 5) }}</span>
                                                                @if ($d->status_out == 'izin_pulang_cepat')
                                                                    <br><span class="badge bg-warning text-dark mt-1" style="color: #000000 !important;">Izin Pulang Cepat</span>
                                                                @elseif ($d->status_out == 'pulang_duluan' || (!empty($d->jam_pulang) && $d->jam_out < $d->jam_pulang))
                                                                    <br><span class="badge bg-danger text-white mt-1">Pulang Duluan {{ hitungSelisihAdmin($d->jam_out, $d->jam_pulang) }}</span>
                                                                @endif
                                                            @else
                                                                <span class="badge bg-danger text-white">Belum Absen</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($foto_out != null)
                                                                <img src="{{ url($foto_out) }}" class="avatar rounded border shadow-sm" alt="Foto Pulang" style="width: 40px; height: 40px; object-fit: cover;">
                                                            @else
                                                                <span class="text-muted small">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($d->presensi_status == 'h')
                                                                <span class="badge bg-success text-white">Hadir</span>
                                                            @elseif ($d->presensi_status == 'i')
                                                                <span class="badge bg-warning text-dark" style="color: #000000 !important;">Izin</span>
                                                            @elseif ($d->presensi_status == 's')
                                                                <span class="badge bg-info text-white" style="background-color: #0284c7 !important; color: #ffffff !important;">Sakit</span>
                                                            @elseif ($d->presensi_status == 'c')
                                                                <span class="badge bg-secondary text-white">Cuti</span>
                                                            @elseif (empty($d->jam_in) && empty($d->jam_out))
                                                                <span class="badge bg-danger text-white">Belum Absen</span>
                                                            @else
                                                                <span class="badge bg-primary text-white">{{ strtoupper($d->presensi_status) }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group" role="group">
                                                                @if (!empty($d->presensi_id) && (!empty($d->lokasi_in) || !empty($d->lokasi_out)))
                                                                    <a href="#" class="btn btn-primary btn-sm tampilkanpeta" id="{{ $d->presensi_id }}" title="Lihat Peta Lokasi">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                            <path d="M12 18.5l-3 -1.5l-6 3v-13l6 -3l6 3l6 -3v7.5"></path>
                                                                            <path d="M9 4v13"></path>
                                                                            <path d="M15 7v5.5"></path>
                                                                            <path d="M21.121 20.121a3 3 0 1 0 -4.242 0c.418 .419 1.125 1.045 2.121 1.879c1.051 -.89 1.759 -1.516 2.121 -1.879z"></path>
                                                                            <path d="M19 18v.01"></path>
                                                                        </svg>
                                                                    </a>
                                                                @endif
                                                                <a href="#" class="btn btn-success btn-sm koreksipresensi" nik="{{ $d->layanan_id }}" tanggal="{{ $d->tgl_presensi }}" title="Koreksi Presensi">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                        <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4"></path>
                                                                        <path d="M13.5 6.5l4 4"></path>
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="11" class="text-center py-4 text-muted">
                                                            Tidak ada data presensi pada periode atau filter layanan yang dipilih.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @if ($presensi->hasPages())
                                <div class="card-footer bg-white py-2 border-top d-flex align-items-center">
                                    {{ $presensi->links('vendor.pagination.bootstrap-5') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modal-tampilkanpeta" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lokasi Presensi Layanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="loadmap"></div>
            </div>
        </div>
    </div>

    <div class="modal modal-blur fade" id="modal-koreksipresensi" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Koreksi Presensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="loadkoreksipresensi"></div>
            </div>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        $(function() {
            // Modal Tampilkan Peta
            $(document).on('click', '.tampilkanpeta', function(e) {
                e.preventDefault();
                var id = $(this).attr('id');
                $("#modal-tampilkanpeta").modal("show");
                $("#loadmap").html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><div class="mt-2 text-muted">Memuat peta...</div></div>');
                $.ajax({
                    type: 'POST',
                    url: '/panel-adm14/tampilkanpeta',
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadmap").html(respond);
                    },
                    error: function() {
                        $("#loadmap").html('<div class="alert alert-danger m-3">Gagal memuat peta lokasi presensi.</div>');
                    }
                });
            });

            // Modal Koreksi Presensi
            $(document).on('click', '.koreksipresensi', function(e) {
                e.preventDefault();
                var nik = $(this).attr('nik');
                var tanggal = $(this).attr('tanggal');
                $("#loadkoreksipresensi").html('<div class="text-center py-4"><div class="spinner-border text-success" role="status"></div><div class="mt-2 text-muted">Memuat form koreksi...</div></div>');
                $("#modal-koreksipresensi").modal("show");
                $.ajax({
                    type: 'POST',
                    url: '/panel-adm14/koreksipresensi',
                    data: {
                        _token: "{{ csrf_token() }}",
                        nik: nik,
                        tanggal: tanggal
                    },
                    cache: false,
                    success: function(respond) {
                        $("#loadkoreksipresensi").html(respond);
                    },
                    error: function() {
                        $("#loadkoreksipresensi").html('<div class="alert alert-danger m-3">Gagal memuat form koreksi presensi.</div>');
                    }
                });
            });

            // AJAX Pagination untuk Tabel Monitoring Presensi
            function loadMonitoringTable(url, pushState) {
                if (pushState === undefined) pushState = true;
                var card = document.querySelector('.card');
                if (!card) return;

                $('#containerTablePresensi, .card-footer').css({
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
                            var newTable = doc.getElementById('containerTablePresensi');
                            var newFooter = doc.querySelector('.card-footer');

                            if (newTable) {
                                document.getElementById('containerTablePresensi').innerHTML = newTable.innerHTML;
                                var currentFooter = document.querySelector('.card-footer');
                                if (currentFooter && newFooter) {
                                    currentFooter.innerHTML = newFooter.innerHTML;
                                } else if (!currentFooter && newFooter) {
                                    document.querySelector('.card-body').appendChild(newFooter);
                                } else if (currentFooter && !newFooter) {
                                    currentFooter.remove();
                                }
                                $('#containerTablePresensi, .card-footer').css({
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

            $(document).on('click', '.card-footer .pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                if (!url || url === '#' || url.indexOf('javascript') === 0) return;
                loadMonitoringTable(url, true);
            });

            window.addEventListener('popstate', function() {
                if (window.location.pathname.indexOf('presensi/monitoring') !== -1) {
                    loadMonitoringTable(window.location.href, false);
                }
            });
        });
    </script>
@endpush
