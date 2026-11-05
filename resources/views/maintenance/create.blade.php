@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/maintenance" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Form Maintenance Layanan</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <style>
        body {
            background-color: #f8fafc !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        #appCapsule {
            padding-top: 65px !important;
            padding-bottom: 95px !important;
        }

        .form-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 20px 16px;
            margin: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f5f9;
        }

        .input-group-custom {
            margin-bottom: 16px;
        }

        .input-group-custom label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
            display: block;
        }

        .form-control-custom {
            height: 48px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            padding: 10px 14px;
            font-size: 15px;
            width: 100%;
            transition: all 0.2s;
            background-color: #fff;
            color: #1e293b;
        }

        .form-control-custom:focus {
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
            outline: none;
        }

        textarea.form-control-custom {
            height: auto;
            min-height: 90px;
            resize: vertical;
        }
    </style>

    <div class="section pt-1">
        @if (Session::get('error'))
            <div class="alert alert-danger mx-3 mt-2" style="border-radius: 12px;">
                <div class="d-flex align-items-center gap-2">
                    <ion-icon name="alert-circle-outline" style="font-size: 22px;"></ion-icon>
                    <span>{{ Session::get('error') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mx-3 mt-2" style="border-radius: 12px;">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-card">
            <!-- Informasi Panduan -->
            <div class="p-3 mb-3" style="background: #fffbeb; border: 1px solid #fef3c7; border-left: 4px solid #f59e0b; border-radius: 12px;">
                <div class="d-flex align-items-start gap-2">
                    <ion-icon name="information-circle" style="font-size: 24px; color: #d97706; flex-shrink: 0; margin-top: 1px;"></ion-icon>
                    <div style="font-size: 12px; color: #78350f; line-height: 1.4;">
                        <strong>Catatan Unit OFF / Pemeliharaan:</strong><br>
                        Gunakan form ini jika unit layanan sedang dalam perawatan/perbaikan teknis. Status presensi layanan otomatis tercatat sebagai <strong>Maintenance</strong> pada rentang tanggal yang dipilih.
                    </div>
                </div>
            </div>

            <form action="/maintenance/store" method="POST" id="formMaintenance" autocomplete="off">
                @csrf

                <!-- Akun Layanan (Readonly) -->
                <div class="input-group-custom">
                    <label>Akun Layanan</label>
                    <div class="form-control-custom d-flex align-items-center" style="background-color: #f1f5f9; font-weight: 600; color: #334155;">
                        <ion-icon name="car-sport-outline" class="me-2" style="font-size: 20px; color: #2563eb;"></ion-icon>
                        {{ $user->nama_layanan }}
                    </div>
                </div>

                <!-- Rentang Tanggal -->
                <div class="row">
                    <div class="col-6">
                        <div class="input-group-custom">
                            <label>Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_izin_dari" id="tgl_izin_dari" class="form-control-custom"
                                value="{{ old('tgl_izin_dari', $hariini) }}" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group-custom">
                            <label>Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="tgl_izin_sampai" id="tgl_izin_sampai" class="form-control-custom"
                                value="{{ old('tgl_izin_sampai', $hariini) }}" required>
                        </div>
                    </div>
                </div>

                <!-- Keterangan / Alasan -->
                <div class="input-group-custom">
                    <label>Keterangan / Alasan Kendala Unit <span class="text-danger">*</span></label>
                    <textarea name="keterangan" id="keterangan" class="form-control-custom" rows="3"
                        placeholder="Contoh: Perbaikan genset keliling, servis armada berkala, kendala jaringan online, perbaikan AC unit, dll."
                        required>{{ old('keterangan') }}</textarea>
                </div>

                <!-- Tombol Aksi -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-warning btn-block text-dark fw-bold" style="border-radius: 12px; padding: 13px; font-size: 15px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);">
                        <ion-icon name="construct" class="me-1"></ion-icon> Simpan Status Maintenance (Unit OFF)
                    </button>
                    <a href="/maintenance" class="btn btn-light btn-block mt-2 text-muted fw-semibold" style="border-radius: 12px; padding: 12px; font-size: 14px;">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tglDari = document.getElementById('tgl_izin_dari');
            var tglSampai = document.getElementById('tgl_izin_sampai');

            if (tglDari && tglSampai) {
                tglDari.addEventListener('change', function() {
                    if (tglSampai.value < tglDari.value) {
                        tglSampai.value = tglDari.value;
                    }
                    tglSampai.min = tglDari.value;
                });
            }
        });
    </script>
@endsection

