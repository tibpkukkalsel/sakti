<form action="/panel-adm14/jadwallokasi/{{ $jadwal->id }}/update" method="POST" id="frmJadwalEdit">
    @csrf
    <input type="hidden" name="jenis_jadwal" value="{{ $jadwal->jenis_jadwal ?? 'rutin' }}">

    <div class="row mb-3">
        <div class="col-12">
            @if (($jadwal->jenis_jadwal ?? 'rutin') == 'khusus')
                <div class="d-flex align-items-center">
                    <span class="badge bg-warning text-dark px-2 py-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-star me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="currentColor" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z"/></svg>
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

    @if (($jadwal->jenis_jadwal ?? 'rutin') == 'khusus')
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label small text-muted">Tanggal Event Khusus</label>
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
    @endif

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
                <input type="text" id="keterangan_edit" value="{{ $jadwal->keterangan }}" class="form-control" name="keterangan" placeholder="Keterangan">
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
