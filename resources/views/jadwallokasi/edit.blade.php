<form action="/panel-adm14/jadwallokasi/{{ $jadwal->id }}/update" method="POST">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2 text-warning"></i>Edit Jadwal Lokasi Layanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label class="form-label fw-bold">Akun Layanan <span class="text-danger">*</span></label>
            <select name="layanan_id" class="form-select" required>
                @foreach ($listLayanan as $l)
                    <option value="{{ $l->id }}" {{ $jadwal->layanan_id == $l->id ? 'selected' : '' }}>
                        {{ $l->nama_layanan }} (PIC: {{ $l->nama_pic ?: '-' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Hari Pelayanan <span class="text-danger">*</span></label>
            <select name="hari" class="form-select" required>
                @foreach ($listHari as $h)
                    <option value="{{ $h }}" {{ $jadwal->hari == $h ? 'selected' : '' }}>
                        Hari {{ $h }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Lokasi Pelayanan <span class="text-danger">*</span></label>
            <select name="kode_cabang" class="form-select" required>
                @foreach ($listLokasi as $lok)
                    <option value="{{ $lok->kode_cabang }}" {{ $jadwal->kode_cabang == $lok->kode_cabang ? 'selected' : '' }}>
                        {{ $lok->nama_cabang }} (Radius {{ $lok->radius_cabang }}m)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Keterangan / Titik Posko</label>
            <input type="text" name="keterangan" class="form-control" value="{{ $jadwal->keterangan }}" placeholder="Contoh: Depan Pasar / Halaman Kantor Kecamatan">
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Status Jadwal</label>
            <select name="status" class="form-select">
                <option value="1" {{ $jadwal->status == 1 ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ $jadwal->status == 0 ? 'selected' : '' }}>Nonaktif</option>
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
    </div>
</form>
