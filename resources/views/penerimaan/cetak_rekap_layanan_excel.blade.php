<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekap Penerimaan Per Layanan</title>
</head>
<body>
    <h3>REKAPITULASI PENERIMAAN PAJAK KENDARAAN BERMOTOR (PKB) BERDASARKAN LAYANAN</h3>
    <p>UPPD SAMSAT BATU LICIN</p>
    <p>Periode: {{ $periodeLabel }}</p>

    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <th>No</th>
                <th>Akun Layanan</th>
                <th>PIC</th>
                <th>NIP PIC</th>
                <th>Unit R2</th>
                <th>Penerimaan R2 (Rp)</th>
                <th>Unit R4</th>
                <th>Penerimaan R4 (Rp)</th>
                <th>Total Unit</th>
                <th>Total Penerimaan (Rp)</th>
                <th>Notice Dicetak</th>
                <th>Hari Operasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekapLayanan as $row)
                <tr>
                    <td align="center">{{ $loop->iteration }}</td>
                    <td>{{ $row->nama_layanan }}</td>
                    <td>{{ $row->nama_pic ?: '-' }}</td>
                    <td style="mso-number-format:'\@';">{{ $row->nip_pic ?: '-' }}</td>
                    <td align="center">{{ $row->unit_r2 }}</td>
                    <td align="right">{{ $row->rp_r2 }}</td>
                    <td align="center">{{ $row->unit_r4 }}</td>
                    <td align="right">{{ $row->rp_r4 }}</td>
                    <td align="center">{{ $row->total_unit }}</td>
                    <td align="right">{{ $row->total_rp }}</td>
                    <td align="center">{{ $row->total_notice }}</td>
                    <td align="center">{{ $row->total_hari_operasi }}</td>
                </tr>
            @endforeach
            <tr style="background-color: #e2e8f0; font-weight: bold;">
                <td colspan="4" align="center">TOTAL</td>
                <td align="center">{{ $grandTotal->unit_r2 }}</td>
                <td align="right">{{ $grandTotal->rp_r2 }}</td>
                <td align="center">{{ $grandTotal->unit_r4 }}</td>
                <td align="right">{{ $grandTotal->rp_r4 }}</td>
                <td align="center">{{ $grandTotal->total_unit }}</td>
                <td align="right">{{ $grandTotal->total_rp }}</td>
                <td align="center">{{ $grandTotal->total_notice }}</td>
                <td align="center">{{ $grandTotal->total_hari_operasi }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
