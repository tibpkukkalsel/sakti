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
    <style>
        body {
            background-color: #f8fafc !important;
        }

        #appCapsule {
            padding-top: 65px !important;
            padding-bottom: 95px !important;
        }

        .today-card {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border-radius: 20px;
            padding: 20px 18px;
            margin: 12px 14px 18px 14px;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.25);
        }

        .day-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            backdrop-filter: blur(4px);
        }

        .schedule-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 14px 16px;
            margin-bottom: 12px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .schedule-card.is-today {
            border: 2px solid #3b82f6;
            background: #eff6ff;
        }
    </style>

    <div class="section pt-1">
        <!-- Card Lokasi Hari Ini -->
        <div class="today-card">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="day-badge"><ion-icon name="calendar-outline" class="me-1"></ion-icon> Hari Ini ({{ $namahariIni }})</span>
                <span style="font-size: 11px; opacity: 0.85;">{{ $user->nama_layanan }}</span>
            </div>

            @if ($jadwalHariIni)
                <h3 style="font-size: 20px; font-weight: 800; margin-bottom: 4px;">
                    {{ $jadwalHariIni->nama_cabang }}
                </h3>
                <p style="font-size: 12px; opacity: 0.92; margin-bottom: 10px;">
                    <ion-icon name="pin-outline" class="me-1"></ion-icon> {{ $jadwalHariIni->keterangan ?: 'Posko Operasional Layanan' }}
                </p>
                <div class="d-flex align-items-center justify-content-between pt-2 border-top border-white-50">
                    <span style="font-size: 11.5px; opacity: 0.9;">Radius Presensi: <strong>{{ $jadwalHariIni->radius_cabang }} meter</strong></span>
                    @php
                        $coords = explode(',', $jadwalHariIni->lokasi_cabang);
                        $mapsUrl = count($coords) >= 2 ? 'https://www.google.com/maps?q=' . trim($coords[0]) . ',' . trim($coords[1]) : '#';
                    @endphp
                    <a href="{{ $mapsUrl }}" target="_blank" class="btn btn-sm btn-light text-primary" style="border-radius: 10px; font-weight: 600; padding: 4px 12px; font-size: 11px;">
                        <ion-icon name="navigate-outline" class="me-1"></ion-icon> Buka Peta
                    </a>
                </div>
            @else
                <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 4px;">
                    Lokasi Kantor Induk / Libur
                </h3>
                <p style="font-size: 12px; opacity: 0.85; margin-bottom: 0;">
                    Tidak ada jadwal keliling khusus hari ini. Presensi mengacu pada lokasi default UPPD SAMSAT Batu Licin.
                </p>
            @endif
        </div>

        <!-- Jadwal Mingguan Lengkap -->
        <div class="px-3">
            <h4 style="font-size: 14px; font-weight: 700; color: #334155; margin-bottom: 12px;">
                <ion-icon name="time-outline" class="me-1 text-primary"></ion-icon> Jadwal Operasional Mingguan
            </h4>

            @php
                $listSemuaHari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
            @endphp

            @foreach ($listSemuaHari as $h)
                @php
                    $j = $jadwalMingguan->firstWhere('hari', $h);
                    $isHariIni = ($h === $namahariIni);
                @endphp
                <div class="schedule-card {{ $isHariIni ? 'is-today' : '' }}">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width: 48px; text-align: center;">
                            <span class="badge {{ $isHariIni ? 'badge-primary' : 'badge-secondary' }}" style="font-size: 11px; padding: 5px 8px; border-radius: 8px;">
                                {{ $h }}
                            </span>
                        </div>
                        <div>
                            @if ($j)
                                <div style="font-size: 14px; font-weight: 700; color: #1e293b;">
                                    {{ $j->nama_cabang }}
                                    @if ($isHariIni)
                                        <span class="badge badge-success ms-1" style="font-size: 9px;">HARI INI</span>
                                    @endif
                                </div>
                                <small class="text-muted d-block" style="font-size: 11px;">
                                    {{ $j->keterangan ?: 'Posko Pelayanan' }} &bull; Radius: {{ $j->radius_cabang }}m
                                </small>
                            @else
                                <div style="font-size: 13px; font-weight: 600; color: #64748b;">
                                    - SAMSAT Induk / Libur -
                                </div>
                                <small class="text-muted" style="font-size: 11px;">Lokasi Default</small>
                            @endif
                        </div>
                    </div>
                    @if ($j && !empty($j->lokasi_cabang))
                        @php
                            $c = explode(',', $j->lokasi_cabang);
                            $map = count($c) >= 2 ? 'https://www.google.com/maps?q=' . trim($c[0]) . ',' . trim($c[1]) : null;
                        @endphp
                        @if ($map)
                            <a href="{{ $map }}" target="_blank" class="text-primary" style="font-size: 22px;">
                                <ion-icon name="map-outline"></ion-icon>
                            </a>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
