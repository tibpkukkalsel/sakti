@extends('layouts.monitoring')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/monitoring/dashboard" class="headerButton goBack">
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
        .day-pills-scroll {
            display: flex;
            gap: 6px;
            overflow-x: auto;
            padding: 4px 2px 10px 2px;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }

        .day-pills-scroll::-webkit-scrollbar {
            display: none;
        }

        .day-pill {
            padding: 7px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #64748b;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.2s;
        }

        .day-pill.active {
            background: #2A8BF2;
            color: #ffffff;
            border-color: #2A8BF2;
            box-shadow: 0 2px 8px rgba(42, 139, 242, 0.3);
        }

        .jadwal-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }
    </style>

    <div class="section" style="padding-top: 70px; padding-bottom: 75px;">
        <!-- Day Filter Horizontal Scroll -->
        <div class="day-pills-scroll mb-2">
            <a href="/monitoring/jadwal?hari=all" class="day-pill {{ empty($hariPilihan) || $hariPilihan == 'all' ? 'active' : '' }}">
                Semua Hari
            </a>
            @foreach ($listHari as $h)
                <a href="/monitoring/jadwal?hari={{ $h }}" class="day-pill {{ $hariPilihan == $h ? 'active' : '' }}">
                    Hari {{ $h }}
                </a>
            @endforeach
        </div>

        @forelse ($jadwalList as $item)
            <div class="jadwal-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge bg-primary text-white font-weight-bold" style="font-size: 11px;">
                        <ion-icon name="calendar-outline" style="vertical-align:-1px;"></ion-icon> Hari {{ $item->hari }}
                    </span>
                    <span class="badge bg-light text-primary border" style="font-size: 11px;">
                        <ion-icon name="time-outline" style="vertical-align:-1px;"></ion-icon>
                        {{ $item->jam_masuk ? substr($item->jam_masuk, 0, 5) . ' - ' . substr($item->jam_pulang, 0, 5) : 'Jam Operasional Standar' }}
                    </span>
                </div>

                <div class="d-flex align-items-center gap-3">
                    @if (!empty($item->foto_layanan))
                        <img src="{{ Storage::url('uploads/karyawan/' . $item->foto_layanan) }}"
                            style="width: 46px; height: 46px; border-radius: 50%; object-fit: cover; border: 1.5px solid #e2e8f0;"
                            alt="Foto Layanan" onerror="this.src='{{ asset('assets/img/avatar_layanan.png') }}'">
                    @else
                        <img src="{{ asset('assets/img/avatar_layanan.png') }}"
                            style="width: 46px; height: 46px; border-radius: 50%; object-fit: cover; background: #fff; border: 1.5px solid #e2e8f0;"
                            alt="Avatar Layanan">
                    @endif

                    <div style="flex: 1; min-width: 0;">
                        <h4 class="mb-0 font-weight-bold text-dark" style="font-size: 14px;">{{ $item->nama_layanan }}</h4>
                        <div class="text-primary font-weight-bold small mt-1">
                            <ion-icon name="location" style="vertical-align:-1px; color:#ef4444;"></ion-icon> {{ $item->nama_cabang }}
                        </div>
                        <div class="text-muted small">
                            PIC: {{ $item->nama_pic ?: '-' }}
                            @if (!empty($item->radius_cabang))
                                &bull; Radius: {{ $item->radius_cabang }} meter
                            @endif
                        </div>
                    </div>
                </div>

                @if (!empty($item->lokasi_cabang))
                    <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                        <small class="text-muted font-monospace" style="font-size: 10.5px;">{{ $item->lokasi_cabang }}</small>
                        <a href="https://maps.google.com/?q={{ $item->lokasi_cabang }}" target="_blank"
                            class="btn btn-sm btn-outline-primary" style="border-radius: 20px; font-size: 11px; padding: 2px 10px;">
                            <ion-icon name="map-outline" style="vertical-align:-1px;"></ion-icon> Peta
                        </a>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-5">
                <ion-icon name="calendar-outline" style="font-size: 48px; color: #cbd5e1;"></ion-icon>
                <div class="text-muted mt-2 font-weight-bold">Tidak ada jadwal layanan pada hari ini.</div>
            </div>
        @endforelse
    </div>
@endsection

