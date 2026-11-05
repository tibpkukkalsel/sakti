<form action="/panel-adm14/jadwallokasi/{{ $jadwal->id }}/update" method="POST" id="frmJadwalEdit">
    @csrf
    <div class="row">
        <div class="col-12">
            <select name="layanan_id" id="layanan_id_edit" class="form-select mb-3">
                <option value="">Pilih Layanan</option>
                @foreach ($listLayanan as $l)
                <option value="{{ $l->id }}" {{ $jadwal->layanan_id == $l->id ? 'selected' : '' }}>
                    {{ $l->nama_layanan }} 
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <select name="hari" id="hari_edit" class="form-select mb-3">
                <option value="">Pilih Hari</option>
                @foreach ($listHari as $h)
                <option value="{{ $h }}" {{ $jadwal->hari == $h ? 'selected' : '' }}>
                    Hari {{ $h }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <select name="kode_cabang" id="kode_cabang_edit" class="form-select mb-3">
                <option value="">Pilih Lokasi Pelayanan</option>
                @foreach ($listLokasi as $lok)
                <option value="{{ $lok->kode_cabang }}" {{ $jadwal->kode_cabang == $lok->kode_cabang ? 'selected' : '' }}>
                    {{ $lok->nama_cabang }} (Radius {{ $lok->radius_cabang }}m)
                </option>
                @endforeach
            </select>
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
                <input type="text" id="keterangan_edit" value="{{ $jadwal->keterangan }}" class="form-control" name="keterangan" placeholder="Keterangan / Posko (Opsional)">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <select name="status" id="status_edit" class="form-select mb-3">
                <option value="1" {{ $jadwal->status == 1 ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ $jadwal->status == 0 ? 'selected' : '' }}>Nonaktif</option>
            </select>
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
    $(function() {
        $("#frmJadwalEdit").submit(function() {
            var layanan_id = $("#frmJadwalEdit").find("#layanan_id_edit").val();
            var hari = $("#frmJadwalEdit").find("#hari_edit").val();
            var kode_cabang = $("#frmJadwalEdit").find("#kode_cabang_edit").val();

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
            } else if (hari == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Hari Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#hari_edit").focus();
                });
                return false;
            } else if (kode_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Lokasi Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#kode_cabang_edit").focus();
                });
                return false;
            }
        });
    });
</script>
