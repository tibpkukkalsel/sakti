@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/penerimaan" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Input Penerimaan PKB</div>
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

            @if (Session::get('warning'))
                <div class="alert alert-warning mb-2">
                    {{ Session::get('warning') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger mb-2">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($data->status_verifikasi == 'ditolak' && !empty($data->catatan_verifikasi))
                <div class="alert alert-danger mb-3 p-3" style="border-radius: 10px;">
                    <div class="fw-bold mb-1 d-flex align-items-center gap-1">
                        <ion-icon name="alert-circle" style="font-size: 18px;"></ion-icon> Catatan Revisi Admin:
                    </div>
                    <div style="font-size: 13px;">{{ $data->catatan_verifikasi }}</div>
                </div>
            @endif

            <!-- 1. Ringkasan Awal Layanan (Readonly) -->
            <div class="card mb-3 shadow-none border" style="border-radius: 12px; background: #f8fafc;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted small fw-bold text-uppercase">Data Awal Layanan</span>
                        <span class="badge bg-success" style="font-size: 11px;">Sudah Dibuka</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 14px;">
                                {{ formatHariTanggal($data->tgl_penerimaan) }}
                            </div>
                            <div class="text-muted small">
                                Lokasi: <strong>{{ $data->cabang->nama_cabang ?? 'UPPD SAMSAT Batulicin' }}</strong>
                            </div>
                            <div class="text-primary small mt-1">
                                No. Awal Notice: <strong class="fs-6">{{ $data->no_awal_notice }}</strong>
                            </div>
                        </div>
                        @if ($data->foto_awal_url)
                            <div class="text-center ms-2">
                                <a href="javascript:void(0)" onclick="previewFotoNotice('{{ $data->foto_awal_url }}', 'Bukti Notice Awal')">
                                    <img src="{{ $data->foto_awal_url }}" alt="Foto Awal" class="rounded border shadow-sm" style="width: 55px; height: 55px; object-fit: cover; cursor: pointer;">
                                </a>
                                <div class="text-muted" style="font-size: 10px;">Foto Awal</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- 2. Form Lengkapi Akhir Layanan & Realisasi -->
            <form action="/penerimaan/{{ $data->id }}/kirim-laporan" method="POST" enctype="multipart/form-data" id="formTutupLayanan">
                @csrf

                <div class="card mb-3 shadow-none border" style="border-radius: 12px;">
                    <div class="card-body">
                        <div class="fw-bold text-dark mb-2 pb-1 border-bottom" style="font-size: 13.5px;">
                            1. Notice Akhir Pelayanan
                        </div>

                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Nomor Akhir Notice Pajak</label>
                                <input type="text" class="form-control" name="no_akhir_notice" id="no_akhir_notice"
                                    placeholder="Nomor Seri Lembar Terakhir" value="{{ old('no_akhir_notice', $data->no_akhir_notice) }}" required autocomplete="off" style="font-weight: 700; font-size: 15px;">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>

                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Foto Bukti Lembar Akhir Notice</label>
                                <input type="file" class="form-control" name="foto_akhir_notice" id="foto_akhir_notice"
                                    accept="image/*" capture="environment" {{ empty($data->foto_akhir_notice) ? 'required' : '' }} onchange="previewImage(this, 'previewAkhir')">
                            </div>
                            <div class="mt-2 text-center" id="boxPreviewAkhir" style="{{ empty($data->foto_akhir_url) ? 'display: none;' : '' }}">
                                <img id="previewAkhir" src="{{ $data->foto_akhir_url ?? '#' }}" alt="Preview Foto Akhir" class="img-fluid rounded border shadow-sm" style="max-height: 220px; object-fit: contain;">
                            </div>
                        </div>

                        <div class="fw-bold text-dark mt-4 mb-2 pb-1 border-bottom" style="font-size: 13.5px;">
                            2. Realisasi Penerimaan PKB
                        </div>

                        <!-- Baris Roda 2 (R2) -->
                        <div class="row">
                            <div class="col-7">
                                <div class="form-group boxed">
                                    <div class="input-wrapper">
                                        <label class="label">Penerimaan R2</label>
                                        <input type="text" class="form-control rupiah-input" name="jumlah_rp_r2" id="jumlah_rp_r2"
                                            placeholder="Rp 0" value="{{ old('jumlah_rp_r2', $data->jumlah_rp_r2 ? number_format($data->jumlah_rp_r2, 0, ',', '.') : '') }}" autocomplete="off">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group boxed">
                                    <div class="input-wrapper">
                                        <label class="label">Unit R2</label>
                                        <input type="number" class="form-control unit-calc" name="jumlah_unit_r2" id="jumlah_unit_r2"
                                            placeholder="0" min="0" value="{{ old('jumlah_unit_r2', $data->jumlah_unit_r2 ?: '') }}">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Baris Roda 4 (R4) -->
                        <div class="row">
                            <div class="col-7">
                                <div class="form-group boxed">
                                    <div class="input-wrapper">
                                        <label class="label">Penerimaan R4</label>
                                        <input type="text" class="form-control rupiah-input" name="jumlah_rp_r4" id="jumlah_rp_r4"
                                            placeholder="Rp 0" value="{{ old('jumlah_rp_r4', $data->jumlah_rp_r4 ? number_format($data->jumlah_rp_r4, 0, ',', '.') : '') }}" autocomplete="off">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="form-group boxed">
                                    <div class="input-wrapper">
                                        <label class="label">Unit R4</label>
                                        <input type="number" class="form-control unit-calc" name="jumlah_unit_r4" id="jumlah_unit_r4"
                                            placeholder="0" min="0" value="{{ old('jumlah_unit_r4', $data->jumlah_unit_r4 ?: '') }}">
                                        <i class="clear-input">
                                            <ion-icon name="close-circle"></ion-icon>
                                        </i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ringkasan Total Realisasi -->
                        <div class="p-3 my-2 bg-light border rounded">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="text-muted small fw-bold text-uppercase">Total Penerimaan</div>
                                    <div style="font-size: 18px; font-weight: 800; color: #10b981;" id="previewTotalRp">
                                        Rp {{ number_format($data->total_rp ?: 0, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-muted small fw-bold text-uppercase">Total Unit</div>
                                    <div style="font-size: 15px; font-weight: 700; color: #1e293b;" id="previewTotalUnit">
                                        {{ $data->total_unit ?: 0 }} Unit
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Notice Dicetak</label>
                                <input type="number" class="form-control" name="jumlah_notice" id="jumlah_notice"
                                    placeholder="Jumlah Notice" min="0" value="{{ old('jumlah_notice', $data->jumlah_notice ?: '') }}">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>

                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Keterangan</label>
                                <textarea class="form-control" name="keterangan" rows="2" placeholder="Keterangan">{{ old('keterangan', $data->keterangan) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="border-radius: 10px; font-weight: 700;">
                        <ion-icon name="save-outline"></ion-icon> Simpan Penerimaan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }

        function hitungTotal() {
            var r2Str = $('#jumlah_rp_r2').val() || '0';
            var r4Str = $('#jumlah_rp_r4').val() || '0';
            var r2 = parseInt(r2Str.replace(/[^0-9]/g, '')) || 0;
            var r4 = parseInt(r4Str.replace(/[^0-9]/g, '')) || 0;
            var total = r2 + r4;
            $('#previewTotalRp').text('Rp ' + formatRupiah(total.toString()));

            var u2 = parseInt($('#jumlah_unit_r2').val()) || 0;
            var u4 = parseInt($('#jumlah_unit_r4').val()) || 0;
            var totalUnit = u2 + u4;
            $('#previewTotalUnit').text(totalUnit + ' Unit');

            // Auto-fill notice jika belum diisi manual
            var currentNotice = $('#jumlah_notice').val();
            if (currentNotice === '' || currentNotice === '0') {
                if (totalUnit > 0) {
                    $('#jumlah_notice').val(totalUnit);
                }
            }
        }

        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const box = document.getElementById('box' + previewId.charAt(0).toUpperCase() + previewId.slice(1));
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    if (box) box.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).on('keyup', '.rupiah-input', function() {
            var val = $(this).val();
            $(this).val(formatRupiah(val));
            hitungTotal();
        });

        $(document).on('input change', '.unit-calc', function() {
            hitungTotal();
        });

        $(document).ready(function() {
            hitungTotal();
        });
    </script>
@endpush
