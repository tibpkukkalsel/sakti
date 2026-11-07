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
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <style>
        .service-list-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .service-avatar-wrap {
            width: 54px;
            height: 54px;
            min-width: 54px;
            border-radius: 50%;
            overflow: hidden;
            background: #f0f7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            flex-shrink: 0;
            margin-right: 14px;
        }

        .service-avatar {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .btn-wa-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 30px;
            background: #25d366;
            color: #ffffff !important;
            padding: 0 13px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(37, 211, 102, 0.28);
            transition: all 0.2s ease;
        }

        .btn-wa-action:active {
            transform: scale(0.97);
            box-shadow: 0 1px 3px rgba(37, 211, 102, 0.2);
        }
    </style>

    <div class="section" style="padding-top: 70px; padding-bottom: 75px;">
        @forelse ($layananList as $item)
            <div class="service-list-card">
                <div class="d-flex align-items-center">
                    <!-- Avatar Layanan -->
                    <div class="service-avatar-wrap">
                        @if (!empty($item->foto))
                            <img src="{{ Storage::url('uploads/karyawan/' . $item->foto) }}" class="service-avatar"
                                alt="Foto Layanan" onerror="this.src='{{ asset('assets/img/avatar_layanan.png') }}'">
                        @else
                            <img src="{{ asset('assets/img/avatar_layanan.png') }}" class="service-avatar"
                                alt="Avatar Layanan">
                        @endif
                    </div>

                    <!-- Informasi Layanan Presisi -->
                    <div style="flex: 1; min-width: 0;">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <h4 class="mb-0 font-weight-bold text-dark" style="font-size: 15px; letter-spacing: 0.2px;">
                                {{ $item->nama_layanan }}
                            </h4>
                        </div>

                        <!-- Baris PIC -->
                        <div class="d-flex align-items-center mb-1" style="font-size: 12.5px; line-height: 1.35;">
                            <span style="color: #64748b; margin-right: 4px;">PIC:</span>
                            <strong style="color: #1e293b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $item->nama_pic ?: '-' }}
                            </strong>
                        </div>

                        <!-- Baris Lokasi -->
                        <div class="d-flex align-items-center" style="font-size: 12.5px; line-height: 1.35;">
                            <ion-icon name="location"
                                style="color: #ef4444; font-size: 14px; margin-right: 3px; flex-shrink: 0;"></ion-icon>
                            <span style="color: #64748b; margin-right: 4px;">Lokasi:</span>
                            <strong style="color: #0f172a; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $item->lokasi_hari_ini }}
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Footer Kartu: Status Layanan & Tombol WA -->
                <div class="mt-3 pt-2 d-flex align-items-center justify-content-between"
                    style="border-top: 1px solid #f1f5f9;">
                    <div>
                        <span
                            style="display: inline-flex; align-items: center; justify-content: center; height: 30px; background: rgba(34, 197, 94, 0.12); color: #15803d; font-size: 11.5px; font-weight: 600; padding: 0 12px; border-radius: 20px; border: 1px solid rgba(34, 197, 94, 0.25);">
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
                            <ion-icon name="logo-whatsapp" style="font-size: 15px; margin-right: 5px;"></ion-icon>
                            <span>Hubungi PIC</span>
                        </a>
                    @else
                        <span class="text-muted small" style="font-size: 11.5px;">No. HP: -</span>
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
