<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Rekap Presensi Detail - {{ $namabulan[(int)$bulan] ?? '' }} {{ $tahun }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8pt;
            color: #111;
            line-height: 1.2;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .header-title {
            text-align: center;
            vertical-align: middle;
        }

        .header-title h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-title h3 {
            font-size: 11pt;
            font-weight: bold;
            margin: 2px 0;
            padding: 0;
            text-transform: uppercase;
        }

        .header-title p {
            font-size: 10pt;
            font-weight: bold;
            margin: 0;
            padding: 0;
            text-transform: uppercase;
        }

        .tabelpresensi {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.5pt;
        }

        .tabelpresensi th {
            border: 0.5px solid #222;
            padding: 3px 1px;
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
        }

        .tabelpresensi td {
            border: 0.5px solid #222;
            padding: 2px 1px;
            text-align: center;
            vertical-align: top;
        }

        .text-left {
            text-align: left !important;
            padding-left: 3px !important;
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

    <table class="header-table">
        <tr>
            <td align="center" style="text-align: center; vertical-align: middle;">
                <table align="center" style="margin: 0 auto;">
                    <tr>
                        <td style="vertical-align: middle; padding-right: 15px;">
                            @if ($has_logo)
                                <img src="{{ $logo_path }}" width="48" height="65" alt="Logo">
                            @endif
                        </td>
                        <td class="header-title" style="text-align: center; vertical-align: middle;">
                            <h2>REKAP PRESENSI UPPD SAMSAT BATU LICIN</h2>
                            <h3>REKAPITULASI PRESENSI LAYANAN</h3>
                            <p>BULAN {{ strtoupper($namabulan[(int)$bulan] ?? '') }} TAHUN {{ $tahun }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="tabelpresensi">
        <thead>
            <tr>
                <th rowspan="2" style="width: 20px;">NO.</th>
                <th rowspan="2" style="width: 120px;">NAMA LAYANAN</th>
                <th colspan="{{ $jmlhari }}">BULAN {{ strtoupper($namabulan[(int)$bulan] ?? '') }} {{ $tahun }}</th>
                <th rowspan="2" style="width: 25px;">H</th>
                <th rowspan="2" style="width: 25px;">X</th>
                <th rowspan="2" style="width: 25px;">L</th>
                <th rowspan="2" style="width: 25px;">TK</th>
            </tr>
            <tr>
                @foreach ($rangetanggal as $d)
                    @if ($d != null)
                        <th>{{ date('d', strtotime($d)) }}</th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($rekap as $r)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-left" style="font-weight: 500;">{{ $r->nama_lengkap }}</td>

                    @php
                        $jml_hadir = 0;
                        $jml_maintenance = 0;
                        $jml_libur = 0;
                        $jml_tk = 0;
                        $color = '';
                        for ($i = 1; $i <= $jmlhari; $i++) {
                            $tgl = 'tgl_' . $i;
                            $tgl_presensi = $rangetanggal[$i - 1];
                            $search_items = [
                                'nik' => $r->nik,
                                'tanggal_libur' => $tgl_presensi,
                            ];
                            $ceklibur = cekkaryawanlibur($datalibur, $search_items);

                            $datapresensi = explode('|', $r->$tgl ?? '');
                            if (!empty($r->$tgl)) {
                                $jam_in = $datapresensi[0] ?? '';
                                $jam_out = $datapresensi[1] ?? '';
                                $status = $datapresensi[2] ?? '';
                                $nama_jam_kerja = $datapresensi[3] ?? '';
                                $jam_masuk = $datapresensi[4] ?? '';
                                $jam_pulang = $datapresensi[5] ?? '';
                                $total_jam = $datapresensi[8] ?? '';
                                $lintashari = $datapresensi[9] ?? '';
                                $awal_jam_istirahat = $datapresensi[10] ?? '';
                                $akhir_jam_istirahat = $datapresensi[11] ?? '';
                                $terlambat = hitungjamterlambat($jam_masuk, $jam_in);
                                $terlambat_desimal = hitungjamterlambatdesimal($jam_masuk, $jam_in);
                                $j_terlambat = explode(':', $terlambat);
                                $jam_terlambat = intVal($j_terlambat[0]);
                                if ($jam_terlambat < 1) {
                                    $jam_mulai = $jam_masuk;
                                } else {
                                    $jam_mulai = $jam_in > $jam_masuk ? $jam_in : $jam_masuk;
                                }
                                $jam_berakhir = $jam_out > $jam_pulang ? $jam_pulang : $jam_out;
                                $total_jam_kerja = hitungjamkerja(
                                    $tgl_presensi,
                                    date('H:i', strtotime($jam_mulai)),
                                    date('H:i', strtotime($jam_berakhir)),
                                    $total_jam,
                                    $lintashari,
                                    date('H:i', strtotime($awal_jam_istirahat)),
                                    date('H:i', strtotime($akhir_jam_istirahat)),
                                );
                            } else {
                                $status = '';
                                $jam_in = '';
                                $jam_out = '';
                                $jam_masuk = '';
                                $jam_pulang = '';
                                $nama_jam_kerja = '';
                                $total_jam_kerja = 0;
                                $terlambat = 0;
                                $terlambat_desimal = 0;
                            }

                            $cekhari = gethari(date('D', strtotime($tgl_presensi)));

                            // Cek apakah hari ini merupakan hari kerja sesuai jadwal layanan
                            $is_harikerja = cekJadwalKerja(
                                $r->nik, 
                                $r->kode_cabang ?? null, 
                                $r->kode_dept ?? null, 
                                $tgl_presensi, 
                                $cekhari, 
                                $jadwal_by_date ?? null, 
                                $jadwal_pegawai ?? null, 
                                $jadwal_dept ?? null
                            );

                            if ($status == 'h' || $status == 'tl') {
                                $jml_hadir += 1;
                                $color = '#198754';
                            } elseif ($status == 'm' || $status == 'x') {
                                $jml_maintenance += 1;
                                $color = '#0d6efd';
                            } elseif (!empty($ceklibur) || !$is_harikerja) {
                                $jml_libur += 1;
                                $color = '#fd7e14';
                            } else {
                                $jml_tk += 1;
                                $color = '#dc3545';
                            }
                    @endphp
                        <td style="background-color: {{ $color }}; color: #ffffff; font-size: 6pt;">
                            @if ($status == 'h' || $status == 'tl')
                                <b style="color: #ffffff;">{{ $nama_jam_kerja }}</b><br>
                                <span style="color: #ffffff;">{{ $jam_masuk }}-{{ $jam_pulang }}</span><br>
                                <span style="color: #ffffff;">{{ $jam_in }}-{{ $jam_out }}</span><br>
                                <span style="color: #ffffff;">Jam: {{ $total_jam_kerja }}</span>
                            @elseif ($status == 'm' || $status == 'x')
                                <span style="color: white; font-weight: bold;">X</span>
                            @elseif (!empty($ceklibur) || !$is_harikerja)
                                <span style="color: white; font-weight: bold;">L</span>
                            @else
                                <span style="color: white; font-weight: bold;">TK</span>
                            @endif
                        </td>
                    @php
                        }
                    @endphp
                    <td style="font-weight: bold; color: {{ !empty($jml_hadir) ? '#198754' : '#000' }};">{{ !empty($jml_hadir) ? $jml_hadir : '-' }}</td>
                    <td style="font-weight: bold; color: {{ !empty($jml_maintenance) ? '#0d6efd' : '#000' }};">{{ !empty($jml_maintenance) ? $jml_maintenance : '-' }}</td>
                    <td style="font-weight: bold; color: {{ !empty($jml_libur) ? '#fd7e14' : '#000' }};">{{ !empty($jml_libur) ? $jml_libur : '-' }}</td>
                    <td style="font-weight: bold; color: {{ !empty($jml_tk) ? '#dc3545' : '#000' }};">{{ !empty($jml_tk) ? $jml_tk : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="legend-box" style="width: 100%; margin-top: 8px; font-size: 7.5pt;">
        <tr>
            <td style="vertical-align: top;">
                <b>Keterangan:</b> &nbsp;
                <span class="legend-item"><span class="color-badge" style="display:inline-block;width:12px;height:12px;border:0.5px solid #555;vertical-align:middle;margin-right:3px;background-color: #198754;"></span><b>H</b> = Hadir Layanan</span> &nbsp;
                <span class="legend-item"><span class="color-badge" style="display:inline-block;width:12px;height:12px;border:0.5px solid #555;vertical-align:middle;margin-right:3px;background-color: #0d6efd;"></span><b>X</b> = Maintance Layanan</span> &nbsp;
                <span class="legend-item"><span class="color-badge" style="display:inline-block;width:12px;height:12px;border:0.5px solid #555;vertical-align:middle;margin-right:3px;background-color: #fd7e14;"></span><b>L</b> = Libur</span> &nbsp;
                <span class="legend-item"><span class="color-badge" style="display:inline-block;width:12px;height:12px;border:0.5px solid #555;vertical-align:middle;margin-right:3px;background-color: #dc3545;"></span><b>TK</b> = Tidak Ada Keterangan</span>
            </td>
        </tr>
    </table>

    @if ($harilibur->isNotEmpty())
        <div style="margin-top: 8px; font-size: 8pt;">
            <b>Daftar Hari Libur :</b>
            <ol style="margin: 2px 0; padding-left: 18px;">
                @foreach ($harilibur as $d)
                    <li>{{ date('d-m-Y', strtotime($d->tanggal_libur)) }} - {{ $d->keterangan }}</li>
                @endforeach
            </ol>
        </div>
    @endif

    @php
        if (!function_exists('tgl_indo')) {
            function tgl_indo($tanggal)
            {
                $bulan = [
                    1 => 'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember',
                ];
                $pecahkan = explode('-', $tanggal);
                return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
            }
        }
    @endphp

    @php
        $kota_cetak = $konfigurasi_laporan->kota_cetak ?? 'Pelaihari';
        $pejabat1_jabatan = $konfigurasi_laporan->pejabat1_jabatan ?? 'KEPALA UPPD SAMSAT BATU LICIN';
        $pejabat1_nama = $konfigurasi_laporan->pejabat1_nama ?? 'FEBRY RAHARJO, S.IP, MM';
        $pejabat1_nip = $konfigurasi_laporan->pejabat1_nip ?? '19870222 200701 1 002';
        $pejabat2_jabatan = $konfigurasi_laporan->pejabat2_jabatan ?? 'KASUBBAG TATA USAHA';
        $pejabat2_nama = $konfigurasi_laporan->pejabat2_nama ?? 'ADIMAS FITRIANDI, ST';
        $pejabat2_nip = $konfigurasi_laporan->pejabat2_nip ?? '19800813 200712 1 001';
    @endphp

    <table class="ttd-table">
        <tr>
            <td style="width: 55%;"></td>
            <td style="width: 45%; text-align: center;">{{ $kota_cetak }}, {{ tgl_indo(date('Y-m-d')) }}</td>
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
