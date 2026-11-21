@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/penerimaan" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Data Lintas Daerah</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <div style="padding-top: 65px; padding-bottom: 80px;">
        @if (Session::get('success'))
            <div class="alert alert-success mx-3 my-2">
                {{ Session::get('success') }}
            </div>
        @endif

        @if (Session::get('warning'))
            <div class="alert alert-warning mx-3 my-2">
                {{ Session::get('warning') }}
            </div>
        @endif

        @if (Session::get('error'))
            <div class="alert alert-danger mx-3 my-2">
                {{ Session::get('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mx-3 my-2">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 1. Ringkasan Rekap Pelayanan & Rekonsiliasi Notice -->
        <div class="bg-white border-bottom px-3 py-3 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-bold text-uppercase">Informasi Layanan</span>
                @if ($data->status_verifikasi == 'disetujui')
                    <span class="badge bg-success text-white" style="font-size: 11px;">Disetujui</span>
                @elseif ($data->status_verifikasi == 'menunggu')
                    <span class="badge bg-info text-white" style="font-size: 11px;">Menunggu Verifikasi</span>
                @elseif ($data->status_verifikasi == 'ditolak')
                    <span class="badge bg-danger text-white" style="font-size: 11px;">Perlu Revisi</span>
                @else
                    <span class="badge bg-warning text-dark" style="font-size: 11px;">Draft</span>
                @endif
            </div>

            <div class="fw-bold text-dark" style="font-size: 14.5px;">
                {{ \Carbon\Carbon::parse($data->tgl_penerimaan)->translatedFormat('l, d F Y') }}
            </div>
            <div class="text-muted small d-flex align-items-center gap-1 mt-1">
                <ion-icon name="location-outline" style="color: #ef4444; font-size: 14px;"></ion-icon>
                <span>{{ $data->cabang->nama_cabang ?? 'UPPD SAMSAT Batulicin' }}</span>
                <span class="mx-1">•</span>
                <span>{{ $data->layanan->nama_layanan ?? '' }}</span>
            </div>

            <!-- Kartu Rekonsiliasi Notice vs Unit -->
            <div class="p-2 mt-2 rounded border bg-light">
                <div class="d-flex justify-content-between text-muted small">
                    <span>Notice Fisik Terpakai:</span>
                    <strong class="text-dark">{{ $data->jumlah_notice ?: 0 }} Lembar</strong>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-1">
                    <span>Unit PKB Lokal:</span>
                    <strong>{{ $totalUnitLokal }} Unit</strong>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-1">
                    <span>Notice Lintas Luar Daerah:</span>
                    <strong class="text-primary">{{ $totalUnitLintas }} Unit</strong>
                </div>
                <div class="d-flex justify-content-between small mt-1 pt-1 border-top fw-bold">
                    <span>Total Unit Terlayani:</span>
                    <span class="{{ $data->jumlah_notice == $totalUnitSemua ? 'text-success' : 'text-danger' }}">
                        {{ $totalUnitSemua }} Unit
                    </span>
                </div>
                @if ($totalRpLintas > 0)
                    <div class="d-flex justify-content-between text-muted small mt-1 pt-1 border-top">
                        <span>Total Nominal Lintas:</span>
                        <strong class="text-success">Rp {{ number_format($totalRpLintas, 0, ',', '.') }}</strong>
                    </div>
                @endif
            </div>

            @if ($isEditable)
                <div class="mt-2">
                    <button type="button" class="btn btn-primary btn-block btn-sm" data-toggle="modal" data-target="#modalTambahLintas" style="border-radius: 8px; font-weight: 700; height: 38px;">
                        <ion-icon name="add-circle-outline" style="font-size: 18px; margin-right: 4px; vertical-align: -3px;"></ion-icon> Tambah Data Lintas Daerah
                    </button>
                </div>
            @endif
        </div>

        <!-- 2. Daftar Transaksi Cetak Lintas Luar Daerah -->
        <div class="px-3 mt-3 mb-2">
            <div class="text-muted small fw-bold text-uppercase">Daftar Notice Lintas Luar Daerah</div>
        </div>

        <div class="mx-3 mb-3 bg-white border rounded shadow-sm" style="border-radius: 10px; overflow: hidden;">
            <div class="table-responsive" style="min-height: 140px;">
                <table class="table table-hover table-striped align-middle mb-0" style="font-size: 12px; white-space: nowrap;">
                    <thead style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="text-center" style="width: 40px; padding: 10px 8px;">No</th>
                            <th style="padding: 10px 12px;">No. Polisi</th>
                            <th style="padding: 10px 12px;">Wajib Pajak</th>
                            <th class="text-center" style="width: 65px; padding: 10px 8px;">Jenis</th>
                            <th style="padding: 10px 12px;">Asal Daerah</th>
                            <th class="text-end" style="padding: 10px 12px;">Nominal PKB</th>
                            @if ($isEditable)
                                <th class="text-center" style="width: 50px; padding: 10px 8px;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data->lintasDaerah as $item)
                            <tr>
                                <td class="text-center text-muted fw-bold" style="padding: 10px 8px;">
                                    {{ $loop->iteration }}
                                </td>
                                <td style="padding: 10px 12px;">
                                    <span class="fw-bold text-dark" style="font-size: 13px;">{{ $item->nopol }}</span>
                                </td>
                                <td style="padding: 10px 12px;">
                                    <div class="fw-bold text-dark" style="font-size: 12.5px;">{{ $item->nama_wp }}</div>
                                    @if (!empty($item->keterangan))
                                        <div class="text-muted small fst-italic" style="font-size: 11px; margin-top: 1px;">{{ $item->keterangan }}</div>
                                    @endif
                                </td>
                                <td class="text-center" style="padding: 10px 8px;">
                                    <span class="text-dark fw-semibold" style="font-size: 12px;">{{ $item->jenis_kendaraan }}</span>
                                </td>
                                <td style="padding: 10px 12px;">
                                    <span class="text-secondary fw-semibold">{{ $item->kabupaten->nama_kabupaten ?? '-' }}</span>
                                </td>
                                <td class="text-end fw-bold text-success" style="padding: 10px 12px; font-size: 12.5px;">
                                    {{ $item->nominal > 0 ? 'Rp ' . number_format($item->nominal, 0, ',', '.') : '-' }}
                                </td>
                                @if ($isEditable)
                                    <td class="text-center" style="padding: 6px 8px;">
                                        <div class="dropdown">
                                            <a href="javascript:;"
                                                class="text-secondary d-inline-flex align-items-center justify-content-center"
                                                data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false" style="width: 28px; height: 28px; text-decoration: none;">
                                                <ion-icon name="ellipsis-vertical" style="font-size: 18px;"></ion-icon>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-end shadow-sm"
                                                style="min-width: 120px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px 0; z-index: 1060;">
                                                <a class="dropdown-item btn-edit-lintas text-dark" href="javascript:;"
                                                    data-id="{{ $item->id }}"
                                                    data-nopol="{{ $item->nopol }}"
                                                    data-nama="{{ $item->nama_wp }}"
                                                    data-kabupaten="{{ $item->kabupaten_id }}"
                                                    data-jenis="{{ $item->jenis_kendaraan }}"
                                                    data-nominal="{{ $item->nominal ? number_format($item->nominal, 0, ',', '.') : '' }}"
                                                    data-keterangan="{{ $item->keterangan }}"
                                                    style="padding: 8px 16px; font-size: 13px; font-weight: 500;">
                                                    Edit
                                                </a>
                                                <form action="/penerimaan/lintas-daerah/{{ $item->id }}/delete" method="POST"
                                                    onsubmit="return confirm('Hapus data notice lintas {{ $item->nopol }}?')" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger"
                                                        style="padding: 8px 16px; font-size: 13px; font-weight: 500; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isEditable ? 7 : 6 }}" class="text-center py-4 text-muted">
                                    <ion-icon name="car-outline" style="font-size: 36px; opacity: 0.35; display: block; margin: 0 auto 6px;"></ion-icon>
                                    <span class="small">Belum ada data cetak notice lintas luar daerah.</span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($data->lintasDaerah && $data->lintasDaerah->count() > 0)
                        <tfoot style="background-color: #f8fafc; font-weight: 700; border-top: 2px solid #e2e8f0;">
                            <tr>
                                <td colspan="3" class="text-uppercase" style="padding: 10px 12px; font-size: 11px; color: #475569;">Total Lintas Daerah</td>
                                <td class="text-center" style="padding: 10px 8px; font-size: 11.5px;">{{ $totalUnitLintas }} Unit</td>
                                <td></td>
                                <td class="text-end text-success" style="padding: 10px 12px; font-size: 12.5px;">
                                    Rp {{ number_format($totalRpLintas, 0, ',', '.') }}
                                </td>
                                @if ($isEditable)
                                    <td></td>
                                @endif
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    @if ($isEditable)
        <!-- Modal Tambah Data Lintas -->
        <div class="modal fade" id="modalTambahLintas" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 12px;">
                    <div class="modal-header py-2 px-3 border-bottom">
                        <h5 class="modal-title" style="font-size: 15px; font-weight: 700;">Tambah Data Lintas Daerah</h5>
                        <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="border: none; background: transparent; font-size: 24px; color: #64748b; line-height: 1;">
                            &times;
                        </button>
                    </div>
                    <form action="/penerimaan/{{ $data->id }}/lintas-daerah/store" method="POST">
                        @csrf
                        <div class="modal-body p-3">
                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">No. Polisi</label>
                                    <input type="text" class="form-control" name="nopol" placeholder="DA 1234 XX" required autocomplete="off" style="text-transform: uppercase; font-weight: 700;">
                                </div>
                            </div>

                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">Nama Wajib Pajak</label>
                                    <input type="text" class="form-control" name="nama_wp" placeholder="Nama Wajib Pajak" required autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">Jenis Kendaraan</label>
                                    <select class="form-control custom-select" name="jenis_kendaraan" required>
                                        <option value="R2">R2</option>
                                        <option value="R4">R4</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">Asal Daerah</label>
                                    <select class="form-control custom-select" name="kabupaten_id" required>
                                        <option value="">Pilih Kabupaten / Kota</option>
                                        @foreach ($daftarKabupaten as $kab)
                                            <option value="{{ $kab->id }}">{{ $kab->nama_kabupaten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">Nominal PKB</label>
                                    <input type="text" class="form-control rupiah-input" name="nominal" placeholder="Rp 0" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">Keterangan</label>
                                    <input type="text" class="form-control" name="keterangan" placeholder="Keterangan">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer py-2 px-3 border-top">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 6px;">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 6px; font-weight: 700;">Simpan Data Lintas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Data Lintas -->
        <div class="modal fade" id="modalEditLintas" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 12px;">
                    <div class="modal-header py-2 px-3 border-bottom">
                        <h5 class="modal-title" style="font-size: 15px; font-weight: 700;">Edit Data Lintas Daerah</h5>
                        <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="border: none; background: transparent; font-size: 24px; color: #64748b; line-height: 1;">
                            &times;
                        </button>
                    </div>
                    <form action="" method="POST" id="formEditLintas">
                        @csrf
                        <div class="modal-body p-3">
                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">No. Polisi</label>
                                    <input type="text" class="form-control" name="nopol" id="edit_nopol" placeholder="DA 1234 XX" required autocomplete="off" style="text-transform: uppercase; font-weight: 700;">
                                </div>
                            </div>

                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">Nama Wajib Pajak</label>
                                    <input type="text" class="form-control" name="nama_wp" id="edit_nama_wp" placeholder="Nama Wajib Pajak" required autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">Jenis Kendaraan</label>
                                    <select class="form-control custom-select" name="jenis_kendaraan" id="edit_jenis_kendaraan" required>
                                        <option value="R2">R2</option>
                                        <option value="R4">R4</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">Asal Daerah</label>
                                    <select class="form-control custom-select" name="kabupaten_id" id="edit_kabupaten_id" required>
                                        <option value="">Pilih Kabupaten / Kota</option>
                                        @foreach ($daftarKabupaten as $kab)
                                            <option value="{{ $kab->id }}">{{ $kab->nama_kabupaten }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">Nominal PKB</label>
                                    <input type="text" class="form-control rupiah-input" name="nominal" id="edit_nominal" placeholder="Rp 0" autocomplete="off">
                                </div>
                            </div>

                            <div class="form-group boxed mb-2">
                                <div class="input-wrapper">
                                    <label class="label">Keterangan</label>
                                    <input type="text" class="form-control" name="keterangan" id="edit_keterangan" placeholder="Keterangan">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer py-2 px-3 border-top">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 6px;">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm" style="border-radius: 6px; font-weight: 700;">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('myscript')
    <script>
        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }

        $(document).on('keyup', '.rupiah-input', function() {
            var val = $(this).val();
            $(this).val(formatRupiah(val));
        });

        $(document).on('click', '.btn-edit-lintas', function() {
            var id = $(this).data('id');
            var nopol = $(this).data('nopol');
            var nama = $(this).data('nama');
            var kab = $(this).data('kabupaten');
            var jenis = $(this).data('jenis');
            var nominal = $(this).data('nominal');
            var ket = $(this).data('keterangan');

            $('#formEditLintas').attr('action', '/penerimaan/lintas-daerah/' + id + '/update');
            $('#edit_nopol').val(nopol);
            $('#edit_nama_wp').val(nama);
            $('#edit_kabupaten_id').val(kab);
            $('#edit_jenis_kendaraan').val(jenis);
            $('#edit_nominal').val(nominal);
            $('#edit_keterangan').val(ket);

            $('#modalEditLintas').modal('show');
        });
    </script>
@endpush
