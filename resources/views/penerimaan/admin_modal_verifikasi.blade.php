<div class="modal-header">
    <h5 class="modal-title">Verifikasi Penerimaan & Notice Pajak</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <!-- Info Ringkas Layanan & Tanggal -->
    <div class="card bg-light border mb-3">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-sm-6">
                    <div class="text-muted small">Akun Layanan:</div>
                    <div class="fw-bold text-dark fs-5">{{ $data->layanan->nama_layanan ?? '-' }}</div>
                    <div class="text-muted small mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin text-danger" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <circle cx="12" cy="11" r="3"></circle>
                            <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                        </svg>
                        <span>{{ $data->cabang->nama_cabang ?? 'UPPD SAMSAT Batulicin' }}</span>
                    </div>
                </div>
                <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                    <div class="text-muted small">Hari / Tanggal:</div>
                    <div class="fw-bold text-dark">{{ formatHariTanggal($data->tgl_penerimaan) }}</div>
                    @if (!empty($data->input_by_nama))
                        <div class="text-primary small mt-1">
                            Petugas: <strong>{{ $data->input_by_nama }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Bukti Lembar Notice (Awal vs Akhir) -->
    <div class="row g-2 mb-3">
        <!-- Lembar Awal Notice -->
        <div class="col-6">
            <div class="card h-100 border text-center">
                <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-primary small">1. Notice Awal</span>
                    <span class="badge bg-primary text-white font-monospace">{{ $data->no_awal_notice ?: '-' }}</span>
                </div>
                <div class="card-body p-2 d-flex flex-column justify-content-center align-items-center" style="min-height: 180px;">
                    @if ($data->foto_awal_url)
                        <a href="javascript:void(0)" onclick="previewFotoNotice('{{ $data->foto_awal_url }}', 'Bukti Notice Awal')" title="Klik untuk perbesar">
                            <img src="{{ $data->foto_awal_url }}" alt="Foto Awal" class="img-fluid rounded border shadow-sm" style="max-height: 160px; object-fit: contain; cursor: pointer;">
                        </a>
                        <small class="text-muted mt-1">Klik gambar untuk perbesar</small>
                    @else
                        <div class="text-muted small py-4">Belum ada foto bukti notice awal</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Lembar Akhir Notice -->
        <div class="col-6">
            <div class="card h-100 border text-center">
                <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-info small">2. Notice Akhir</span>
                    <span class="badge bg-info text-white font-monospace">{{ $data->no_akhir_notice ?: '-' }}</span>
                </div>
                <div class="card-body p-2 d-flex flex-column justify-content-center align-items-center" style="min-height: 180px;">
                    @if ($data->foto_akhir_url)
                        <a href="javascript:void(0)" onclick="previewFotoNotice('{{ $data->foto_akhir_url }}', 'Bukti Notice Akhir')" title="Klik untuk perbesar">
                            <img src="{{ $data->foto_akhir_url }}" alt="Foto Akhir" class="img-fluid rounded border shadow-sm" style="max-height: 160px; object-fit: contain; cursor: pointer;">
                        </a>
                        <small class="text-muted mt-1">Klik gambar untuk perbesar</small>
                    @else
                        <div class="text-muted small py-4">Belum ada foto bukti notice akhir</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Angka Penerimaan -->
    <div class="table-responsive mb-3">
        <table class="table table-bordered table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kategori</th>
                    <th class="text-end">Penerimaan (Rp)</th>
                    <th class="text-center">Jumlah Unit</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Roda 2 (R2)</td>
                    <td class="text-end fw-bold text-success">Rp {{ number_format($data->jumlah_rp_r2, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $data->jumlah_unit_r2 }} Unit</td>
                </tr>
                <tr>
                    <td>Roda 4 (R4)</td>
                    <td class="text-end fw-bold text-info">Rp {{ number_format($data->jumlah_rp_r4, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $data->jumlah_unit_r4 }} Unit</td>
                </tr>
                <tr class="table-light fw-bold">
                    <td>TOTAL</td>
                    <td class="text-end fs-5 text-dark">Rp {{ number_format($data->total_rp, 0, ',', '.') }}</td>
                    <td class="text-center fs-5 text-dark">{{ $data->total_unit }} Unit</td>
                </tr>
                <tr>
                    <td colspan="2" class="text-muted">Total Notice Pajak Dicetak</td>
                    <td class="text-center fw-bold">{{ $data->jumlah_notice }} Lembar</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if ($data->lintasDaerah && $data->lintasDaerah->count() > 0)
        <div class="card border mb-3" style="border-radius: 10px; overflow: hidden;">
            <div class="card-header py-2 bg-light d-flex justify-content-between align-items-center border-bottom">
                <span class="fw-bold small text-dark">Data Cetak Notice Lintas Luar Daerah ({{ $data->lintasDaerah->count() }} Kendaraan)</span>
                <span class="badge bg-primary text-white">Total: Rp {{ number_format($data->lintasDaerah->sum('nominal'), 0, ',', '.') }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover align-middle mb-0" style="font-size: 12px; white-space: nowrap;">
                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 11px; text-transform: uppercase;">
                        <tr>
                            <th class="text-center" style="width: 35px; padding: 8px;">No</th>
                            <th style="padding: 8px 10px;">No. Polisi</th>
                            <th style="padding: 8px 10px;">Nama WP</th>
                            <th class="text-center" style="width: 60px; padding: 8px;">Jenis</th>
                            <th style="padding: 8px 10px;">Asal Daerah</th>
                            <th class="text-end" style="padding: 8px 10px;">Nominal PKB</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data->lintasDaerah as $ld)
                            <tr>
                                <td class="text-center text-muted fw-bold" style="padding: 8px;">{{ $loop->iteration }}</td>
                                <td style="padding: 8px 10px;">
                                    <span class="fw-bold text-dark">{{ $ld->nopol }}</span>
                                </td>
                                <td style="padding: 8px 10px;">
                                    <div class="fw-semibold text-dark">{{ $ld->nama_wp }}</div>
                                    @if (!empty($ld->keterangan))
                                        <div class="text-muted fst-italic" style="font-size: 10.5px;">{{ $ld->keterangan }}</div>
                                    @endif
                                </td>
                                <td class="text-center text-dark fw-semibold" style="padding: 8px;">
                                    {{ $ld->jenis_kendaraan }}
                                </td>
                                <td style="padding: 8px 10px;">{{ $ld->kabupaten->nama_kabupaten ?? '-' }}</td>
                                <td class="text-end fw-bold text-success" style="padding: 8px 10px;">{{ $ld->nominal > 0 ? 'Rp ' . number_format($ld->nominal, 0, ',', '.') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background-color: #f8fafc; font-weight: 700; border-top: 2px solid #e2e8f0;">
                        <tr>
                            <td colspan="3" class="text-uppercase" style="padding: 8px 10px; font-size: 11px; color: #475569;">Total</td>
                            <td class="text-center" style="padding: 8px; font-size: 11px;">{{ $data->lintasDaerah->count() }} Unit</td>
                            <td></td>
                            <td class="text-end text-success" style="padding: 8px 10px;">Rp {{ number_format($data->lintasDaerah->sum('nominal'), 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif

    @if (!empty($data->keterangan))
        <div class="alert alert-light border small py-2 mb-3">
            <strong>Catatan Petugas:</strong> {{ $data->keterangan }}
        </div>
    @endif

    <!-- Status Verifikasi -->
    <div class="d-flex justify-content-between align-items-center p-2 mb-3 rounded border bg-light">
        <span class="text-muted small fw-bold">STATUS VERIFIKASI:</span>
        @if ($data->status_verifikasi == 'draft')
            <span class="badge bg-warning text-dark fs-6">Draft (Layanan Berjalan)</span>
        @elseif ($data->status_verifikasi == 'menunggu')
            <span class="badge bg-info text-white fs-6">Menunggu Verifikasi</span>
        @elseif ($data->status_verifikasi == 'disetujui')
            <span class="badge bg-success text-white fs-6">Disetujui</span>
        @elseif ($data->status_verifikasi == 'ditolak')
            <span class="badge bg-danger text-white fs-6">Ditolak / Perlu Revisi</span>
        @endif
    </div>

    @if ($data->status_verifikasi == 'ditolak' && !empty($data->catatan_verifikasi))
        <div class="alert alert-danger py-2 mb-3 small">
            <strong>Catatan Penolakan:</strong> {{ $data->catatan_verifikasi }}
        </div>
    @endif

    <!-- Area Tindakan Verifikasi (Untuk Admin) -->
    <div class="border-top pt-3">
        @if ($data->status_verifikasi == 'disetujui')
            <!-- Laporan Sudah Disetujui: Hanya Tombol Batalkan / Lakukan Revisi -->
            <button type="button" class="btn btn-warning w-100 py-2 fw-bold text-dark d-flex align-items-center justify-content-center gap-2" data-bs-toggle="collapse" data-bs-target="#formBatalRevisi">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M9 14l-4 -4l4 -4"></path>
                    <path d="M5 10h11a4 4 0 1 1 0 8h-1"></path>
                </svg>
                <span>Batalkan / Lakukan Revisi</span>
            </button>

            <!-- Collapse Form Batalkan / Lakukan Revisi -->
            <div class="collapse mt-3" id="formBatalRevisi">
                <form action="/panel-adm14/penerimaan/{{ $data->id }}/tolak" method="POST" class="p-3 bg-light border rounded">
                    @csrf
                    <label class="form-label small fw-bold text-danger">Catatan Revisi</label>
                    <textarea name="catatan_verifikasi" class="form-control mb-2" rows="2" placeholder="Tuliskan catatan perbaikan untuk petugas layanan..."></textarea>
                    <div class="text-end">
                        <button type="submit" class="btn btn-danger btn-sm">
                            Konfirmasi Pembatalan & Kembalikan untuk Revisi
                        </button>
                    </div>
                </form>
            </div>
        @else
            <!-- Belum Disetujui (Menunggu / Draft / Ditolak): Tampilkan Setujui dan Tolak -->
            <div class="row g-2">
                <!-- Form Setujui -->
                <div class="col-sm-6">
                    <form action="/panel-adm14/penerimaan/{{ $data->id }}/setujui" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M5 12l5 5l10 -10"></path>
                            </svg>
                            <span>Setujui Laporan</span>
                        </button>
                    </form>
                </div>

                <!-- Tombol Tolak (Buka Collapse Catatan) -->
                <div class="col-sm-6">
                    <button type="button" class="btn btn-outline-danger w-100 py-2 fw-bold d-flex align-items-center justify-content-center gap-2" data-bs-toggle="collapse" data-bs-target="#formTolakLaporan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M18 6l-12 12"></path>
                            <path d="M6 6l12 12"></path>
                        </svg>
                        <span>Tolak / Minta Revisi</span>
                    </button>
                </div>
            </div>

            <!-- Collapse Form Tolak -->
            <div class="collapse mt-3" id="formTolakLaporan">
                <form action="/panel-adm14/penerimaan/{{ $data->id }}/tolak" method="POST" class="p-3 bg-light border rounded">
                    @csrf
                    <label class="form-label small fw-bold text-danger">Catatan Revisi</label>
                    <textarea name="catatan_verifikasi" class="form-control mb-2" rows="2" placeholder="Tuliskan catatan perbaikan untuk petugas layanan..." required></textarea>
                    <div class="text-end">
                        <button type="submit" class="btn btn-danger btn-sm">
                            Kirim Penolakan
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
<div class="modal-footer py-2">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
