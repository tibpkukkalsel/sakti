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
    <div class="row" style="margin-top: 70px; margin-bottom: 70px;">
        <div class="col">
            @if (Session::get('success'))
                <div class="alert alert-success mb-2">
                    {{ Session::get('success') }}
                </div>
            @endif

            @if (Session::get('error'))
                <div class="alert alert-danger mb-2">
                    {{ Session::get('error') }}
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger mb-2">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="/maintenance/store" method="POST" id="formMaintenance">
                @csrf

                <!-- Informasi Panduan -->
                <div class="p-3 my-2 bg-white border rounded">
                    <div class="text-muted small fw-bold text-uppercase mb-1">
                        <ion-icon name="information-circle-outline" style="vertical-align: -2px; font-size: 16px;"></ion-icon>
                        Catatan Unit OFF / Pemeliharaan
                    </div>
                    <div class="text-muted small">
                        Form ini digunakan jika unit layanan sedang dalam perawatan atau perbaikan teknis. Status presensi layanan otomatis tercatat sebagai <strong>Maintenance (Unit OFF)</strong> pada rentang tanggal yang dipilih.
                    </div>
                </div>

                <!-- Akun Layanan -->
                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label">Akun Layanan</label>
                        <input type="text" class="form-control" value="{{ $user->nama_layanan }}" readonly style="background-color: #f8fafc; font-weight: 600;">
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

                <!-- Rentang Tanggal -->
                <div class="row">
                    <div class="col-6">
                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="tgl_izin_dari" id="tgl_izin_dari"
                                    value="{{ old('tgl_izin_dari', $hariini) }}" required>
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="tgl_izin_sampai" id="tgl_izin_sampai"
                                    value="{{ old('tgl_izin_sampai', $hariini) }}" required>
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan Kendala -->
                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label">Keterangan / Alasan Kendala Unit</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control" maxlength="255"
                            placeholder="Keterangan maintenance"
                            required>{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                <!-- Tombol Simpan -->
                <div class="form-group boxed mt-3">
                    <div class="input-wrapper">
                        <button type="submit" class="btn btn-primary btn-block">
                            <ion-icon name="save-outline"></ion-icon>
                            Simpan Status Maintenance
                        </button>
                    </div>
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
