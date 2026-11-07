<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Rekap Penerimaan Per Lokasi - {{ $periodeLabel }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #111;
            line-height: 1.3;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .header-title {
            text-align: center;
            vertical-align: middle;
        }

        .header-title h4 {
            font-size: 10pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-title h3 {
            font-size: 11pt;
            font-weight: bold;
            margin: 1px 0;
            padding: 0;
            text-transform: uppercase;
        }

        .header-title h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 2px 0;
            padding: 0;
            text-transform: uppercase;
            color: #0b2e59;
        }

        .header-title p {
            font-size: 8pt;
            margin: 1px 0 0 0;
            padding: 0;
            color: #444;
        }

        .doc-title {
            text-align: center;
            margin: 10px 0 14px 0;
        }

        .doc-title h3 {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            padding: 0;
            text-decoration: underline;
        }

        .doc-title p {
            font-size: 9pt;
            margin: 3px 0 0 0;
            font-weight: bold;
            color: #333;
        }

        .info-filter {
            margin-bottom: 8px;
            font-size: 8.5pt;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-bottom: 14px;
        }

        table.data-table th {
            border: 0.5px solid #222;
            padding: 5px 4px;
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }

        table.data-table td {
            border: 0.5px solid #222;
            padding: 4px 5px;
            vertical-align: middle;
        }

        table.data-table tr.total-row td {
            background-color: #e2e8f0;
            font-weight: bold;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .text-start {
            text-align: left;
        }

        .sub-table-title {
            font-size: 9.5pt;
            font-weight: bold;
            margin: 14px 0 6px 0;
            text-transform: uppercase;
            color: #1e293b;
        }

        .ttd-table {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
            font-size: 9pt;
        }

        .ttd-table td {
            vertical-align: top;
        }
    </style>
</head>

<body>
    @php
        $logo_path = str_replace('\\', '/', public_path('assets/img/Logo Provinsi Kalimantan Selatan.png'));
        $has_logo = file_exists($logo_path);
    @endphp

    <!-- Kop Surat Resmi -->
    <table class="header-table">
        <tr>
            <td align="center" style="text-align: center; vertical-align: middle;">
                <table align="center" style="margin: 0 auto;">
                    <tr>
                        <td style="vertical-align: middle; padding-right: 15px;">
                            @if ($has_logo)
                                <img src="{{ $logo_path }}" width="50" height="66" alt="Logo Kalsel">
                            @endif
                        </td>
                        <td class="header-title" style="text-align: center; vertical-align: middle;">
                            <h4>Pemerintah Provinsi Kalimantan Selatan</h4>
                            <h3>Badan Pendapatan Daerah</h3>
                            <h2>UPPD SAMSAT BATULICIN</h2>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        <h3>REKAPITULASI PENERIMAAN PAJAK KENDARAAN BERMOTOR (PKB)</h3>
        <p>BERDASARKAN LOKASI PELAYANAN SAMSAT KELILING</p>
    </div>

    <!-- Informasi Periode & Filter -->
    <table class="info-filter">
        <tr>
            <td style="width: 130px; font-weight: bold;">Periode Rekap</td>
            <td style="width: 10px;">:</td>
            <td><strong>{{ $periodeLabel }}</strong></td>
            <td style="width: 140px; font-weight: bold; text-align: right;">Tanggal Cetak:</td>
            <td style="width: 100px; text-align: right;">{{ date('d-m-Y H:i') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Filter Lokasi</td>
            <td>:</td>
            <td colspan="3">
                @if ($lokasiTerpilih)
                    <strong>{{ $lokasiTerpilih->nama_cabang }}</strong> (Kode: {{ $lokasiTerpilih->kode_cabang }})
                @else
                    Semua Titik / Lokasi Pelayanan
                @endif
            </td>
        </tr>
    </table>

    <!-- Tabel Rekap Per Lokasi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px;" rowspan="2">No</th>
                <th rowspan="2">Titik / Lokasi Pelayanan</th>
                <th rowspan="2" style="width: 70px;">Kode</th>
                <th colspan="2">Penerimaan R2</th>
                <th colspan="2">Penerimaan R4</th>
                <th rowspan="2" style="width: 80px;">Total Unit<br>(R2 + R4)</th>
                <th rowspan="2" style="width: 120px;">Total Penerimaan (Rp)</th>
                <th rowspan="2" style="width: 75px;">Notice<br>(Lembar)</th>
                <th rowspan="2" style="width: 60px;">Hari<br>Operasi</th>
            </tr>
            <tr>
                <th style="width: 50px;">Unit</th>
                <th style="width: 95px;">Jumlah (Rp)</th>
                <th style="width: 50px;">Unit</th>
                <th style="width: 95px;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekapLokasi as $row)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>
                        <strong>{{ $row->nama_cabang }}</strong>
                        @if ($row->lokasi_cabang && $row->lokasi_cabang != '-')
                            <br><small style="color: #555;">{{ $row->lokasi_cabang }}</small>
                        @endif
                    </td>
                    <td class="text-center font-monospace">{{ $row->kode_cabang }}</td>
                    <td class="text-center">{{ number_format($row->unit_r2, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($row->rp_r2, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($row->unit_r4, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($row->rp_r4, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold">{{ number_format($row->total_unit, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($row->total_rp, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($row->total_notice, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $row->total_hari_operasi }} hari</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 15px; color: #777;">
                        Tidak ada data penerimaan lokasi pada periode yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-center"><strong>TOTAL REKAPITULASI</strong></td>
                <td class="text-center"><strong>{{ number_format($grandTotal->unit_r2, 0, ',', '.') }}</strong></td>
                <td class="text-end"><strong>Rp {{ number_format($grandTotal->rp_r2, 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($grandTotal->unit_r4, 0, ',', '.') }}</strong></td>
                <td class="text-end"><strong>Rp {{ number_format($grandTotal->rp_r4, 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($grandTotal->total_unit, 0, ',', '.') }}</strong></td>
                <td class="text-end"><strong>Rp {{ number_format($grandTotal->total_rp, 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ number_format($grandTotal->total_notice, 0, ',', '.') }}</strong></td>
                <td class="text-center"><strong>{{ $grandTotal->total_hari_operasi }} hari</strong></td>
            </tr>
        </tfoot>
    </table>

    {{-- Jika 1 Lokasi dipilih secara spesifik, tampilkan tabel rincian hariannya --}}
    @if ($detailHarian && count($detailHarian) > 0)
        <div class="sub-table-title">
            Rincian Penerimaan di Lokasi: {{ $lokasiTerpilih->nama_cabang }}
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th style="width: 85px;">Tanggal</th>
                    <th>Akun Layanan</th>
                    <th style="width: 50px;">Unit R2</th>
                    <th style="width: 95px;">Realisasi R2 (Rp)</th>
                    <th style="width: 50px;">Unit R4</th>
                    <th style="width: 95px;">Realisasi R4 (Rp)</th>
                    <th style="width: 65px;">Total Unit</th>
                    <th style="width: 110px;">Total Penerimaan (Rp)</th>
                    <th style="width: 65px;">Notice (Lbr)</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($detailHarian as $dh)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ date('d-m-Y', strtotime($dh->tgl_penerimaan)) }}</td>
                        <td>{{ $dh->nama_layanan }}</td>
                        <td class="text-center">{{ number_format($dh->jumlah_unit_r2, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($dh->jumlah_rp_r2, 0, ',', '.') }}</td>
                        <td class="text-center">{{ number_format($dh->jumlah_unit_r4, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($dh->jumlah_rp_r4, 0, ',', '.') }}</td>
                        <td class="text-center"><strong>{{ number_format($dh->total_unit, 0, ',', '.') }}</strong></td>
                        <td class="text-end"><strong>Rp {{ number_format($dh->total_rp, 0, ',', '.') }}</strong></td>
                        <td class="text-center">{{ number_format($dh->jumlah_notice, 0, ',', '.') }}</td>
                        <td>{{ $dh->keterangan ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @php
        if (!function_exists('tgl_indo_penerimaan')) {
            function tgl_indo_penerimaan($tanggal)
            {
                $bulan = [
                    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ];
                $pecahkan = explode('-', $tanggal);
                return $pecahkan[2] . ' ' . $bulan[(int) $pecahkan[1]] . ' ' . $pecahkan[0];
            }
        }

        $kota_cetak = $konfigurasi_laporan->kota_cetak ?? 'Batulicin';
        $pejabat1_jabatan = $konfigurasi_laporan->pejabat1_jabatan ?? 'KEPALA UPPD SAMSAT BATULICIN';
        $pejabat1_nama = $konfigurasi_laporan->pejabat1_nama ?? 'FEBRY RAHARJO, S.IP, MM';
        $pejabat1_nip = $konfigurasi_laporan->pejabat1_nip ?? '19870222 200701 1 002';
        $pejabat2_jabatan = $konfigurasi_laporan->pejabat2_jabatan ?? 'KASUBBAG TATA USAHA';
        $pejabat2_nama = $konfigurasi_laporan->pejabat2_nama ?? 'ADIMAS FITRIANDI, ST';
        $pejabat2_nip = $konfigurasi_laporan->pejabat2_nip ?? '19800813 200712 1 001';
    @endphp

    <!-- Tanda Tangan Pejabat Pengesah -->
    <table class="ttd-table">
        <tr>
            <td style="width: 50%;"></td>
            <td style="width: 50%; text-align: center;">{{ $kota_cetak }}, {{ tgl_indo_penerimaan(date('Y-m-d')) }}</td>
        </tr>
        <tr>
            <td style="text-align: center;">Mengetahui,</td>
            <td style="text-align: center;"></td>
        </tr>
        <tr>
            <td style="text-align: center; font-weight: bold;">{!! nl2br(e($pejabat1_jabatan)) !!}</td>
            <td style="text-align: center; font-weight: bold;">{!! nl2br(e($pejabat2_jabatan)) !!}</td>
        </tr>
        <tr>
            <td style="height: 55px;"></td>
            <td style="height: 55px;"></td>
        </tr>
        <tr>
            <td style="text-align: center; vertical-align: bottom;">
                <b><u>{{ $pejabat1_nama }}</u></b><br>
                <span>NIP. {{ $pejabat1_nip }}</span>
            </td>
            <td style="text-align: center; vertical-align: bottom;">
                <b><u>{{ $pejabat2_nama }}</u></b><br>
                <span>NIP. {{ $pejabat2_nip }}</span>
            </td>
        </tr>
    </table>
</body>

</html>

