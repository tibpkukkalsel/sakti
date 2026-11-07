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
    <div class="section" style="padding-top: 70px; padding-bottom: 75px;">
        @php
            $adaJadwal = false;
        @endphp

        @foreach ($listHari as $h)
            @if ($jadwalByHari->has($h) && count($jadwalByHari[$h]) > 0)
                @php
                    $adaJadwal = true;
                    $items = $jadwalByHari[$h];
                    $isHariIni = ($h === $hariini);
                @endphp
                <!-- Card Jadwal per Hari -->
                <div class="card mb-3" style="border-radius: 14px; border: 1px solid {{ $isHariIni ? '#93c5fd' : '#e2e8f0' }}; box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow: hidden;">
                    <!-- Card Header Hari -->
                    <div class="card-header d-flex justify-content-between align-items-center py-2 px-3"
                        style="background: {{ $isHariIni ? 'linear-gradient(135deg, #2A8BF2 0%, #1A73E8 100%)' : '#f8fafc' }}; color: {{ $isHariIni ? '#ffffff' : '#1e293b' }}; border-bottom: 1px solid {{ $isHariIni ? '#2A8BF2' : '#e2e8f0' }};">
                        <div class="d-flex align-items-center gap-2">
                            <ion-icon name="calendar" style="font-size: 18px; color: {{ $isHariIni ? '#fde047' : '#2A8BF2' }};"></ion-icon>
                            <strong style="font-size: 14px; letter-spacing: 0.2px;">Hari {{ $h }}</strong>
                            @if ($isHariIni)
                                <span class="badge bg-warning text-dark" style="font-size: 10px; padding: 2px 6px;">Hari Ini</span>
                            @endif
                        </div>
                        <span class="badge {{ $isHariIni ? 'bg-light text-primary' : 'bg-light text-secondary border' }}" style="font-size: 11px;">
                            {{ count($items) }} Layanan
                        </span>
                    </div>

                    <!-- Card Body: Daftar Layanan pada Hari Tersebut -->
                    <div class="card-body p-0">
                        @foreach ($items as $idx => $item)
                            <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                <div class="d-flex align-items-start gap-3">
                                    <div style="width: 42px; height: 42px; border-radius: 12px; background: rgba(42, 139, 242, 0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px;">
                                        <ion-icon name="bus-outline" style="font-size: 22px; color: #2A8BF2;"></ion-icon>
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h4 class="mb-0 font-weight-bold text-dark" style="font-size: 14px;">
                                                {{ $item->nama_layanan }}
                                            </h4>
                                            @if (!empty($item->nama_jam_kerja))
                                                <span class="badge bg-light text-primary border" style="font-size: 10.5px;">
                                                    {{ $item->nama_jam_kerja }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Lokasi Pelayanan (Bukan Posko) -->
                                        <div style="font-size: 13px; color: #1e293b; margin-bottom: 3px;">
                                            <ion-icon name="location" style="vertical-align: -1px; color: #ef4444; font-size: 14px;"></ion-icon>
                                            <strong>{{ $item->nama_cabang }}</strong>
                                        </div>

                                        <!-- Jam Operasional -->
                                        <div class="text-muted small">
                                            <ion-icon name="time-outline" style="vertical-align: -1px; color: #64748b; font-size: 13px;"></ion-icon>
                                            Jam Operasional:
                                            <strong style="color: #0f172a;">
                                                {{ $item->jam_masuk ? substr($item->jam_masuk, 0, 5) . ' - ' . substr($item->jam_pulang, 0, 5) : '08:00 - 14:00' }}
                                            </strong>
                                        </div>

                                        @if (!empty($item->keterangan))
                                            <div class="text-muted small mt-1" style="font-size: 11.5px;">
                                                {{ $item->keterangan }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        @if (!$adaJadwal)
            <div class="text-center py-5">
                <ion-icon name="calendar-outline" style="font-size: 48px; color: #cbd5e1;"></ion-icon>
                <div class="text-muted mt-2 font-weight-bold">Belum ada data jadwal layanan.</div>
            </div>
        @endif
    </div>
@endsection
