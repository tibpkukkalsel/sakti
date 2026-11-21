@extends('layouts.admin.tabler')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    Jadwal Petugas Layanan
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <a href="/panel-adm14/petugas-layanan" class="btn btn-outline-secondary">
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
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

        <div class="card">
            <div class="card-body">
                <!-- Dropdown Pilihan Layanan -->
                <div class="row mb-3 align-items-center">
                    <label class="col-auto col-form-label fw-bold">Layanan</label>
                    <div class="col-md-4 col-sm-6">
                        <select class="form-select" onchange="window.location.href='/panel-adm14/petugas-layanan/jadwal?layanan_id=' + this.value;">
                            @foreach ($listLayanan as $l)
                            <option value="{{ $l->id }}" {{ $layananId == $l->id ? 'selected' : '' }}>
                                {{ $l->nama_layanan }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if ($layananSelected)
                <form action="/panel-adm14/petugas-layanan/jadwal/simpan" method="POST" id="frmJadwalPetugas">
                    @csrf
                    <input type="hidden" name="layanan_id" value="{{ $layananSelected->id }}">

                    <!-- Pilihan Mode Presensi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mode Presensi</label>
                        <div>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode_presensi" value="bebas" {{ ($layananSelected->mode_presensi ?? 'bebas') == 'bebas' ? 'checked' : '' }}>
                                <span class="form-check-label">Bebas</span>
                            </label>
                            <label class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="mode_presensi" value="jadwal" {{ ($layananSelected->mode_presensi ?? '') == 'jadwal' ? 'checked' : '' }}>
                                <span class="form-check-label">Jadwal Mingguan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Tabel Matriks Jadwal -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="min-width: 200px;">Petugas</th>
                                    @foreach ($listHari as $h)
                                    <th class="text-center" style="width: 85px;">{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($listPetugas as $p)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $p->nama_petugas }}</div>
                                        <small class="text-muted">{{ $p->jabatan ?? 'Petugas' }}</small>
                                    </td>
                                    @foreach ($listHari as $h)
                                    <td class="text-center">
                                        <input class="form-check-input" type="checkbox" name="jadwal[{{ $p->id }}][]" value="{{ $h }}" style="cursor: pointer;" {{ isset($matrixJadwal[$p->id][$h]) ? 'checked' : '' }}>
                                    </td>
                                    @endforeach
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        Belum ada petugas terdaftar pada armada ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M10 14l11 -11"></path>
                                <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
                            </svg>
                            Simpan
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
