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
                        Jadwal Layanan
                    </h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="/panel-adm14/home" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Home
                    </a>
                    <a href="/panel-adm14/jadwallokasi" class="btn btn-primary">
                        <i class="bi bi-gear-fill me-1"></i> Kelola Master Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-body">
        <div class="container-xl">

            <!-- Form Filter -->
            <div class="card card-outline card-primary shadow-sm mb-3">
                <div class="card-body">
                    <form action="/panel-adm14/presensi/jadwal-layanan" method="GET" autocomplete="off">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5 col-sm-6 col-12">
                                <label class="form-label fw-bold small text-muted">Pilih Akun Layanan:</label>
                                <select name="layanan_id" class="form-select">
                                    <option value="all">-- Semua Layanan --</option>
                                    @foreach ($allLayanan as $l)
                                        <option value="{{ $l->id }}" {{ $layanan_id == $l->id ? 'selected' : '' }}>
                                            {{ $l->nama_layanan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6 col-12">
                                <label class="form-label fw-bold small text-muted">Filter Hari:</label>
                                <select name="hari" class="form-select">
                                    <option value="all">-- Semua Hari (Senin - Minggu) --</option>
                                    @foreach ($listHari as $h)
                                        <option value="{{ $h }}" {{ $hari == $h ? 'selected' : '' }}>
                                            Hari {{ $h }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search me-1"></i> Tampilkan
                                    </button>
                                    <a href="/panel-adm14/presensi/jadwal-layanan" class="btn btn-outline-secondary"
                                        title="Reset Filter">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- TABEL UTAMA: MATRIKS JADWAL LAYANAN PER HARI UNTUK LOKASINYA -->
            <div class="card card-outline card-primary shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-0">
                            <i class="bi bi-calendar2-week text-primary me-2"></i> Jadwal Lokasi Pelayanan
                        </h3>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr class="text-center align-middle">
                                <th style="width: 50px;">No</th>
                                <th style="min-width: 220px;" class="text-start">Akun Layanan</th>
                                @foreach ($listHari as $h)
                                    @if (empty($hari) || $hari == 'all' || $hari == $h)
                                        <th style="min-width: 140px;" class="text-dark fw-bold">
                                            {{ $h }}
                                        </th>
                                    @endif
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($layananList as $l)
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="avatar rounded-circle bg-primary text-white fw-bold me-2"
                                                style="width: 34px; height: 34px; font-size: 13px;">
                                                {{ strtoupper(substr($l->nama_layanan, 0, 2)) }}
                                            </span>
                                            <strong class="text-dark">{{ $l->nama_layanan }}</strong>
                                        </div>
                                    </td>

                                    <!-- Kolom Hari -->
                                    @foreach ($listHari as $h)
                                        @if (empty($hari) || $hari == 'all' || $hari == $h)
                                            <td class="text-center">
                                                @if (isset($matrix[$l->id][$h]))
                                                    @php $jadwalItem = $matrix[$l->id][$h]; @endphp
                                                    <div class="p-2 rounded bg-light border border-secondary-subtle"
                                                        style="font-size: 12px;">
                                                        <strong class="text-primary d-block">
                                                            <i class="bi bi-geo-alt-fill me-1"></i>
                                                            {{ $jadwalItem->nama_cabang }}
                                                        </strong>
                                                        @if (!empty($jadwalItem->keterangan))
                                                            <span class="text-muted small d-block">
                                                                {{ $jadwalItem->keterangan }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 2 + count($listHari) }}" class="text-center py-4 text-muted">
                                        Tidak ada data layanan yang sesuai dengan filter yang dipilih.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white py-2 text-muted small d-flex justify-content-between align-items-center">
                    <span>Total Jadwal Rutin Aktif: <strong>{{ $totalJadwalAktif }}</strong></span>
                </div>
            </div>

            <!-- TABEL JADWAL KHUSUS / EVENT MENDATANG -->
            <div class="card card-outline card-warning shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="card-title fw-bold text-dark mb-0">
                            <i class="bi bi-star-fill text-warning me-2"></i> Jadwal Khusus & Event Layanan
                        </h3>
                        <div class="text-muted small mt-1">Jadwal lokasi insidental/event yang diatur pada tanggal tertentu (di luar jadwal mingguan)</div>
                    </div>
                    <span class="badge bg-warning text-dark">{{ count($listEventKhusus) }} Event</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover align-middle mb-0 text-nowrap">
                        <thead class="table-light">
                            <tr class="text-center align-middle">
                                <th style="width: 50px;">No</th>
                                <th style="width: 150px;">Tanggal Event</th>
                                <th>Hari</th>
                                <th class="text-start">Akun Layanan</th>
                                <th>Lokasi Pelayanan</th>
                                <th>Radius</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($listEventKhusus as $ek)
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark fw-bold">
                                            {{ date('d/m/Y', strtotime($ek->tanggal)) }}
                                        </span>
                                    </td>
                                    <td class="text-center fw-semibold">
                                        {{ $ek->hari }}
                                    </td>
                                    <td>
                                        <strong class="text-dark">{{ $ek->nama_layanan }}</strong>
                                    </td>
                                    <td>
                                        <strong class="text-primary">{{ $ek->nama_cabang }}</strong>
                                        <div class="small text-muted">Kode: {{ $ek->kode_cabang }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info text-white">{{ $ek->radius_cabang }} Meter</span>
                                    </td>
                                    <td>
                                        {{ $ek->keterangan ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        Tidak ada jadwal event khusus / tanggal khusus mendatang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
@endsection
