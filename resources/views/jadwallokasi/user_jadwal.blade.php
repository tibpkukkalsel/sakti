@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Jadwal Lokasi Layanan</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <div style="padding-top: 65px; padding-bottom: 75px;">
        @if (Session::get('success'))
            <div class="alert alert-success mx-3 my-2">
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::get('warning'))
            <div class="alert alert-warning mx-3 my-2">
                {{ Session::get('warning') }}
            </div>
        @endif

        <!-- 1. Lokasi Hari Ini (Pembatas Biasa) -->
        <div class="bg-white border-bottom px-3 py-3 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-bold text-uppercase">Lokasi Hari Ini ({{ $namahariIni }})</span>
                @if ($jadwalHariIni)
                    <span class="badge bg-success" style="font-size: 11px;">Aktif Beroperasi</span>
                @else
                    <span class="badge bg-secondary" style="font-size: 11px;">Tidak Beroperasi</span>
                @endif
            </div>

            @if ($jadwalHariIni)
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <div style="font-size: 17px; font-weight: 700; color: #1e293b;">
                            {{ $jadwalHariIni->nama_cabang }}
                        </div>
                        <div class="text-muted small mt-1">
                            <ion-icon name="location-outline" class="text-primary"></ion-icon>
                            {{ $jadwalHariIni->keterangan ?: 'Posko Operasional Pelayanan' }}
                        </div>
                    </div>
                    @php
                        $coords = explode(',', $jadwalHariIni->lokasi_cabang);
                        $mapsUrl = count($coords) >= 2 ? 'https://www.google.com/maps?q=' . trim($coords[0]) . ',' . trim($coords[1]) : '#';
                    @endphp
                    @if ($mapsUrl !== '#')
                        <a href="{{ $mapsUrl }}" target="_blank" class="btn btn-sm btn-outline-primary" style="padding: 3px 12px; font-size: 12px;">
                            <ion-icon name="navigate-outline"></ion-icon> Buka Peta
                        </a>
                    @endif
                </div>

                <div class="d-flex justify-content-between text-muted small mt-2 pt-2 border-top">
                    <span>Radius Presensi: <strong>{{ $jadwalHariIni->radius_cabang }} meter</strong></span>
                    <span>Layanan: <strong>{{ $user->nama_layanan }}</strong></span>
                </div>
            @else
                <div class="text-muted small mt-2">
                    Tidak ada jadwal operasional keliling pada hari ini.
                </div>
            @endif
        </div>

        <!-- 2. Header Jadwal Mingguan -->
        <div class="d-flex justify-content-between align-items-center px-3 mt-3 mb-2">
            <span class="text-muted small fw-bold text-uppercase">Jadwal Operasional Mingguan</span>
            <span class="text-muted small">{{ $user->nama_layanan }}</span>
        </div>

        <!-- 3. Daftar Jadwal Dengan Pembatas Biasa (Tanpa Card) -->
        <div class="bg-white border-top border-bottom">
            @php
                $listSemuaHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            @endphp

            @foreach ($listSemuaHari as $h)
                @php
                    $j = $jadwalMingguan->firstWhere('hari', $h);
                    $isHariIni = ($h === $namahariIni);
                @endphp
                <div class="px-3 py-3 border-bottom {{ $isHariIni ? 'bg-light' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-start gap-2">
                            <div style="min-width: 58px;">
                                <span class="badge {{ $isHariIni ? 'bg-primary' : 'bg-secondary' }}" style="font-size: 11px; padding: 4px 8px;">
                                    {{ $h }}
                                </span>
                                @if ($isHariIni)
                                    <div class="text-primary fw-bold mt-1" style="font-size: 10px; text-transform: uppercase;">Hari Ini</div>
                                @endif
                            </div>
                            <div>
                                @if ($j)
                                    <strong class="text-dark" style="font-size: 14px;">
                                        {{ $j->nama_cabang }}
                                    </strong>
                                    <div class="text-muted small mt-1">
                                        {{ $j->keterangan ?: 'Posko Operasional' }} &bull; Radius: {{ $j->radius_cabang }}m
                                    </div>
                                @else
                                    <div class="text-muted fst-italic" style="font-size: 13px;">
                                        - Libur / Tidak Beroperasi -
                                    </div>
                                    <div class="text-muted small mt-1">
                                        Tidak ada jadwal layanan
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($j && !empty($j->lokasi_cabang))
                            @php
                                $c = explode(',', $j->lokasi_cabang);
                                $map = count($c) >= 2 ? 'https://www.google.com/maps?q=' . trim($c[0]) . ',' . trim($c[1]) : null;
                            @endphp
                            @if ($map)
                                <a href="{{ $map }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 12px; border-radius: 4px;" title="Buka di Maps">
                                    <ion-icon name="navigate-outline"></ion-icon>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
