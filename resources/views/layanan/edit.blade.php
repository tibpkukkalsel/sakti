<form action="/panel-adm14/layanan/{{ $layanan->id }}/update" method="POST" id="frmEditLayanan" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-analytics" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M3 4m0 1a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1z"></path>
                        <path d="M7 20l10 0"></path>
                        <path d="M9 16l0 4"></path>
                        <path d="M15 16l0 4"></path>
                        <path d="M8 12l3 -3l2 2l3 -3"></path>
                    </svg>
                </span>
                <input type="text" id="nama_layanan" value="{{ $layanan->nama_layanan }}" class="form-control" name="nama_layanan" placeholder="Nama Layanan">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M12 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path>
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                    </svg>
                </span>
                <input type="text" id="username" value="{{ $layanan->username }}" class="form-control" name="username" placeholder="Username">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="input-icon mb-3">
                <span class="input-icon-addon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-key" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M16.555 3.843l3.602 3.602a2.877 2.877 0 0 1 0 4.069l-2.643 2.643a2.877 2.877 0 0 1 -4.069 0l-.301 -.301l-6.558 6.558a2 2 0 0 1 -1.239 .578l-1.725 .144a1 1 0 0 1 -1.082 -1.082l.144 -1.725a2 2 0 0 1 .578 -1.239l6.558 -6.558l-.301 -.301a2.877 2.877 0 0 1 0 -4.069l2.643 -2.643a2.877 2.877 0 0 1 4.069 0z"></path>
                        <path d="M15 9h.01"></path>
                    </svg>
                </span>
                <input type="password" id="password" class="form-control" name="password" placeholder="Password">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Mode Presensi</label>
            <select name="mode_presensi" class="form-select">
                <option value="bebas" {{ ($layanan->mode_presensi ?? 'bebas') == 'bebas' ? 'selected' : '' }}>Bebas</option>
                <option value="jadwal" {{ ($layanan->mode_presensi ?? '') == 'jadwal' ? 'selected' : '' }}>Jadwal Mingguan</option>
            </select>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <input type="file" name="foto" class="form-control">
            <input type="hidden" name="old_foto" value="{{ $layanan->foto }}">
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
    $("#frmEditLayanan").submit(function() {
        var form = $(this);
        var nama_layanan = form.find("input[name='nama_layanan']").val();
        var username = form.find("input[name='username']").val();

        if (nama_layanan == "") {
            Swal.fire({
                title: 'Warning!',
                text: 'Nama Layanan Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                form.find("input[name='nama_layanan']").focus();
            });
            return false;
        } else if (username == "") {
            Swal.fire({
                title: 'Warning!',
                text: 'Username Harus Diisi !',
                icon: 'warning',
                confirmButtonText: 'Ok'
            }).then((result) => {
                form.find("input[name='username']").focus();
            });
            return false;
        }
    });
</script>
