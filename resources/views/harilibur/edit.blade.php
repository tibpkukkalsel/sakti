<form action="/panel-adm14/konfigurasi/harilibur/{{ $harilibur->kode_libur }}/update" method="POST" id="frmeditHarilibur">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                        <path d="M16 3v4" />
                        <path d="M8 3v4" />
                        <path d="M4 11h16" />
                        <path d="M11 15h1" />
                        <path d="M12 15v3" />
                    </svg>
                </span>
                <input type="text" id="tanggal_libur" class="form-control" placeholder="Tanggal Libur"
                    name="tanggal_libur" autocomplete="off" value="{{ $harilibur->tanggal_libur }}">
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-12">
            <div class="form-group">
                <select name="layanan_id" id="layanan_id" class="form-select">
                    <option value="ALL" {{ $harilibur->kode_cabang == 'ALL' ? 'selected' : '' }}>Semua Layanan</option>
                    @foreach ($layanan as $l)
                        <option value="{{ $l->id }}" {{ $harilibur->kode_cabang == $l->id ? 'selected' : '' }}>
                            {{ $l->nama_layanan }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-description"
                        width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        <path d="M9 17h6" />
                        <path d="M9 13h6" />
                    </svg>
                </span>
                <input type="text" id="keterangan" autocomplete="off" class="form-control" placeholder="Keterangan"
                    name="keterangan" value="{{ $harilibur->keterangan }}">
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-send" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M10 14l11 -11"></path>
                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5">
                        </path>
                    </svg>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {
        $("#tanggal_libur").datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd'
        });

        $("#frmeditHarilibur").submit(function(e) {
            var tanggal_libur = $("#tanggal_libur").val();
            var layanan_id = $("#layanan_id").val();
            var keterangan = $("#keterangan").val();

            if (tanggal_libur == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Tanggal Libur Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#tanggal_libur").focus();
                });
                return false;
            } else if (layanan_id == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Layanan Harus Dipilih !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#layanan_id").focus();
                });
                return false;
            } else if (keterangan == "") {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Keterangan Harus Diisi !',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                }).then((result) => {
                    $("#keterangan").focus();
                });
                return false;
            }
        });
    });
</script>
