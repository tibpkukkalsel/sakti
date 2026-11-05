@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    Presensi Layanan
                </div>
                <h2 class="page-title">
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
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body">
                <form action="/panel-adm14/presensi/jadwal-layanan" method="GET" autocomplete="off">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-5 col-sm-6 col-12">
                            <label class="form-label fw-bold small text-muted">Pilih Akun Layanan:</label>
                            <select name="layanan_id" class="form-select">
                                <option value="all">-- Semua Layanan --</option>
                                @foreach ($allLayanan as $l)
                                    <option value="{{ $l->id }}" {{ $layanan_id == $l->id ? 'selected' : '' }}>
                                        {{ $l->nama_layanan }} (PIC: {{ $l->nama_pic ?: '-' }})
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
                                <a href="/panel-adm14/presensi/jadwal-layanan" class="btn btn-outline-secondary" title="Reset Filter">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- TABEL UTAMA: MATRIKS JADWAL LAYANAN PER HARI UNTUK LOKASINYA -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title fw-bold text-dark mb-0">
                        <i class="bi bi-calendar2-week-fill text-primary me-2"></i>
                        Matriks Penugasan Lokasi Pelayanan Per Hari
                    </h3>
                    <small class="text-muted">Data jadwal lokasi operasional yang dikelompokkan per layanan per hari (Senin - Minggu).</small>
                </div>
                <span class="badge bg-primary-lt fs-6">{{ count($layananList) }} Layanan Tampil</span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-vcenter table-hover mb-0 text-nowrap">
                    <thead class="table-light">
                        <tr class="text-center align-middle">
                            <th style="width: 40px;">No</th>
                            <th style="min-width: 180px;" class="text-start">Akun Layanan</th>
                            <th style="min-width: 130px;" class="text-start">Lokasi Default</th>
                            @foreach ($listHari as $h)
                                @if (empty($hari) || $hari == 'all' || $hari == $h)
                                    <th style="min-width: 130px;" class="bg-primary-lt text-dark fw-bold">
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
                                    <span class="avatar rounded-circle bg-primary-lt text-primary fw-bold me-2" style="width: 34px; height: 34px;">
                                        {{ strtoupper(substr($l->nama_layanan, 0, 2)) }}
                                    </span>
                                    <div>
                                        <strong class="text-dark d-block">{{ $l->nama_layanan }}</strong>
                                        <span class="text-muted small">PIC: {{ $l->nama_pic ?: '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-building me-1 text-muted"></i>
                                    {{ $l->nama_cabang_default ?: 'UPPD Batulicin' }}
                                </span>
                            </td>

                            <!-- Kolom Hari -->
                            @foreach ($listHari as $h)
                                @if (empty($hari) || $hari == 'all' || $hari == $h)
                                    <td class="text-center">
                                        @if (isset($matrix[$l->id][$h]))
                                            @php $jadwalItem = $matrix[$l->id][$h]; @endphp
                                            <div class="p-1 rounded bg-azure-lt border border-azure" style="font-size: 12px;">
                                                <strong class="text-azure d-block">
                                                    <i class="bi bi-geo-alt-fill me-1"></i>
                                                    {{ $jadwalItem->nama_cabang }}
                                                </strong>
                                                @if (!empty($jadwalItem->keterangan))
                                                    <span class="text-muted" style="font-size: 11px;">
                                                        {{ $jadwalItem->keterangan }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small" style="font-size: 11.5px;">
                                                {{ $l->nama_cabang_default ?: 'UPPD Batulicin' }}
                                            </span>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ 3 + count($listHari) }}" class="text-center py-4 text-muted">
                                Tidak ada data layanan yang sesuai dengan filter yang dipilih.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white py-2 text-muted small d-flex justify-content-between align-items-center">
                <span><i class="bi bi-info-circle me-1"></i> Warna biru menandakan jadwal lokasi penugasan khusus pada hari tersebut.</span>
                <span>Total Jadwal Aktif: <strong>{{ $totalJadwalAktif }}</strong></span>
            </div>
        </div>

    </div>
</div>
@endsection

