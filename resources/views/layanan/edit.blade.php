<form action="/panel-adm14/layanan/{{ Crypt::encrypt($layanan->id) }}/update" method="POST" id="formEditLayanan" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="old_foto" value="{{ $layanan->foto }}">

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label required">Nama Layanan</label>
            <input type="text" name="nama_layanan" class="form-control" value="{{ $layanan->nama_layanan }}" placeholder="Contoh: SAMKEL 1" required>
        </div>
        <div class="col-md-6">
            <label class="form-label required">Username Login</label>
            <input type="text" name="username" class="form-control" value="{{ $layanan->username }}" placeholder="Contoh: samkel1" required>
            <small class="text-muted">Username untuk masuk ke aplikasi.</small>
        </div>

        <div class="col-md-6">
            <label class="form-label">Ganti Password</label>
            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
        </div>
        <div class="col-md-6">
            <label class="form-label">Lokasi Kantor / Layanan</label>
            <select name="kode_cabang" class="form-select">
                <option value="">-- Pilih Lokasi --</option>
                @foreach ($cabang as $c)
                    <option value="{{ $c->kode_cabang }}" {{ $layanan->kode_cabang == $c->kode_cabang ? 'selected' : '' }}>
                        {{ $c->nama_cabang }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-12"><hr class="my-1"></div>

        <h6 class="fw-bold text-secondary mb-0">Informasi Penanggung Jawab (PIC)</h6>

        <div class="col-md-6">
            <label class="form-label required">Nama PIC</label>
            <input type="text" name="nama_pic" class="form-control" value="{{ $layanan->nama_pic }}" placeholder="Nama PIC" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">NIP PIC</label>
            <input type="text" name="nip_pic" class="form-control" value="{{ $layanan->nip_pic }}" placeholder="NIP (Opsional)">
        </div>

        <div class="col-md-6">
            <label class="form-label">Jabatan PIC</label>
            <input type="text" name="jabatan_pic" class="form-control" value="{{ $layanan->jabatan_pic }}" placeholder="Contoh: Petugas Layanan">
        </div>
        <div class="col-md-6">
            <label class="form-label">No. HP / WhatsApp PIC</label>
            <input type="text" name="no_hp" class="form-control" value="{{ $layanan->no_hp }}" placeholder="08xxxxxxxxxx">
        </div>

        <div class="col-12">
            <label class="form-label">Foto / Logo Layanan</label>
            <div class="d-flex align-items-center gap-3">
                @if ($layanan->foto)
                    <img src="{{ Storage::url('uploads/karyawan/' . $layanan->foto) }}" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">
                @endif
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
        </div>
    </div>

    <div class="modal-footer px-0 pb-0 pt-3 mt-3 border-top">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
    </div>
</form>

