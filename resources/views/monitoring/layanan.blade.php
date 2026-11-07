@extends('layouts.monitoring')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/monitoring/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Data Layanan</div>
        <div class="right">
            <span class="badge bg-light text-primary font-weight-bold" style="font-size: 11px;">
                {{ $totalLayanan }} Unit
            </span>
        </div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <style>
        .service-list-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 14px;
            margin-bottom: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .service-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
            background: #ffffff;
            flex-shrink: 0;
        }

        .btn-wa-action {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #25d366;
            color: #ffffff !important;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(37, 211, 102, 0.25);
        }
    </style>

    <div class="section" style="padding-top: 70px; padding-bottom: 75px;">
        <!-- Search Box -->
        <div class="mb-3 px-1">
            <form action="/monitoring/layanan" method="GET">
                <div class="input-group" style="background: #ffffff; border-radius: 12px; border: 1px solid #cbd5e1; overflow: hidden; padding: 2px 8px; display: flex; align-items: center;">
                    <ion-icon name="search-outline" style="font-size: 20px; color: #64748b; margin-right: 6px;"></ion-icon>
                    <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari nama layanan atau PIC..." style="border: none; outline: none; box-shadow: none; font-size: 13.5px; height: 38px;">
                    @if (!empty($search))
                        <a href="/monitoring/layanan" class="text-muted" style="text-decoration: none; margin-right: 6px;">
                            <ion-icon name="close-circle" style="font-size: 18px;"></ion-icon>
                        </a>
                    @endif
                    <button type="submit" class="btn btn-sm btn-primary" style="border-radius: 8px; padding: 4px 12px; background-color: #2A8BF2; border-color: #2A8BF2;">Cari</button>
                </div>
            </form>
        </div>

        @forelse ($layananList as $item)
            <div class="service-list-card">
                <div class="d-flex align-items-center gap-3">
                    @if (!empty($item->foto))
                        <img src="{{ Storage::url('uploads/karyawan/' . $item->foto) }}" class="service-avatar"
                            alt="Foto Layanan" onerror="this.src='{{ asset('assets/img/avatar_layanan.png') }}'">
                    @else
                        <img src="{{ asset('assets/img/avatar_layanan.png') }}" class="service-avatar" alt="Avatar Layanan">
                    @endif

                    <div style="flex: 1; min-width: 0;">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 font-weight-bold text-dark" style="font-size: 14px;">{{ $item->nama_layanan }}</h4>
                            <span class="badge bg-light text-primary border" style="font-size: 10px;">ID: {{ $item->id }}</span>
                        </div>
                        <div class="text-muted small mt-1">
                            PIC: <strong>{{ $item->nama_pic ?: '-' }}</strong>
                        </div>
                        <div class="text-muted small" style="margin-top: 2px;">
                            <ion-icon name="navigate-outline" style="vertical-align: -2px; color: #2A8BF2; margin-right: 4px;"></ion-icon> Lokasi: <strong>{{ $item->lokasi_hari_ini }}</strong>
                        </div>
                    </div>
                </div>

                <div class="mt-3 pt-2 border-top d-flex align-items-center justify-content-between">
                    <div>
                        <span class="badge bg-success text-white" style="font-size: 10.5px;">
                            Siap Layanan
                        </span>
                    </div>

                    @if (!empty($item->no_hp))
                        @php
                            $waNumber = preg_replace('/[^0-9]/', '', $item->no_hp);
                            if (str_starts_with($waNumber, '0')) {
                                $waNumber = '62' . substr($waNumber, 1);
                            }
                        @endphp
                        <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="btn-wa-action">
                            <ion-icon name="logo-whatsapp" style="font-size: 14px;"></ion-icon>
                            <span>Hubungi PIC</span>
                        </a>
                    @else
                        <span class="text-muted small">No HP: -</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <ion-icon name="bus-outline" style="font-size: 48px; color: #cbd5e1;"></ion-icon>
                <div class="text-muted mt-2 font-weight-bold">Tidak ada data layanan ditemukan.</div>
            </div>
        @endforelse
    </div>
@endsection

