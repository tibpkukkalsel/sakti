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

            @if (Session::get('error'))
                <div class="alert alert-danger mb-2">
                    {{ Session::get('error') }}
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

            <form action="/penerimaan/store" method="POST" id="formPenerimaan">
                @csrf

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label">Tanggal Penerimaan</label>
                        <input type="date" class="form-control" name="tgl_penerimaan" id="tgl_penerimaan"
                            value="{{ old('tgl_penerimaan', $tglDefault) }}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

                <div class="row">
                    <div class="col-7">
                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Penerimaan R2 (Rp)</label>
                                <input type="text" class="form-control rupiah-input" name="jumlah_rp_r2" id="jumlah_rp_r2"
                                    placeholder="Rp 0" value="{{ old('jumlah_rp_r2') }}" autocomplete="off">
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
                                    placeholder="0" min="0" value="{{ old('jumlah_unit_r2') }}">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-7">
                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Penerimaan R4 (Rp)</label>
                                <input type="text" class="form-control rupiah-input" name="jumlah_rp_r4" id="jumlah_rp_r4"
                                    placeholder="Rp 0" value="{{ old('jumlah_rp_r4') }}" autocomplete="off">
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
                                    placeholder="0" min="0" value="{{ old('jumlah_unit_r4') }}">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Total Realisasi -->
                <div class="card my-2" style="background: #ffffff; border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Total Penerimaan</div>
                                <div style="font-size: 18px; font-weight: 800; color: #1e293b;" id="previewTotalRp">Rp 0</div>
                            </div>
                            <div class="text-right">
                                <div class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Total Unit</div>
                                <div style="font-size: 15px; font-weight: 700; color: #1e293b;" id="previewTotalUnit">0 Unit</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label">Notice Pajak Dicetak (Lembar)</label>
                        <input type="number" class="form-control" name="jumlah_notice" id="jumlah_notice"
                            placeholder="Jumlah Lembar Notice" min="0" value="{{ old('jumlah_notice') }}">
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label">Keterangan / Catatan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control"
                            placeholder="Keterangan opsional">{{ old('keterangan') }}</textarea>
                    </div>
                </div>

                <div class="form-group boxed mt-3">
                    <div class="input-wrapper">
                        <button type="submit" class="btn btn-primary btn-block">
                            <ion-icon name="save-outline"></ion-icon>
                            Simpan Penerimaan
                        </button>
                    </div>
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

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    function hitungTotal() {
        var valR2 = $('#jumlah_rp_r2').val().replace(/[^0-9]/g, '') || 0;
        var valR4 = $('#jumlah_rp_r4').val().replace(/[^0-9]/g, '') || 0;
        var totalRp = parseInt(valR2) + parseInt(valR4);

        $('#previewTotalRp').text('Rp ' + formatRupiah(totalRp.toString()));

        var unitR2 = parseInt($('#jumlah_unit_r2').val()) || 0;
        var unitR4 = parseInt($('#jumlah_unit_r4').val()) || 0;
        var totalUnit = unitR2 + unitR4;

        $('#previewTotalUnit').text(totalUnit + ' Unit');
    }

    $(document).ready(function() {
        $('.rupiah-input').on('keyup', function(e) {
            $(this).val(formatRupiah($(this).val()));
            hitungTotal();
        });

        $('.unit-calc').on('input change', function() {
            hitungTotal();
        });

        // Hitung saat pertama kali load
        hitungTotal();
    });
</script>
@endpush
