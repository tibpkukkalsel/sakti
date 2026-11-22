<form action="/panel-adm14/konfigurasi/updatejamkerja" method="POST" id="frmJK_edit">
    @csrf
    <input type="hidden" id="kode_jam_kerja_edit" name="kode_jam_kerja" value="{{ $jamkerja->kode_jam_kerja }}">
    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Nama Jam Kerja</label>
            <input type="text" value="{{ $jamkerja->nama_jam_kerja }}" id="nama_jam_kerja_edit"
                class="form-control" placeholder="Nama Jam Kerja" name="nama_jam_kerja">
        </div>
    </div>
    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">Awal Jam Masuk</label>
            <input type="text" value="{{ $jamkerja->awal_jam_masuk }}" id="awal_jam_masuk_edit"
                class="form-control" placeholder="00:00" name="awal_jam_masuk">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">Jam Masuk</label>
            <input type="text" value="{{ $jamkerja->jam_masuk }}" id="jam_masuk_edit" class="form-control"
                placeholder="00:00" name="jam_masuk">
        </div>
    </div>
    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">Akhir Jam Masuk</label>
            <input type="text" value="{{ $jamkerja->akhir_jam_masuk }}" id="akhir_jam_masuk_edit"
                class="form-control" placeholder="00:00" name="akhir_jam_masuk">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">Jam Pulang</label>
            <input type="text" value="{{ $jamkerja->jam_pulang }}" id="jam_pulang_edit" class="form-control"
                placeholder="00:00" name="jam_pulang">
        </div>
    </div>
    <div class="row">
        <div class="col-6 mb-3">
            <label class="form-label">Total Jam</label>
            <input type="text" id="total_jam_edit" class="form-control" placeholder="Total Jam"
                name="total_jam" value="{{ $jamkerja->total_jam }}">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">Lintas Hari</label>
            <select name="lintashari" id="lintashari_edit" class="form-select">
                <option value="">Pilih</option>
                <option value="1" {{ $jamkerja->lintashari == 1 ? 'selected' : '' }}>Ya</option>
                <option value="0" {{ $jamkerja->lintashari == 0 ? 'selected' : '' }}>Tidak</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Istirahat</label>
            <select name="status_istirahat" id="status_istirahat_edit" class="form-select">
                <option value="">Pilih</option>
                <option value="1" {{ $jamkerja->status_istirahat == 1 ? 'selected' : '' }}>Ada</option>
                <option value="0" {{ $jamkerja->status_istirahat == 0 ? 'selected' : '' }}>Tidak</option>
            </select>
        </div>
    </div>
    <div class="row setjamistirahat">
        <div class="col-6 mb-3">
            <label class="form-label">Awal Jam Istirahat</label>
            <input type="text" value="{{ $jamkerja->awal_jam_istirahat }}" id="awal_jam_istirahat_edit" class="form-control"
                placeholder="00:00" name="awal_jam_istirahat">
        </div>
        <div class="col-6 mb-3">
            <label class="form-label">Akhir Jam Istirahat</label>
            <input type="text" value="{{ $jamkerja->akhir_jam_istirahat }}" id="akhir_jam_istirahat_edit" class="form-control"
                placeholder="00:00" name="akhir_jam_istirahat">
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        </div>
    </div>
</form>


<script>
    function showsetjamistirahat() {
        var status_istirahat = $("#status_istirahat_edit").val();
        if (status_istirahat == "1") {
            $(".setjamistirahat").show();
        } else {
            $(".setjamistirahat").hide();
        }
    }

    $("#status_istirahat_edit").change(function() {
        showsetjamistirahat();
    });
    showsetjamistirahat();
    $("#awal_jam_masuk_edit, #jam_masuk_edit, #akhir_jam_masuk_edit, #jam_pulang_edit,#awal_jam_istirahat_edit,#akhir_jam_istirahat_edit")
        .mask("00:00");
    $("#frmJK_edit").submit(function() {
        var nama_jam_kerja = $("#nama_jam_kerja_edit").val();
        var awal_jam_masuk = $("#awal_jam_masuk_edit").val();
        var jam_masuk = $("#jam_masuk_edit").val();
        var akhir_jam_masuk = $("#akhir_jam_masuk_edit").val();
        var jam_pulang = $("#jam_pulang_edit").val();
        var total_jam = $("#total_jam_edit").val();
        var lintashari = $("#lintashari_edit").val();
        var awal_jam_istirahat = $("#awal_jam_istirahat_edit").val();
        var akhir_jam_istirahat = $("#akhir_jam_istirahat_edit").val();
        var status_istirahat = $("#status_istirahat_edit").val();
        if (nama_jam_kerja == "") {
            // alert('Nik Harus Diisi');
            Swal.fire({
                title: 'Warning!',
                text: 'Nama Jam Kerja Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                $("#nama_jam_kerja").focus();
            });

            return false;
        } else if (awal_jam_masuk == "") {
            // alert('Nik Harus Diisi');
            Swal.fire({
                title: 'Warning!',
                text: 'Awal Jam Masuk Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                $("#awal_jam_masuk").focus();
            });

            return false;
        } else if (jam_masuk == "") {
            // alert('Nik Harus Diisi');
            Swal.fire({
                title: 'Warning!',
                text: 'Jam Masuk Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                $("#jam_masuk").focus();
            });

            return false;
        } else if (akhir_jam_masuk == "") {
            // alert('Nik Harus Diisi');
            Swal.fire({
                title: 'Warning!',
                text: 'Akhir Masuk Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                $("#akhir_jam_masuk").focus();
            });

            return false;
        } else if (status_istirahat === "") {

            Swal.fire({
                title: 'Warning!',
                text: 'Status Istirahat Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                $("#status_istirahat_edit").focus();
            });

            return false;
        } else if (awal_jam_istirahat == "" && status_istirahat == "1") {

            Swal.fire({
                title: 'Warning!',
                text: 'Jam Awal Istirahat Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                $("#awal_jam_istirahat").focus();
            });

            return false;
        } else if (akhir_jam_istirahat == "" && status_istirahat == "1") {
            // alert('Nik Harus Diisi');
            Swal.fire({
                title: 'Warning!',
                text: 'Jam Akhir Istirahat Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                $("#akhir_jam_istirahat").focus();
            });

            return false;
        } else if (jam_pulang == "") {
            // alert('Nik Harus Diisi');
            Swal.fire({
                title: 'Warning!',
                text: 'Jam Pulang Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                $("#jam_pulang").focus();
            });

            return false;
        } else if (total_jam == "") {
            // alert('Nik Harus Diisi');
            Swal.fire({
                title: 'Warning!',
                text: 'Total Jam Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                $("#total_jam").focus();
            });

            return false;
        } else if (lintashari == "") {
            // alert('Nik Harus Diisi');
            Swal.fire({
                title: 'Warning!',
                text: 'Lintas Hari Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                $("#lintashari_edit").focus();
            });

            return false;
        }
    });
</script>
