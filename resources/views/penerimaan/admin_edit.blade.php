<form action="/panel-adm14/penerimaan/{{ $data->id }}/update" method="POST" id="formAdminEditPenerimaan">
    @csrf
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label required">Akun Layanan</label>
            <select name="layanan_id" class="form-select" required>
                @foreach ($daftarLayanan as $l)
                    <option value="{{ $l->id }}" {{ $data->layanan_id == $l->id ? 'selected' : '' }}>
                        {{ $l->nama_layanan }} (PIC: {{ $l->nama_pic }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label required">Tanggal Penerimaan</label>
            <input type="date" name="tgl_penerimaan" class="form-control" value="{{ $data->tgl_penerimaan->format('Y-m-d') }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label required">Penerimaan Roda 2 / R2 (Rp)</label>
            <input type="text" name="jumlah_rp_r2" id="edit_rp_r2" class="form-control rupiah-edit"
                   value="{{ number_format($data->jumlah_rp_r2, 0, ',', '.') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label required">Penerimaan Roda 4 / R4 (Rp)</label>
            <input type="text" name="jumlah_rp_r4" id="edit_rp_r4" class="form-control rupiah-edit"
                   value="{{ number_format($data->jumlah_rp_r4, 0, ',', '.') }}" required>
        </div>

        <div class="col-12">
            <div class="p-3 bg-light rounded border text-center">
                <div class="text-muted small">TOTAL PENERIMAAN (R2 + R4)</div>
                <h3 class="text-success fw-bold mb-0" id="edit_preview_total_rp">
                    Rp {{ number_format($data->total_rp, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        <div class="col-md-4">
            <label class="form-label required">Jumlah Unit R2</label>
            <input type="number" name="jumlah_unit_r2" class="form-control" value="{{ $data->jumlah_unit_r2 }}" min="0" required>
        </div>
        <div class="col-md-4">
            <label class="form-label required">Jumlah Unit R4</label>
            <input type="number" name="jumlah_unit_r4" class="form-control" value="{{ $data->jumlah_unit_r4 }}" min="0" required>
        </div>
        <div class="col-md-4">
            <label class="form-label required">Jumlah Notice Dicetak</label>
            <input type="number" name="jumlah_notice" class="form-control" value="{{ $data->jumlah_notice }}" min="0" required>
        </div>

        <div class="col-12">
            <label class="form-label">Keterangan (Opsional)</label>
            <textarea name="keterangan" class="form-control">{{ $data->keterangan }}</textarea>
        </div>
    </div>

    <div class="modal-footer px-0 pb-0 pt-3 mt-3 border-top">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-save me-1"></i> Simpan Perubahan
        </button>
    </div>
</form>

<script>
    function hitungTotalEdit() {
        var r2 = $('#edit_rp_r2').val().replace(/[^0-9]/g, '') || 0;
        var r4 = $('#edit_rp_r4').val().replace(/[^0-9]/g, '') || 0;
        var total = parseInt(r2) + parseInt(r4);
        $('#edit_preview_total_rp').text('Rp ' + formatRupiahAdmin(total.toString()));
    }

    $('.rupiah-edit').on('keyup', function() {
        $(this).val(formatRupiahAdmin($(this).val()));
        hitungTotalEdit();
    });
</script>
