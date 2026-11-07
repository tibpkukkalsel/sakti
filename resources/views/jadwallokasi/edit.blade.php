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
    <div class="row mb-3">
        <div class="col-12">
            <label class="form-label fw-bold">Tipe Jadwal</label>
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="jenis_jadwal" id="tipe_rutin_edit" value="rutin" {{ ($jadwal->jenis_jadwal ?? 'rutin') == 'rutin' ? 'checked' : '' }} autocomplete="off">
                <label class="btn btn-outline-primary" for="tipe_rutin_edit">Rutin Mingguan</label>

                <input type="radio" class="btn-check" name="jenis_jadwal" id="tipe_khusus_edit" value="khusus" {{ ($jadwal->jenis_jadwal ?? '') == 'khusus' ? 'checked' : '' }} autocomplete="off">
                <label class="btn btn-outline-warning" for="tipe_khusus_edit">Event Khusus (Custom Tanggal)</label>
            </div>
        </div>
    </div>
    <div class="row {{ ($jadwal->jenis_jadwal ?? 'rutin') == 'khusus' ? 'd-none' : '' }}" id="group_hari_edit">
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
    <div class="row {{ ($jadwal->jenis_jadwal ?? 'rutin') == 'khusus' ? '' : 'd-none' }}" id="group_tanggal_edit">
        <div class="col-12">
            <label class="form-label small text-muted">Tanggal Event Khusus</label>
            <input type="date" name="tanggal" id="tanggal_edit" value="{{ $jadwal->tanggal }}" class="form-control mb-1">
            <div id="info_hari_tanggal_edit" class="small text-muted mb-3">
                @if (!empty($jadwal->tanggal))
                    <span class="badge bg-light text-dark border">Hari {{ $jadwal->hari }}</span>
                @endif
            </div>
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
        $('#frmJadwalEdit input[name="jenis_jadwal"]').change(function() {
            if ($(this).val() == 'khusus') {
                $('#group_hari_edit').addClass('d-none');
                $('#group_tanggal_edit').removeClass('d-none');
            } else {
                $('#group_tanggal_edit').addClass('d-none');
                $('#group_hari_edit').removeClass('d-none');
            }
        });

        const namaHariIndoEdit = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $('#tanggal_edit').on('input change', function() {
            var val = $(this).val();
            if (val) {
                var d = new Date(val + 'T00:00:00');
                var dayName = namaHariIndoEdit[d.getDay()];
                $('#info_hari_tanggal_edit').html('<span class="badge bg-light text-dark border">Hari ' + dayName + '</span>');
            } else {
                $('#info_hari_tanggal_edit').html('');
            }
        });

        $("#frmJadwalEdit").submit(function() {
            var form = $(this);
            var layanan_id = form.find("#layanan_id_edit").val();
            var jenis_jadwal = form.find("input[name='jenis_jadwal']:checked").val();
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
            } else if (jenis_jadwal == "rutin" && hari == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Hari Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    form.find("#hari_edit").focus();
                });
                return false;
            } else if (jenis_jadwal == "khusus" && tanggal == "") {
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
