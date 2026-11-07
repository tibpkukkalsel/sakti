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

        <!-- Filter Tab di Atas (Hanya Mingguan & Event, Default: Mingguan) -->
        <div class="presencetab mb-3">
            <ul class="nav nav-tabs style1" role="tablist">
                <li class="nav-item" style="width: 50%;">
                    <a class="nav-link active" data-toggle="tab" href="#tab-mingguan" role="tab"
                        style="font-weight: 700; font-size: 13.5px; text-align: center; padding: 12px 6px;">
                        <ion-icon name="calendar-outline" style="vertical-align: -2px; margin-right: 8px !important; font-size: 16px;"></ion-icon>
                        Jadwal Mingguan
                    </a>
                </li>
                <li class="nav-item" style="width: 50%;">
                    <a class="nav-link" data-toggle="tab" href="#tab-event" role="tab"
                        style="font-weight: 700; font-size: 13.5px; text-align: center; padding: 12px 6px;">
                        <ion-icon name="sparkles-outline" style="vertical-align: -2px; margin-right: 8px !important; font-size: 16px;"></ion-icon>
                        Event
                        @if (count($eventKhusus) > 0)
                            <span class="badge bg-warning text-dark ml-1" style="font-size: 10px; padding: 2px 6px; margin-left: 6px !important;">
                                {{ count($eventKhusus) }}
                            </span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>

        <div class="tab-content">
            <!-- ================= TAB 1: JADWAL MINGGUAN (DEFAULT AKTIF) ================= -->
            <div class="tab-pane fade show active" id="tab-mingguan" role="tabpanel">
                @php
                    $adaJadwalMingguan = false;
                @endphp

                @foreach ($listHari as $h)
                    @if ($jadwalByHari->has($h) && count($jadwalByHari[$h]) > 0)
                        @php
                            $adaJadwalMingguan = true;
                            $items = $jadwalByHari[$h];
                            $isHariIni = ($h === $hariini);
                        @endphp
                        <!-- Satu Card untuk Hari Ini yang Menggabungkan Seluruh Layanan -->
                        <div class="card mb-3"
                            style="border-radius: 14px; border: 1px solid {{ $isHariIni ? '#93c5fd' : '#e2e8f0' }}; box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow: hidden;">
                            <!-- Header Hari -->
                            <div class="card-header d-flex justify-content-between align-items-center py-2 px-3"
                                style="background: {{ $isHariIni ? 'linear-gradient(135deg, #2A8BF2 0%, #1A73E8 100%)' : '#f8fafc' }}; color: {{ $isHariIni ? '#ffffff' : '#1e293b' }}; border-bottom: 1px solid {{ $isHariIni ? '#2A8BF2' : '#e2e8f0' }};">
                                <div class="d-flex align-items-center">
                                    <ion-icon name="calendar"
                                        style="font-size: 18px; color: {{ $isHariIni ? '#fde047' : '#2A8BF2' }}; margin-right: 8px !important;"></ion-icon>
                                    <strong style="font-size: 14px; letter-spacing: 0.2px;">Hari {{ $h }}</strong>
                                    @if ($isHariIni)
                                        <span class="badge bg-warning text-dark" style="font-size: 10px; padding: 2px 6px; margin-left: 8px !important;">
                                            Hari Ini
                                        </span>
                                    @endif
                                </div>
                                <span class="badge {{ $isHariIni ? 'bg-light text-primary' : 'bg-light text-secondary border' }}" style="font-size: 11px;">
                                    {{ count($items) }} Layanan
                                </span>
                            </div>

                            <!-- Body: Daftar Seluruh Layanan pada Hari Tersebut -->
                            <div class="card-body p-0">
                                @foreach ($items as $idx => $item)
                                    <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex align-items-start">
                                            <!-- Icon Armada dengan Jarak Nyaman -->
                                            <div
                                                style="width: 44px; height: 44px; min-width: 44px; border-radius: 12px; background: rgba(42, 139, 242, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-right: 15px !important; margin-top: 2px;">
                                                <ion-icon name="bus-outline" style="font-size: 24px; color: #2A8BF2;"></ion-icon>
                                            </div>

                                            <div style="flex: 1; min-width: 0;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h4 class="mb-0 font-weight-bold text-dark" style="font-size: 14.5px;">
                                                        {{ $item->nama_layanan }}
                                                    </h4>
                                                    @if (!empty($item->nama_jam_kerja))
                                                        <span class="badge bg-light text-primary border" style="font-size: 10.5px;">
                                                            {{ $item->nama_jam_kerja }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Lokasi Pelayanan -->
                                                <div style="font-size: 13px; color: #1e293b; margin-bottom: 5px !important; line-height: 1.4;">
                                                    <ion-icon name="location"
                                                        style="vertical-align: -2px; color: #ef4444; font-size: 15px; margin-right: 6px !important;"></ion-icon>
                                                    <strong>{{ $item->nama_cabang }}</strong>
                                                </div>

                                                <!-- Jam Operasional -->
                                                <div class="text-muted small" style="margin-bottom: 3px !important; line-height: 1.4;">
                                                    <ion-icon name="time-outline"
                                                        style="vertical-align: -2px; color: #64748b; font-size: 14px; margin-right: 6px !important;"></ion-icon>
                                                    Jam Operasional:
                                                    <strong style="color: #0f172a;">
                                                        {{ $item->jam_masuk ? substr($item->jam_masuk, 0, 5) . ' - ' . substr($item->jam_pulang, 0, 5) : '08:00 - 14:00' }}
                                                    </strong>
                                                </div>

                                                @if (!empty($item->keterangan))
                                                    <div class="text-muted small mt-1" style="font-size: 11.5px; padding-left: 20px;">
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

                @if (!$adaJadwalMingguan)
                    <div class="text-center py-5">
                        <ion-icon name="calendar-outline" style="font-size: 48px; color: #cbd5e1;"></ion-icon>
                        <div class="text-muted mt-2 font-weight-bold">Belum ada data jadwal mingguan.</div>
                    </div>
                @endif
            </div>

            <!-- ================= TAB 2: EVENT KHUSUS ================= -->
            <div class="tab-pane fade" id="tab-event" role="tabpanel">
                @if ($eventKhusus->isEmpty())
                    <div class="card" style="border-radius: 14px; border: 1px dashed #cbd5e1;">
                        <div class="card-body text-center py-5">
                            <ion-icon name="sparkles-outline" style="font-size: 46px; color: #94a3b8;"></ion-icon>
                            <h4 class="mt-2 mb-1 font-weight-bold text-dark" style="font-size: 15px;">Tidak Ada Event Mendatang</h4>
                            <p class="text-muted small mb-0">Belum ada jadwal event khusus pelayanan yang diagendakan saat ini.</p>
                        </div>
                    </div>
                @else
                    @foreach ($eventByTanggal as $tgl => $eventItems)
                        @php
                            $isEventHariIni = ($tgl === date('Y-m-d'));
                            $tglCarbon = \Carbon\Carbon::parse($tgl);
                        @endphp
                        <!-- Satu Card untuk Tanggal Event -->
                        <div class="card mb-3"
                            style="border-radius: 14px; border: 1px solid {{ $isEventHariIni ? '#fde047' : '#fed7aa' }}; box-shadow: 0 2px 8px rgba(0,0,0,0.04); overflow: hidden;">
                            <!-- Header Tanggal Event -->
                            <div class="card-header d-flex justify-content-between align-items-center py-2 px-3"
                                style="background: {{ $isEventHariIni ? '#fef08a' : '#fff7ed' }}; color: #9a3412; border-bottom: 1px solid #fed7aa;">
                                <div class="d-flex align-items-center">
                                    <ion-icon name="star"
                                        style="font-size: 17px; color: #ea580c; margin-right: 8px !important;"></ion-icon>
                                    <strong style="font-size: 14px; letter-spacing: 0.2px; color: #9a3412;">
                                        {{ $tglCarbon->translatedFormat('l, d F Y') }}
                                    </strong>
                                    @if ($isEventHariIni)
                                        <span class="badge bg-danger text-white" style="font-size: 10px; padding: 2px 6px; margin-left: 8px !important;">
                                            Hari Ini
                                        </span>
                                    @endif
                                </div>
                                <span class="badge bg-warning text-dark" style="font-size: 11px;">
                                    {{ count($eventItems) }} Layanan
                                </span>
                            </div>

                            <!-- Body: Daftar Layanan pada Event Tersebut -->
                            <div class="card-body p-0">
                                @foreach ($eventItems as $idx => $ev)
                                    <div class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                        <div class="d-flex align-items-start">
                                            <!-- Icon Event dengan Jarak Nyaman -->
                                            <div
                                                style="width: 44px; height: 44px; min-width: 44px; border-radius: 12px; background: rgba(234, 88, 12, 0.12); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-right: 15px !important; margin-top: 2px;">
                                                <ion-icon name="sparkles" style="font-size: 22px; color: #ea580c;"></ion-icon>
                                            </div>

                                            <div style="flex: 1; min-width: 0;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <h4 class="mb-0 font-weight-bold text-dark" style="font-size: 14.5px;">
                                                        {{ $ev->nama_layanan }}
                                                    </h4>
                                                    <span class="badge bg-warning text-dark" style="font-size: 10.5px;">
                                                        Event Khusus
                                                    </span>
                                                </div>

                                                <!-- Lokasi Pelayanan -->
                                                <div style="font-size: 13px; color: #1e293b; margin-bottom: 5px !important; line-height: 1.4;">
                                                    <ion-icon name="location"
                                                        style="vertical-align: -2px; color: #ef4444; font-size: 15px; margin-right: 6px !important;"></ion-icon>
                                                    <strong>{{ $ev->nama_cabang }}</strong>
                                                </div>

                                                <!-- Jam Operasional -->
                                                <div class="text-muted small" style="margin-bottom: 3px !important; line-height: 1.4;">
                                                    <ion-icon name="time-outline"
                                                        style="vertical-align: -2px; color: #64748b; font-size: 14px; margin-right: 6px !important;"></ion-icon>
                                                    Jam Operasional:
                                                    <strong style="color: #0f172a;">
                                                        {{ $ev->jam_masuk ? substr($ev->jam_masuk, 0, 5) . ' - ' . substr($ev->jam_pulang, 0, 5) : '08:00 - 14:00' }}
                                                    </strong>
                                                </div>

                                                @if (!empty($ev->keterangan))
                                                    <div class="text-muted small mt-1" style="font-size: 11.5px; padding-left: 20px;">
                                                        {{ $ev->keterangan }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>
@endsection
