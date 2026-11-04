<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Penerimaan PKB - UPPD SAMSAT Batu Licin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            color: #000;
            margin: 20px;
        }

        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .header-kop h3 {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-kop h2 {
            margin: 4px 0;
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .header-kop p {
            margin: 0;
            font-size: 10pt;
        }

        .title-doc {
            text-align: center;
            margin-bottom: 18px;
        }

        .title-doc h4 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .title-doc span {
            font-size: 10pt;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 9.5pt;
        }

        table.data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }

        .ttd-box {
            float: right;
            width: 280px;
            text-align: center;
            margin-top: 20px;
        }

        .clear { clear: both; }

        @media print {
            .no-print { display: none; }
            @page {
                size: landscape;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; font-weight: bold; background: #059669; color: white; border: none; border-radius: 6px; cursor: pointer;">
            Cetak / Simpan PDF
        </button>
    </div>

    <div class="header-kop">
        <h3>PEMERINTAH PROVINSI KALIMANTAN SELATAN</h3>
        <h3>BADAN PENDAPATAN DAERAH</h3>
        <h2>UPPD SAMSAT BATU LICIN</h2>
        <p>Jl. Transmigrasi Km. 4,5 Batu Licin, Kabupaten Tanah Bumbu, Kalimantan Selatan</p>
    </div>

    <div class="title-doc">
        <h4>REKAPITULASI PENERIMAAN HARIAN PAJAK KENDARAAN BERMOTOR (PKB)</h4>
        <span>Periode: {{ \Carbon\Carbon::parse($tgl_dari)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($tgl_sampai)->translatedFormat('d F Y') }}</span>
        @if ($filterLayanan)
            <br><span>Layanan: <strong>{{ $filterLayanan->nama_layanan }}</strong> (PIC: {{ $filterLayanan->nama_pic }})</span>
        @endif
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 30px;">No</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">Akun Layanan</th>
                <th rowspan="2">PIC</th>
                <th colspan="2">Roda 2 (R2)</th>
                <th colspan="2">Roda 4 (R4)</th>
                <th rowspan="2">Total Penerimaan (Rp)</th>
                <th rowspan="2">Total Unit</th>
                <th rowspan="2">Notice Dicetak</th>
                <th rowspan="2">Keterangan</th>
            </tr>
            <tr>
                <th>Unit</th>
                <th>Jumlah (Rp)</th>
                <th>Unit</th>
                <th>Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($penerimaan as $d)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($d->tgl_penerimaan)->format('d/m/Y') }}</td>
                    <td class="fw-bold">{{ $d->layanan->nama_layanan ?? '-' }}</td>
                    <td>{{ $d->layanan->nama_pic ?? '-' }}</td>
                    <td class="text-center">{{ number_format($d->jumlah_unit_r2, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($d->jumlah_rp_r2, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($d->jumlah_unit_r4, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($d->jumlah_rp_r4, 0, ',', '.') }}</td>
                    <td class="text-end fw-bold">{{ number_format($d->total_rp, 0, ',', '.') }}</td>
                    <td class="text-center fw-bold">{{ number_format($d->total_unit, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($d->jumlah_notice, 0, ',', '.') }}</td>
                    <td>{{ $d->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center py-3">Tidak ada data penerimaan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="fw-bold" style="background-color: #f2f2f2;">
                <td colspan="4" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-center">{{ number_format($summary->sum_unit_r2 ?? 0, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($summary->sum_rp_r2 ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($summary->sum_unit_r4 ?? 0, 0, ',', '.') }}</td>
                <td class="text-end">{{ number_format($summary->sum_rp_r4 ?? 0, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($summary->sum_total_rp ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($summary->sum_total_unit ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">{{ number_format($summary->sum_notice ?? 0, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="ttd-box">
        <p>Batu Licin, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <p style="margin-bottom: 60px;">KEPALA UPPD SAMSAT BATU LICIN</p>
        <p class="fw-bold" style="text-decoration: underline; margin-bottom: 2px;">( .................................................... )</p>
        <p style="margin: 0;">NIP. ....................................................</p>
    </div>

    <div class="clear"></div>

</body>
</html>
