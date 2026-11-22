<form action="/panel-adm14/jadwallokasi/{{ $jadwal->id }}/update" method="POST" id="frmJadwalEdit">
    @csrf
    <input type="hidden" name="jenis_jadwal" value="{{ $jadwal->jenis_jadwal ?? 'rutin' }}">

    <div class="row mb-3">
        <div class="col-12">
            @if (($jadwal->jenis_jadwal ?? 'rutin') == 'khusus')
                <div class="d-flex align-items-center">
                    <span class="badge bg-warning text-dark px-2 py-1">
                        Jadwal Event Khusus
                    </span>
                </div>
            @else
                <div class="d-flex align-items-center">
                    <span class="badge bg-primary text-white px-2 py-1">
                        Jadwal Rutin Mingguan
                    </span>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Layanan</label>
            <select name="layanan_id" id="layanan_id_edit" class="form-select">
                <option value="">Pilih Layanan</option>
                @foreach ($listLayanan as $l)
                <option value="{{ $l->id }}" {{ $jadwal->layanan_id == $l->id ? 'selected' : '' }}>
                    {{ $l->nama_layanan }} 
                </option>
                @endforeach
            </select>
        </div>
    </div>

    @if (($jadwal->jenis_jadwal ?? 'rutin') == 'khusus')
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label">Tanggal Event</label>
                <input type="date" name="tanggal" id="tanggal_edit" value="{{ $jadwal->tanggal }}" class="form-control mb-1">
                <div id="info_hari_tanggal_edit" class="small text-muted">
                    @if (!empty($jadwal->tanggal))
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mt-1">Hari {{ $jadwal->hari }}</span>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label">Hari</label>
                <select name="hari" id="hari_edit" class="form-select">
                    <option value="">Pilih Hari</option>
                    @foreach ($listHari as $h)
                    <option value="{{ $h }}" {{ $jadwal->hari == $h ? 'selected' : '' }}>
                        Hari {{ $h }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Lokasi</label>
            <select name="kode_cabang" id="kode_cabang_edit" class="form-select">
                <option value="">Pilih Lokasi</option>
                @foreach ($listLokasi as $lok)
                <option value="{{ $lok->id }}" {{ $jadwal->kode_cabang == $lok->id ? 'selected' : '' }}>
                    {{ $lok->nama_cabang }} (Radius {{ $lok->radius_cabang }}m)
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Jam Kerja</label>
            <select name="kode_jam_kerja" id="kode_jam_kerja_edit" class="form-select">
                <option value="">Pilih Jam Kerja</option>
                @foreach ($listJamKerja as $jk)
                <option value="{{ $jk->kode_jam_kerja }}" {{ ($jadwal->kode_jam_kerja ?? '') == $jk->kode_jam_kerja ? 'selected' : '' }}>
                    {{ $jk->nama_jam_kerja }} ({{ substr($jk->jam_masuk, 0, 5) }} - {{ substr($jk->jam_pulang, 0, 5) }})
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Keterangan</label>
            <input type="text" id="keterangan_edit" value="{{ $jadwal->keterangan }}" class="form-control" name="keterangan" placeholder="Keterangan">
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Status</label>
            <select name="status" id="status_edit" class="form-select">
                <option value="1" {{ $jadwal->status == 1 ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ $jadwal->status == 0 ? 'selected' : '' }}>Nonaktif</option>
            </select>
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
        const namaHariIndoEdit = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $('#tanggal_edit').on('input change', function() {
            var val = $(this).val();
            if (val) {
                var d = new Date(val + 'T00:00:00');
                var dayName = namaHariIndoEdit[d.getDay()];
                $('#info_hari_tanggal_edit').html('<span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle mt-1">Hari ' + dayName + '</span>');
            } else {
                $('#info_hari_tanggal_edit').html('');
            }
        });

        $("#frmJadwalEdit").submit(function() {
            var form = $(this);
            var layanan_id = form.find("#layanan_id_edit").val();
            var jenis_jadwal = form.find("input[name='jenis_jadwal']").val();
            var hari = form.find("#hari_edit").val();
            var tanggal = form.find("#tanggal_edit").val();
            var kode_cabang = form.find("#kode_cabang_edit").val();

            if (layanan_id == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Layanan Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    form.find("#layanan_id_edit").focus();
                });
                return false;
            } else if (jenis_jadwal == "rutin" && (!hari || hari == "")) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Hari Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    form.find("#hari_edit").focus();
                });
                return false;
            } else if (jenis_jadwal == "khusus" && (!tanggal || tanggal == "")) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Tanggal Event Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    form.find("#tanggal_edit").focus();
                });
                return false;
            } else if (kode_cabang == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Lokasi Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    form.find("#kode_cabang_edit").focus();
                });
                return false;
            }
        });
    });
</script>
