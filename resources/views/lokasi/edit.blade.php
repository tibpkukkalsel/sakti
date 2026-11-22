<form action="/panel-adm14/lokasi/update" method="POST" id="frmLokasiEdit">
    @csrf
    <input type="hidden" value="{{ $cabang->id }}" id="id" name="id">
    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Nama Lokasi</label>
            <input type="text" id="nama_cabang" value="{{ $cabang->nama_cabang }}" class="form-control" name="nama_cabang" placeholder="Nama Lokasi">
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Titik Koordinat</label>
            <input type="text" id="lokasi_cabang" value="{{ $cabang->lokasi_cabang }}" class="form-control" name="lokasi_cabang" placeholder="Titik Koordinat">
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Radius Presensi</label>
            <input type="text" id="radius_cabang" value="{{ $cabang->radius_cabang }}" class="form-control" name="radius_cabang" placeholder="Radius Presensi">
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        </div>
    </div>
</form>
<script>
    $(function() {
        $("#frmLokasiEdit").submit(function() {
            var nama_cabang = $("#frmLokasiEdit").find("#nama_cabang").val();
            var lokasi_cabang = $("#frmLokasiEdit").find("#lokasi_cabang").val();
            var radius_cabang = $("#frmLokasiEdit").find("#radius_cabang").val();

            if (nama_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Nama Lokasi Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#frmLokasiEdit").find("#nama_cabang").focus();
                });
                return false;
            } else if (lokasi_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Titik Lokasi Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#frmLokasiEdit").find("#lokasi_cabang").focus();
                });
                return false;
            } else if (radius_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Radius Lokasi Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#frmLokasiEdit").find("#radius_cabang").focus();
                });
                return false;
            }
        });
    });
</script>
