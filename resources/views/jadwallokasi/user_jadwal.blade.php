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
                    @if (!empty($isEventKhususHariIni))
                        <span class="badge bg-warning text-dark" style="font-size: 11px;">
                            <ion-icon name="star" style="font-size: 12px; vertical-align: -1px;"></ion-icon> Event Khusus
                        </span>
                    @else
                        <span class="badge bg-success" style="font-size: 11px;">Aktif</span>
                    @endif
                @else
                    <span class="badge bg-danger" style="font-size: 11px;">Libur</span>
                @endif
            </div>

            @if ($jadwalHariIni)
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div>
                        <div style="font-size: 17px; font-weight: 700; color: #1e293b;">
                            {{ $jadwalHariIni->nama_cabang }}
                        </div>
                        @if (!empty($jadwalHariIni->nama_jam_kerja))
                            <div class="text-primary small fw-semibold mt-1">
                                <ion-icon name="time-outline" style="vertical-align: -1px;"></ion-icon> {{ $jadwalHariIni->nama_jam_kerja }} ({{ substr($jadwalHariIni->jam_masuk, 0, 5) }} - {{ substr($jadwalHariIni->jam_pulang, 0, 5) }})
                            </div>
                        @endif
                        @if (!empty($jadwalHariIni->keterangan))
                            <div class="text-muted small mt-1">
                                {{ $jadwalHariIni->keterangan }}
                            </div>
                        @endif
                    </div>
                    @php
                        $coords = explode(',', $jadwalHariIni->lokasi_cabang);
                        $mapsUrl =
                            count($coords) >= 2
                                ? 'https://www.google.com/maps?q=' . trim($coords[0]) . ',' . trim($coords[1])
                                : '#';
                    @endphp
                    @if ($mapsUrl !== '#')
                        <a href="{{ $mapsUrl }}" target="_blank"
                            class="btn btn-sm btn-success text-white d-inline-flex align-items-center justify-content-center"
                            style="padding: 4px 12px; font-size: 12px; border-radius: 6px;">
                            <ion-icon name="location" class="me-1"
                                style="font-size: 15px; margin: 0 4px 0 0 !important;"></ion-icon> Buka Peta
                        </a>
                    @endif
                </div>
            @else
                <div class="mt-2">
                    <div style="font-size: 17px; font-weight: 700; color: #1e293b;">
                        Libur
                    </div>
                </div>
            @endif
        </div>

        @if (isset($jadwalEventMendatang) && count($jadwalEventMendatang) > 0)
            <!-- Jadwal Event Khusus Mendatang -->
            <div class="d-flex justify-content-between align-items-center px-3 mt-3 mb-2">
                <span class="text-muted small fw-bold text-uppercase">Jadwal Event Khusus</span>
                <span class="badge bg-warning text-dark" style="font-size: 11px;">{{ count($jadwalEventMendatang) }} Event</span>
            </div>
            <div class="bg-white border-top border-bottom mb-2">
                @foreach ($jadwalEventMendatang as $ev)
                    @php
                        $isEventHariIni = ($ev->tanggal == date('Y-m-d'));
                        $cCoords = explode(',', $ev->lokasi_cabang);
                        $evMap = count($cCoords) >= 2 ? 'https://www.google.com/maps?q=' . trim($cCoords[0]) . ',' . trim($cCoords[1]) : null;
                    @endphp
                    <div class="px-3 py-3 border-bottom {{ $isEventHariIni ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <div style="min-width: 75px; text-align: center;">
                                    <span class="badge bg-warning text-dark" style="font-size: 11px; padding: 4px 6px; border-radius: 6px;">
                                        {{ date('d/m/Y', strtotime($ev->tanggal)) }}
                                    </span>
                                    <div class="small text-muted mt-1">{{ $ev->hari }}</div>
                                </div>
                                <div>
                                    <strong class="text-dark" style="font-size: 14px;">{{ $ev->nama_cabang }}</strong>
                                    @if (!empty($ev->nama_jam_kerja))
                                        <div class="small text-primary fw-semibold">
                                            <ion-icon name="time-outline" style="vertical-align: -1px;"></ion-icon> {{ substr($ev->jam_masuk, 0, 5) }} - {{ substr($ev->jam_pulang, 0, 5) }}
                                        </div>
                                    @endif
                                    @if (!empty($ev->keterangan))
                                        <div class="small text-muted">{{ $ev->keterangan }}</div>
                                    @endif
                                </div>
                            </div>
                            @if ($evMap)
                                <a href="{{ $evMap }}" target="_blank"
                                    class="btn btn-icon btn-warning text-dark"
                                    style="width: 36px !important; height: 36px !important; min-width: 36px !important; border-radius: 8px !important; padding: 0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; line-height: 1 !important;"
                                    title="Buka di Maps">
                                    <ion-icon name="location" style="margin: 0 !important; font-size: 20px !important; line-height: 1 !important;"></ion-icon>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- 2. Header Jadwal Mingguan -->
        <div class="d-flex justify-content-between align-items-center px-3 mt-3 mb-2">
            <span class="text-muted small fw-bold text-uppercase">Jadwal Mingguan Rutin</span>
        </div>

        <!-- 3. Daftar Jadwal Dengan Pembatas Biasa (Tanpa Card) -->
        <div class="bg-white border-top border-bottom">
            @php
                $listSemuaHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            @endphp

            @foreach ($listSemuaHari as $h)
                @php
                    $j = $jadwalMingguan->firstWhere('hari', $h);
                    $isHariIni = $h === $namahariIni;
                    if (!$j) {
                        $badgeClass = 'bg-danger';
                    } elseif ($isHariIni) {
                        $badgeClass = 'bg-success';
                    } else {
                        $badgeClass = 'bg-secondary';
                    }
                @endphp
                <div class="px-3 py-3 border-bottom {{ $isHariIni ? 'bg-light' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <div style="min-width: 58px; text-align: center;">
                                <span class="badge {{ $badgeClass }}"
                                    style="font-size: 11px; padding: 5px 8px; border-radius: 6px;">
                                    {{ $h }}
                                </span>
                            </div>
                            <div>
                                <strong class="text-dark" style="font-size: 14px;">
                                    {{ $j ? $j->nama_cabang : 'Libur' }}
                                </strong>
                                @if ($j && !empty($j->nama_jam_kerja))
                                    <div class="small text-muted font-monospace">
                                        {{ substr($j->jam_masuk, 0, 5) }} - {{ substr($j->jam_pulang, 0, 5) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($j && !empty($j->lokasi_cabang))
                            @php
                                $c = explode(',', $j->lokasi_cabang);
                                $map =
                                    count($c) >= 2
                                        ? 'https://www.google.com/maps?q=' . trim($c[0]) . ',' . trim($c[1])
                                        : null;
                            @endphp
                            @if ($map)
                                <a href="{{ $map }}" target="_blank"
                                    class="btn btn-icon {{ $isHariIni ? 'btn-success' : 'btn-primary' }} text-white"
                                    style="width: 36px !important; height: 36px !important; min-width: 36px !important; border-radius: 8px !important; padding: 0 !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; line-height: 1 !important;"
                                    title="Buka di Maps">
                                    <ion-icon name="location"
                                        style="margin: 0 !important; font-size: 20px !important; line-height: 1 !important;"></ion-icon>
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
