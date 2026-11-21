@extends('layouts.presensi')

@section('header')
    <!-- App Header -->
    <div class="appHeader bg-primary text-light">
        <div class="left">
            <a href="/dashboard" class="headerButton goBack">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </a>
        </div>
        <div class="pageTitle">Rekap Penerimaan PKB</div>
        <div class="right"></div>
    </div>
    <!-- * App Header -->
@endsection

@section('content')
    <div style="padding-top: 65px; padding-bottom: 75px;">
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

        <!-- 1. Status Pelayanan & Penerimaan Hari Ini -->
        <div class="bg-white border-bottom px-3 py-3 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-bold text-uppercase">Pelayanan Hari Ini</span>
                <div class="d-flex align-items-center gap-1">
                    @if (!$penerimaanHariIni)
                        @if (!empty($isLiburHariIni))
                            <span class="badge bg-warning text-dark" style="font-size: 11px;">Libur</span>
                        @else
                            <span class="badge bg-secondary" style="font-size: 11px;">Belum Buka Layanan</span>
                        @endif
                    @elseif ($penerimaanHariIni->status_verifikasi == 'draft')
                        <span class="badge bg-warning text-dark" style="font-size: 11px;">{{ empty($penerimaanHariIni->no_akhir_notice) ? 'Layanan Berjalan' : 'Draft' }}</span>
                    @elseif ($penerimaanHariIni->status_verifikasi == 'menunggu')
                        <span class="badge bg-info text-white" style="font-size: 11px;">Menunggu Verifikasi</span>
                    @elseif ($penerimaanHariIni->status_verifikasi == 'disetujui')
                        <span class="badge bg-success text-white" style="font-size: 11px;">Disetujui</span>
                    @elseif ($penerimaanHariIni->status_verifikasi == 'ditolak')
                        <span class="badge bg-danger text-white" style="font-size: 11px;">Perlu Revisi</span>
                    @endif

                    @if ($penerimaanHariIni)
                        <div class="dropdown">
                            <a href="javascript:;"
                                class="text-secondary d-inline-flex align-items-center justify-content-center"
                                data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" style="width: 28px; height: 28px; text-decoration: none;">
                                <ion-icon name="ellipsis-vertical" style="font-size: 18px;"></ion-icon>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm"
                                style="min-width: 150px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px 0; z-index: 1050;">
                                @if ($penerimaanHariIni->status_verifikasi != 'disetujui' && $penerimaanHariIni->status_verifikasi != 'menunggu' && !empty($penerimaanHariIni->no_akhir_notice))
                                    <form action="/penerimaan/{{ $penerimaanHariIni->id }}/kirim" method="POST"
                                        onsubmit="return confirm('Kirim laporan penerimaan hari ini ke admin untuk diverifikasi?')" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-primary"
                                            style="padding: 8px 16px; font-size: 13px; font-weight: 600; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                            Kirim
                                        </button>
                                    </form>
                                    <div class="dropdown-divider" style="margin: 2px 0;"></div>
                                @endif
                                @if ($penerimaanHariIni->status_verifikasi == 'menunggu')
                                    <form action="/penerimaan/{{ $penerimaanHariIni->id }}/batal-kirim" method="POST"
                                        onsubmit="return confirm('Batalkan pengiriman laporan hari ini dan kembalikan ke draft?')" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-warning"
                                            style="padding: 8px 16px; font-size: 13px; font-weight: 600; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                            Batalkan
                                        </button>
                                    </form>
                                    <div class="dropdown-divider" style="margin: 2px 0;"></div>
                                @endif
                                <a class="dropdown-item text-dark" href="/penerimaan/{{ $penerimaanHariIni->id }}/lintas-daerah"
                                    style="padding: 8px 16px; font-size: 13px; font-weight: 500;">
                                    Data Lintas Daerah
                                </a>
                                @if ($penerimaanHariIni->status_verifikasi != 'disetujui' && $penerimaanHariIni->status_verifikasi != 'menunggu')
                                    <a class="dropdown-item" href="/penerimaan/{{ $penerimaanHariIni->id }}/tutup-layanan"
                                        style="padding: 8px 16px; font-size: 13px; font-weight: 500;">
                                        Edit
                                    </a>
                                    <form action="/penerimaan/{{ $penerimaanHariIni->id }}/delete" method="POST"
                                        onsubmit="return confirm('Hapus data penerimaan hari ini?')" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"
                                            style="padding: 8px 16px; font-size: 13px; font-weight: 500; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if (!$penerimaanHariIni)
                <div class="text-muted small mt-1 mb-2">
                    @if (!empty($isLiburHariIni))
                        {{ $keteranganLibur }}
                    @else
                        Layanan belum dibuka hari ini. Silakan input nomor awal notice pajak untuk memulai pelayanan.
                    @endif
                </div>
                @if (empty($isLiburHariIni))
                    <a href="/penerimaan/buka-layanan" class="btn btn-primary btn-sm btn-block" style="border-radius: 8px; font-weight: 700;">
                        <ion-icon name="document-text-outline"></ion-icon> Input Notice Awal
                    </a>
                @endif
            @elseif ($penerimaanHariIni->status_verifikasi == 'draft' && empty($penerimaanHariIni->no_akhir_notice))
                <!-- Status DRAFT: Pelayanan Sedang Berjalan (Belum Input Hasil Penerimaan) -->
                <div class="p-2 my-2 border rounded bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">No. Awal Notice:</div>
                            <div class="fw-bold text-primary" style="font-size: 15px;">{{ $penerimaanHariIni->no_awal_notice }}</div>
                        </div>
                        @if ($penerimaanHariIni->foto_awal_url)
                            <a href="javascript:void(0)" onclick="previewFotoNotice('{{ $penerimaanHariIni->foto_awal_url }}', 'Bukti Notice Awal')">
                                <img src="{{ $penerimaanHariIni->foto_awal_url }}" alt="Foto Awal" class="rounded border shadow-sm" style="width: 45px; height: 45px; object-fit: cover; cursor: pointer;">
                            </a>
                        @endif
                    </div>
                </div>
                <div class="text-muted small mb-2">
                    Pelayanan sedang berjalan. Setelah selesai pelayanan, lengkapi nomor akhir notice dan realisasi uang/unit.
                </div>
                <a href="/penerimaan/{{ $penerimaanHariIni->id }}/tutup-layanan" class="btn btn-primary btn-block" style="border-radius: 8px; font-weight: 700;">
                    <ion-icon name="add-circle-outline"></ion-icon> + Input Penerimaan Hari Ini
                </a>
            @else
                <!-- Status DRAFT (sudah isi hasil), MENUNGGU, DISETUJUI, atau DITOLAK -->
                @if ($penerimaanHariIni->status_verifikasi == 'ditolak' && !empty($penerimaanHariIni->catatan_verifikasi))
                    <div class="alert alert-danger py-2 px-3 my-2" style="border-radius: 8px; font-size: 12.5px;">
                        <strong>Catatan Admin:</strong> {{ $penerimaanHariIni->catatan_verifikasi }}
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mt-1">
                    <div>
                        <div style="font-size: 20px; font-weight: 800; color: #10b981;">
                            Rp {{ number_format($penerimaanHariIni->total_rp, 0, ',', '.') }}
                        </div>
                        <div class="text-muted small">
                            {{ \Carbon\Carbon::parse($penerimaanHariIni->tgl_penerimaan)->translatedFormat('l, d F Y') }}
                        </div>
                    </div>
                </div>

                @if ($penerimaanHariIni->status_verifikasi == 'ditolak')
                    <div class="mt-2 mb-2">
                        <a href="/penerimaan/{{ $penerimaanHariIni->id }}/tutup-layanan" class="btn btn-danger btn-sm btn-block" style="border-radius: 8px; font-weight: 700;">
                            <ion-icon name="create-outline"></ion-icon> Perbaiki Laporan Sekarang
                        </a>
                    </div>
                @endif

                <div class="row text-muted small mt-2 pt-2 border-top">
                    <div class="col-6">
                        <span>R2: <strong>Rp
                                {{ number_format($penerimaanHariIni->jumlah_rp_r2, 0, ',', '.') }}</strong></span>
                        <span class="d-block text-secondary">({{ $penerimaanHariIni->jumlah_unit_r2 }} Unit)</span>
                    </div>
                    <div class="col-6 text-end">
                        <span>R4: <strong>Rp
                                {{ number_format($penerimaanHariIni->jumlah_rp_r4, 0, ',', '.') }}</strong></span>
                        <span class="d-block text-secondary">({{ $penerimaanHariIni->jumlah_unit_r4 }} Unit)</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center text-muted small mt-1 pt-1 border-top" style="font-size: 11.5px;">
                    <span>Total Unit: <strong>{{ $penerimaanHariIni->total_unit }} Unit</strong></span>
                    <span>
                        Total Lintas: 
                        @if ($penerimaanHariIni->lintasDaerah && $penerimaanHariIni->lintasDaerah->count() > 0)
                            <a href="/penerimaan/{{ $penerimaanHariIni->id }}/lintas-daerah" class="text-primary fw-bold" style="text-decoration: none;">
                                {{ $penerimaanHariIni->lintasDaerah->count() }} Unit
                            </a>
                        @else
                            <strong class="text-dark">0 Unit</strong>
                        @endif
                    </span>
                    <span>Notice: <strong>{{ $penerimaanHariIni->jumlah_notice }} Lbr</strong></span>
                </div>

                @if (!empty($penerimaanHariIni->no_awal_notice) || !empty($penerimaanHariIni->no_akhir_notice))
                    <div class="text-muted small mt-1 pt-1 border-top d-flex justify-content-between align-items-center" style="font-size: 11.5px;">
                        <span>Notice Seri: <strong>{{ $penerimaanHariIni->no_awal_notice ?: '-' }}</strong> s/d <strong>{{ $penerimaanHariIni->no_akhir_notice ?: '-' }}</strong></span>
                        <div class="d-flex gap-1">
                            @if ($penerimaanHariIni->foto_awal_url)
                                <a href="javascript:void(0)" onclick="previewFotoNotice('{{ $penerimaanHariIni->foto_awal_url }}', 'Bukti Notice Awal')" class="badge bg-light text-primary border" style="cursor: pointer;">Awal</a>
                            @endif
                            @if ($penerimaanHariIni->foto_akhir_url)
                                <a href="javascript:void(0)" onclick="previewFotoNotice('{{ $penerimaanHariIni->foto_akhir_url }}', 'Bukti Notice Akhir')" class="badge bg-light text-primary border" style="cursor: pointer;">Akhir</a>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- 2. Filter (Minggu, Bulan, Tahun, Custom) -->
        <div class="px-3 mt-3 mb-2">
            <ul class="nav nav-tabs style1" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $filter == 'minggu' ? 'active' : '' }}" href="/penerimaan?filter=minggu">
                        Minggu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $filter == 'bulan' ? 'active' : '' }}" href="/penerimaan?filter=bulan">
                        Bulan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $filter == 'tahun' ? 'active' : '' }}" href="/penerimaan?filter=tahun">
                        Tahun
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $filter == 'custom' ? 'active' : '' }}" href="/penerimaan?filter=custom">
                        Custom
                    </a>
                </li>
            </ul>

            @if ($filter == 'custom')
                <form action="/penerimaan" method="GET" class="mt-2 pt-1 pb-1">
                    <input type="hidden" name="filter" value="custom">
                    <div class="row g-2 align-items-end">
                        <div class="col-5">
                            <label class="text-muted small mb-1 d-block" style="font-size: 11px;">Dari Tanggal</label>
                            <input type="date" name="dari" value="{{ $dari }}"
                                class="form-control form-control-sm" required
                                style="font-size: 12px; height: 36px; border-radius: 6px;">
                        </div>
                        <div class="col-5">
                            <label class="text-muted small mb-1 d-block" style="font-size: 11px;">Sampai Tanggal</label>
                            <input type="date" name="sampai" value="{{ $sampai }}"
                                class="form-control form-control-sm" required
                                style="font-size: 12px; height: 36px; border-radius: 6px;">
                        </div>
                        <div class="col-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100"
                                style="height: 36px; border-radius: 6px; padding: 0;" title="Terapkan Filter">
                                <ion-icon name="search-outline" style="font-size: 18px; vertical-align: middle;"></ion-icon>
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>

        <!-- 3. Akumulasi Total Sesuai Filter (Pembatas Biasa) -->
        <div class="bg-white border-top border-bottom px-3 py-2 mb-2">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="text-muted small fw-bold text-uppercase">{{ $labelAkumulasi }}</span>
                <strong style="color: #2A8BF2; font-size: 15px;">Rp
                    {{ number_format($totalAkumulasi->total_rp ?? 0, 0, ',', '.') }}</strong>
            </div>
            <div class="d-flex justify-content-between text-muted small">
                <span>Total Unit:
                    <strong>{{ number_format($totalAkumulasi->total_unit ?? 0, 0, ',', '.') }} Unit</strong></span>
                <span>Total Lintas:
                    <strong>{{ number_format($totalUnitLintasAkumulasi ?? 0, 0, ',', '.') }} Unit</strong></span>
                <span>Notice: <strong>{{ number_format($totalAkumulasi->total_notice ?? 0, 0, ',', '.') }}
                        Lbr</strong></span>
            </div>
        </div>

        <!-- 4. Header Riwayat -->
        <div class="px-3 mt-3 mb-2">
            <div class="text-muted small fw-bold text-uppercase">Riwayat Rekap Penerimaan</div>
        </div>

        <!-- 4. Daftar Riwayat Dengan Pembatas Biasa (Tanpa Card) -->
        <div class="bg-white border-top border-bottom">
            @forelse ($histori as $h)
                <div class="px-3 py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-1">
                        <div>
                            <strong class="text-dark" style="font-size: 14px;">
                                {{ \Carbon\Carbon::parse($h->tgl_penerimaan)->translatedFormat('l, d F Y') }}
                            </strong>
                            <div class="text-muted" style="font-size: 11.5px; display: flex; align-items: center; gap: 4px; margin-top: 1px;">
                                <ion-icon name="location-outline" style="font-size: 13px; color: #ef4444; flex-shrink: 0;"></ion-icon>
                                <span>{{ $h->cabang->nama_cabang ?? 'UPPD SAMSAT Batulicin' }}</span>
                            </div>
                            @if (!empty($h->input_by_nama))
                                <div class="text-muted" style="font-size: 11px; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                    <ion-icon name="person-outline" style="font-size: 12px; color: #2563eb; flex-shrink: 0;"></ion-icon>
                                    <span>Diinput: {{ $h->input_by_nama }}</span>
                                </div>
                            @endif
                            <div style="font-size: 16px; font-weight: 800; color: #10b981; margin-top: 2px;">
                                Rp {{ number_format($h->total_rp, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="mb-1">
                                @if ($h->status_verifikasi == 'draft')
                                    <span class="badge bg-warning text-dark" style="font-size: 10.5px;">Draft</span>
                                @elseif ($h->status_verifikasi == 'menunggu')
                                    <span class="badge bg-info text-white" style="font-size: 10.5px;">Menunggu</span>
                                @elseif ($h->status_verifikasi == 'disetujui')
                                    <span class="badge bg-success text-white" style="font-size: 10.5px;">Disetujui</span>
                                @elseif ($h->status_verifikasi == 'ditolak')
                                    <span class="badge bg-danger text-white" style="font-size: 10.5px;">Ditolak</span>
                                @endif
                            </div>
                            <div class="dropdown">
                                <a href="javascript:;"
                                    class="text-secondary d-inline-flex align-items-center justify-content-center"
                                    data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false" style="width: 32px; height: 32px; text-decoration: none;">
                                    <ion-icon name="ellipsis-vertical" style="font-size: 20px;"></ion-icon>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow-sm"
                                    style="min-width: 150px; border-radius: 8px; border: 1px solid #e2e8f0; padding: 4px 0; z-index: 1050;">
                                    @if ($h->status_verifikasi != 'disetujui' && $h->status_verifikasi != 'menunggu' && !empty($h->no_akhir_notice))
                                        <form action="/penerimaan/{{ $h->id }}/kirim" method="POST"
                                            onsubmit="return confirm('Kirim laporan penerimaan tanggal ini ke admin untuk diverifikasi?')" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-primary"
                                                style="padding: 8px 16px; font-size: 13px; font-weight: 600; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                                Kirim
                                            </button>
                                        </form>
                                        <div class="dropdown-divider" style="margin: 2px 0;"></div>
                                    @endif
                                    @if ($h->status_verifikasi == 'menunggu')
                                        <form action="/penerimaan/{{ $h->id }}/batal-kirim" method="POST"
                                            onsubmit="return confirm('Batalkan pengiriman laporan tanggal ini dan kembalikan ke draft?')" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-warning"
                                                style="padding: 8px 16px; font-size: 13px; font-weight: 600; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                                Batalkan
                                            </button>
                                        </form>
                                        <div class="dropdown-divider" style="margin: 2px 0;"></div>
                                    @endif
                                    <a class="dropdown-item text-dark" href="/penerimaan/{{ $h->id }}/lintas-daerah"
                                        style="padding: 8px 16px; font-size: 13px; font-weight: 500;">
                                        Data Lintas Daerah
                                    </a>
                                    @if ($h->status_verifikasi != 'disetujui' && $h->status_verifikasi != 'menunggu')
                                        <a class="dropdown-item" href="/penerimaan/{{ $h->id }}/tutup-layanan"
                                            style="padding: 8px 16px; font-size: 13px; font-weight: 500;">
                                            Edit
                                        </a>
                                        <form action="/penerimaan/{{ $h->id }}/delete" method="POST"
                                            onsubmit="return confirm('Hapus data penerimaan tanggal ini?')" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger"
                                                style="padding: 8px 16px; font-size: 13px; font-weight: 500; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row text-muted small mt-2">
                        <div class="col-6">
                            <span>R2: <strong>Rp {{ number_format($h->jumlah_rp_r2, 0, ',', '.') }}</strong></span>
                            <span class="d-block text-secondary">({{ $h->jumlah_unit_r2 }} Unit)</span>
                        </div>
                        <div class="col-6 text-end">
                            <span>R4: <strong>Rp {{ number_format($h->jumlah_rp_r4, 0, ',', '.') }}</strong></span>
                            <span class="d-block text-secondary">({{ $h->jumlah_unit_r4 }} Unit)</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center text-muted small mt-1 pt-1 border-top" style="font-size: 11.5px;">
                        <span>Total Unit: <strong>{{ $h->total_unit }} Unit</strong></span>
                        <span>
                            Total Lintas: 
                            @if ($h->lintasDaerah && $h->lintasDaerah->count() > 0)
                                <a href="/penerimaan/{{ $h->id }}/lintas-daerah" class="text-primary fw-bold" style="text-decoration: none;">
                                    {{ $h->lintasDaerah->count() }} Unit
                                </a>
                            @else
                                <strong class="text-dark">0 Unit</strong>
                            @endif
                        </span>
                        <span>Notice: <strong>{{ $h->jumlah_notice }} Lbr</strong></span>
                    </div>

                    @if (!empty($h->no_awal_notice) || !empty($h->no_akhir_notice))
                        <div class="text-muted small mt-1 pt-1 border-top d-flex justify-content-between align-items-center" style="font-size: 11.5px;">
                            <span>Notice Seri: <strong>{{ $h->no_awal_notice ?: '-' }}</strong> s/d <strong>{{ $h->no_akhir_notice ?: '-' }}</strong></span>
                            <div class="d-flex gap-1">
                                @if ($h->foto_awal_url)
                                    <a href="javascript:void(0)" onclick="previewFotoNotice('{{ $h->foto_awal_url }}', 'Bukti Notice Awal')" class="badge bg-light text-primary border" style="cursor: pointer;">Awal</a>
                                @endif
                                @if ($h->foto_akhir_url)
                                    <a href="javascript:void(0)" onclick="previewFotoNotice('{{ $h->foto_akhir_url }}', 'Bukti Notice Akhir')" class="badge bg-light text-primary border" style="cursor: pointer;">Akhir</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if ($h->keterangan)
                        <div class="text-muted small mt-1 fst-italic">
                            Catatan: {{ $h->keterangan }}
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-4 text-muted">
                    <ion-icon name="wallet-outline" style="font-size: 36px; opacity: 0.4;"></ion-icon>
                    <div class="mt-1 small">
                        @if ($filter == 'minggu')
                            Belum ada riwayat penerimaan pada minggu ini.
                        @elseif ($filter == 'bulan')
                            Belum ada riwayat penerimaan pada bulan ini.
                        @elseif ($filter == 'tahun')
                            Belum ada riwayat penerimaan pada tahun ini.
                        @elseif ($filter == 'custom')
                            Belum ada riwayat penerimaan pada rentang tanggal yang dipilih.
                        @else
                            Belum ada riwayat rekap penerimaan.
                        @endif
                    </div>
                </div>
            @endforelse
        </div>

        <div class="p-3">
            {{ $histori->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
@endsection
