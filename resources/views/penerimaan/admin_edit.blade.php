<form action="/panel-adm14/penerimaan/{{ $data->id }}/update" method="POST" id="frmPenerimaanEdit">
    @csrf
    <div class="row">
        <div class="col-12">
            <select name="layanan_id" id="layanan_id_edit" class="form-select mb-3">
                <option value="">Pilih Layanan</option>
                @foreach ($daftarLayanan as $l)
                <option value="{{ $l->id }}" {{ $data->layanan_id == $l->id ? 'selected' : '' }}>
                    {{ $l->nama_layanan }} (PIC: {{ $l->nama_pic ?: '-' }})
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path>
                        <path d="M16 3v4"></path>
                        <path d="M8 3v4"></path>
                        <path d="M4 11h16"></path>
                    </svg>
                </span>
                <input type="date" id="tgl_penerimaan_edit" class="form-control" name="tgl_penerimaan" value="{{ $data->tgl_penerimaan ? date('Y-m-d', strtotime($data->tgl_penerimaan)) : '' }}" placeholder="Tanggal Penerimaan">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-motorbike" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                        <path d="M19 16m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
                        <path d="M7.5 14h5l4 -4H7.5m4 0h4l2 -3h-3"></path>
                    </svg>
                </span>
                <input type="text" id="edit_rp_r2" class="form-control rupiah-edit" name="jumlah_rp_r2" value="{{ number_format($data->jumlah_rp_r2, 0, ',', '.') }}" placeholder="Penerimaan R2 (Rp)">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-car" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                        <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                        <path d="M5 17h-2v-6l2 -5h9l4 5h1a2 2 0 0 1 2 2v4h-2m-4 0h-6"></path>
                        <path d="M7 9l1 3h8l1 -3"></path>
                    </svg>
                </span>
                <input type="text" id="edit_rp_r4" class="form-control rupiah-edit" name="jumlah_rp_r4" value="{{ number_format($data->jumlah_rp_r4, 0, ',', '.') }}" placeholder="Penerimaan R4 (Rp)">
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <div class="p-2 bg-light border rounded text-center">
                <small class="text-muted">Total Estimasi Penerimaan</small>
                <div class="font-weight-bold text-success fs-3" id="edit_preview_total_rp">Rp {{ number_format($data->total_rp, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-hash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 9l14 0"></path>
                        <path d="M5 15l14 0"></path>
                        <path d="M11 4l-4 16"></path>
                        <path d="M17 4l-4 16"></path>
                    </svg>
                </span>
                <input type="number" id="jumlah_unit_r2_edit" class="form-control" name="jumlah_unit_r2" value="{{ $data->jumlah_unit_r2 }}" placeholder="Unit R2" min="0">
            </div>
        </div>
        <div class="col-6">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-hash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 9l14 0"></path>
                        <path d="M5 15l14 0"></path>
                        <path d="M11 4l-4 16"></path>
                        <path d="M17 4l-4 16"></path>
                    </svg>
                </span>
                <input type="number" id="jumlah_unit_r4_edit" class="form-control" name="jumlah_unit_r4" value="{{ $data->jumlah_unit_r4 }}" placeholder="Unit R4" min="0">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2m4 -14h6m-6 4h6m-2 4h2"></path>
                    </svg>
                </span>
                <input type="number" id="jumlah_notice_edit" class="form-control" name="jumlah_notice" value="{{ $data->jumlah_notice }}" placeholder="Jumlah Notice Dicetak" min="0">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-notes" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z"></path>
                        <path d="M9 7l6 0"></path>
                        <path d="M9 11l6 0"></path>
                        <path d="M9 15l4 0"></path>
                    </svg>
                </span>
                <input type="text" id="keterangan_edit" class="form-control" name="keterangan" value="{{ $data->keterangan }}" placeholder="Keterangan (Opsional)">
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10 14l11 -11"></path>
                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5"></path>
                    </svg>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>
<script>
    function hitungTotalEdit() {
        var r2 = $('#edit_rp_r2').val().replace(/[^0-9]/g, '') || 0;
        var r4 = $('#edit_rp_r4').val().replace(/[^0-9]/g, '') || 0;
        var total = parseInt(r2) + parseInt(r4);
        $('#edit_preview_total_rp').text('Rp ' + formatRupiah(total.toString()));
    }

    $(function() {
        $('.rupiah-edit').on('keyup', function() {
            $(this).val(formatRupiah($(this).val()));
            hitungTotalEdit();
        });

        $("#frmPenerimaanEdit").submit(function() {
            var layanan_id = $("#frmPenerimaanEdit").find("#layanan_id_edit").val();
            var tgl_penerimaan = $("#frmPenerimaanEdit").find("#tgl_penerimaan_edit").val();
            var edit_rp_r2 = $("#frmPenerimaanEdit").find("#edit_rp_r2").val();
            var edit_rp_r4 = $("#frmPenerimaanEdit").find("#edit_rp_r4").val();

            if (layanan_id == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Layanan Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#layanan_id_edit").focus();
                });
                return false;
            } else if (tgl_penerimaan == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Tanggal Penerimaan Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#tgl_penerimaan_edit").focus();
                });
                return false;
            } else if (edit_rp_r2 == "" && edit_rp_r4 == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Penerimaan R2 atau R4 Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#edit_rp_r2").focus();
                });
                return false;
            }
        });
    });
</script>
