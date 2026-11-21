<form action="/panel-adm14/petugas-layanan/{{ Crypt::encrypt($petugas->id) }}/update" method="POST" id="frmEditPetugas" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Layanan</label>
            <select name="layanan_id" class="form-select" required>
                @foreach ($listLayanan as $l)
                <option value="{{ $l->id }}" {{ $petugas->layanan_id == $l->id ? 'selected' : '' }}>
                    {{ $l->nama_layanan }}
                </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Nama Petugas</label>
            <input type="text" value="{{ $petugas->nama_petugas }}" class="form-control" name="nama_petugas" placeholder="Nama Petugas" required>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Username</label>
            <input type="text" value="{{ $petugas->username }}" class="form-control" name="username" placeholder="Username" required>
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Password Baru</label>
            <input type="password" class="form-control" name="password" placeholder="Password Baru">
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">NIP</label>
            <input type="text" value="{{ $petugas->nip }}" class="form-control" name="nip" placeholder="NIP">
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Jabatan</label>
            <input type="text" value="{{ $petugas->jabatan }}" class="form-control" name="jabatan" placeholder="Jabatan">
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">No. HP</label>
            <input type="text" value="{{ $petugas->no_hp }}" class="form-control" name="no_hp" placeholder="No. HP">
        </div>
    </div>

    <div class="row">
        <div class="col-12 mb-3">
            <label class="form-label">Foto</label>
            <input type="file" name="foto" class="form-control">
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-12">
            <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
        </div>
    </div>
</form>
