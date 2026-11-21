@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/penerimaan" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Buka Layanan</div>
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

            <form action="/penerimaan/simpan-buka-layanan" method="POST" enctype="multipart/form-data" id="formBukaLayanan">
                @csrf

                <div class="card mb-3 shadow-none border" style="border-radius: 12px;">
                    <div class="card-body">
                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Tanggal Pelayanan</label>
                                <input type="text" class="form-control" value="{{ formatHariTanggal($tglDefault) }}" readonly style="background-color: #f8fafc; font-weight: 600;">
                                <input type="hidden" name="tgl_penerimaan" value="{{ $tglDefault }}">
                            </div>
                        </div>

                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Lokasi Pelayanan</label>
                                <input type="text" class="form-control" value="{{ $namaLokasi }}" readonly style="background-color: #f8fafc; font-weight: 600;">
                                <input type="hidden" name="kode_cabang" value="{{ $lokasiId }}">
                            </div>
                        </div>

                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Nomor Awal Notice Pajak</label>
                                <input type="text" class="form-control" name="no_awal_notice" id="no_awal_notice"
                                    placeholder="Nomor Seri Awal" value="{{ old('no_awal_notice') }}" required autocomplete="off" style="font-weight: 700; font-size: 15px;">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>

                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <label class="label">Foto Bukti Lembar Awal Notice</label>
                                <input type="file" class="form-control" name="foto_awal_notice" id="foto_awal_notice"
                                    accept="image/*" capture="environment" required onchange="previewImage(this, 'previewAwal')">
                            </div>
                            <div class="mt-2 text-center" id="boxPreviewAwal" style="display: none;">
                                <img id="previewAwal" src="#" alt="Preview Foto Awal" class="img-fluid rounded border shadow-sm" style="max-height: 220px; object-fit: contain;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="border-radius: 10px; font-weight: 700;">
                        <ion-icon name="checkmark-circle-outline"></ion-icon> Simpan & Buka Layanan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('myscript')
    <script>
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

        let isFormSubmitting = false;
        $('#formBukaLayanan').on('submit', function(e) {
            if (isFormSubmitting) {
                return true;
            }
            e.preventDefault();
            const form = this;

            if (!form.checkValidity()) {
                form.reportValidity();
                return false;
            }

            const noNotice = ($('#no_awal_notice').val() || '').trim();

            Swal.fire({
                title: 'Konfirmasi Simpan',
                html: '<div class="mb-2" style="font-size: 15px; color: #334155;">Apakah Anda yakin ingin menyimpan?</div>' +
                      '<div class="p-2 border rounded" style="background: #f8fafc; font-size: 13px; color: #64748b; margin-top: 10px;">' +
                      'Nomor Notice Awal:<br>' +
                      '<strong style="font-size: 20px; font-weight: 800; color: #2A8BF2; letter-spacing: 0.5px;">' + $('<div>').text(noNotice).html() + '</strong>' +
                      '</div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2A8BF2',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    isFormSubmitting = true;
                    form.submit();
                }
            });
        });
    </script>
@endpush
