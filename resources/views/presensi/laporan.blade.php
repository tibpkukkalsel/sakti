@extends('layouts.admin.tabler')
@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->

                <h2 class="page-title">
                    Laporan Presensi
                </h2>
            </div>

        </div>
    </div>
</div>
<div class="page-body">
    <div class="container-xl">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="card-title fw-bold text-dark mb-0">
                                <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i>
                                Cetak Laporan Presensi Pegawai
                            </h4>
                            <small class="text-muted">Pilih filter periode bulan & tahun serta nama pegawai untuk mencetak laporan via mPDF.</small>
                        </div>
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-semibold px-2 py-1">
                            mPDF
                        </span>
                    </div>
                    <div class="card-body p-4">
                        <form action="/panel-adm14/presensi/cetaklaporan" id="frmLaporan" target="_blank" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-secondary">
                                        <i class="bi bi-calendar-month me-1"></i>Filter Periode Bulan:
                                    </label>
                                    <select name="bulan" id="bulan" class="form-select">
                                        <option value="">-- Pilih Bulan --</option>
                                        @for ($i=1; $i<=12; $i++)
                                            <option value="{{ $i }}" {{ date("m") == $i ? 'selected' : '' }}>{{ $namabulan[$i] }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-secondary">
                                        <i class="bi bi-calendar3 me-1"></i>Filter Periode Tahun:
                                    </label>
                                    <select name="tahun" id="tahun" class="form-select">
                                        <option value="">-- Pilih Tahun --</option>
                                        @php
                                        $tahunmulai = 2022;
                                        $tahunskrg = date("Y");
                                        @endphp
                                        @for ($tahun=$tahunmulai; $tahun<= $tahunskrg; $tahun++)
                                            <option value="{{ $tahun }}" {{ date("Y") == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-secondary">
                                        <i class="bi bi-person-fill me-1"></i>Pilih Pegawai / Akun Layanan:
                                    </label>
                                    <select name="nik" id="nik" class="form-select">
                                        <option value="">-- Pilih Pegawai --</option>
                                        @foreach ($karyawan as $d)
                                            <option value="{{ $d->nik }}">{{ $d->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row g-2 mt-4 pt-3 border-top">
                                <div class="col-6">
                                    <button type="submit" name="cetak" class="btn btn-danger w-100 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm py-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                                            <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                                        </svg>
                                        <strong>Cetak PDF (mPDF)</strong>
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="submit" name="exportexcel" class="btn btn-success w-100 d-inline-flex align-items-center justify-content-center gap-2 shadow-sm py-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                                            <path d="M7 11l5 5l5 -5"></path>
                                            <path d="M12 4l0 12"></path>
                                        </svg>
                                        Export to Excel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('myscript')
<script>
    $(function() {
        $("#frmLaporan").submit(function(e) {
            var bulan = $("#bulan").val();
            var tahun = $("#tahun").val();
            var nik = $("#nik").val();
            if (bulan == "") {
                Swal.fire({
                    title: 'Warning!'
                    , text: 'Bulan Harus Diplih !'
                    , icon: 'warning'
                    , confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#bulan").focus();
                });
                return false;
            } else if (tahun == "") {
                Swal.fire({
                    title: 'Warning!'
                    , text: 'Tahun Harus Dipilih !'
                    , icon: 'warning'
                    , confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#tahun").focus();
                });
                return false;
            } else if (nik == "") {
                Swal.fire({
                    title: 'Warning!'
                    , text: 'Nik Harus Diisi !'
                    , icon: 'warning'
                    , confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#nik").focus();
                });

                return false;
            }
        });
    });

</script>
@endpush
