@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/penerimaan" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Edit Penerimaan PKB</div>
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
            padding-top: 60px !important;
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
        }

        .form-control-custom:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
            outline: none;
        }

        .total-box {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            color: #ffffff;
            border-radius: 16px;
            padding: 16px;
            margin: 16px 0;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.2);
        }

        .total-label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
        }

        .total-amount {
            font-size: 24px;
            font-weight: 800;
            margin-top: 4px;
        }
    </style>

    <div class="section pt-2">
        <form action="/penerimaan/{{ $data->id }}/update" method="POST" id="formPenerimaan">
            @csrf
            <div class="form-card">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div class="rounded-circle bg-emerald-lt p-2 text-success">
                        <ion-icon name="business-outline" style="font-size: 22px;"></ion-icon>
                    </div>
                    <div>
                        <div class="fw-bold text-dark fs-6">{{ $user->nama_layanan }}</div>
                        <small class="text-muted">Edit Penerimaan: {{ \Carbon\Carbon::parse($data->tgl_penerimaan)->translatedFormat('d F Y') }}</small>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" style="border-radius: 12px;">
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="input-group-custom">
                    <label>Tanggal Penerimaan</label>
                    <input type="date" name="tgl_penerimaan" id="tgl_penerimaan" class="form-control-custom"
                        value="{{ old('tgl_penerimaan', $data->tgl_penerimaan->format('Y-m-d')) }}" required>
                </div>

                <div class="row g-2">
                    <div class="col-12">
                        <div class="input-group-custom">
                            <label>Jumlah Penerimaan Roda 2 / R2 (Rp)</label>
                            <input type="text" name="jumlah_rp_r2" id="jumlah_rp_r2" class="form-control-custom rupiah-input"
                                placeholder="0" value="{{ old('jumlah_rp_r2', number_format($data->jumlah_rp_r2, 0, ',', '.')) }}" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group-custom">
                            <label>Jumlah Penerimaan Roda 4 / R4 (Rp)</label>
                            <input type="text" name="jumlah_rp_r4" id="jumlah_rp_r4" class="form-control-custom rupiah-input"
                                placeholder="0" value="{{ old('jumlah_rp_r4', number_format($data->jumlah_rp_r4, 0, ',', '.')) }}" required autocomplete="off">
                        </div>
                    </div>
                </div>

                <!-- Box Total Otomatis -->
                <div class="total-box">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="total-label">Total Penerimaan (R2 + R4)</div>
                            <div class="total-amount" id="previewTotalRp">Rp {{ number_format($data->total_rp, 0, ',', '.') }}</div>
                        </div>
                        <div class="text-end">
                            <div class="total-label">Total Unit</div>
                            <div style="font-size: 20px; font-weight: 700;" id="previewTotalUnit">{{ $data->total_unit }} Unit</div>
                        </div>
                    </div>
                </div>

                <div class="row g-2">
                    <div class="col-6">
                        <div class="input-group-custom">
                            <label>Jumlah Unit R2</label>
                            <input type="number" name="jumlah_unit_r2" id="jumlah_unit_r2" class="form-control-custom unit-calc"
                                placeholder="0" min="0" value="{{ old('jumlah_unit_r2', $data->jumlah_unit_r2) }}" required>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group-custom">
                            <label>Jumlah Unit R4</label>
                            <input type="number" name="jumlah_unit_r4" id="jumlah_unit_r4" class="form-control-custom unit-calc"
                                placeholder="0" min="0" value="{{ old('jumlah_unit_r4', $data->jumlah_unit_r4) }}" required>
                        </div>
                    </div>
                </div>

                <div class="input-group-custom">
                    <label>Jumlah Notice Pajak yang Dicetak (Lembar)</label>
                    <input type="number" name="jumlah_notice" id="jumlah_notice" class="form-control-custom"
                        placeholder="0" min="0" value="{{ old('jumlah_notice', $data->jumlah_notice) }}" required>
                </div>

                <div class="input-group-custom">
                    <label>Keterangan / Catatan (Opsional)</label>
                    <textarea name="keterangan" id="keterangan" class="form-control-custom" style="height: 75px;"
                        placeholder="Catatan kendala / informasi tambahan jika ada...">{{ old('keterangan', $data->keterangan) }}</textarea>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100 fw-bold" style="height: 50px; border-radius: 14px; background: #059669; border-color: #059669; font-size: 16px;">
                        <ion-icon name="save-outline" class="me-1"></ion-icon> Simpan Perubahan
                    </button>
                    <a href="/penerimaan" class="btn btn-light w-100 mt-2 text-muted" style="border-radius: 14px; height: 44px; display: flex; align-items: center; justify-content: center;">
                        Batal
                    </a>
                </div>
            </div>
        </form>
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

        hitungTotal();
    });
</script>
@endpush
