<?php

namespace Database\Seeders;

use DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RekapPenerimaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            '2026-01-01', // Tahun Baru 2026
            '2026-01-16', // Isra Mi'raj
            '2026-02-17', // Tahun Baru Imlek
            '2026-03-20', '2026-03-21', '2026-03-22', '2026-03-23', '2026-03-24', // Idul Fitri & Cuti Bersama
            '2026-05-01', // Hari Buruh
            '2026-05-14', // Kenaikan Isa Almasih
            '2026-05-27', // Idul Adha
            '2026-06-01', // Hari Lahir Pancasila
            '2026-06-16', // 1 Muharram / Tahun Baru Islam
            '2026-08-17', // HUT RI ke-81
            '2026-08-25', // Maulid Nabi Muhammad
        ];

        // Tambahkan hari libur yang ada di tabel harilibur
        $dbLibur = DB::table('harilibur')->pluck('tanggal_libur')->toArray();
        $holidays = array_unique(array_merge($holidays, $dbLibur));

        $start = new DateTime('2026-01-02');
        $end = new DateTime('2026-11-07');

        $layanans = DB::table('layanan')->pluck('id')->toArray();
        if (empty($layanans)) {
            $layanans = [1, 2];
        }

        foreach ($layanans as $layananId) {
            // Seed random agar hasil angka realistis dan konsisten
            mt_srand(12345 + $layananId);

            $curr = clone $start;
            while ($curr <= $end) {
                $tglStr = $curr->format('Y-m-d');
                $w = (int) $curr->format('w'); // 0 = Minggu

                // Layanan beroperasi Senin s/d Sabtu, libur hari Minggu dan tanggal merah
                if ($w !== 0 && !in_array($tglStr, $holidays)) {
                    // Cek jika sudah ada data manual yang tersimpan
                    $exists = DB::table('rekap_penerimaan_pkb')
                        ->where('layanan_id', $layananId)
                        ->where('tgl_penerimaan', $tglStr)
                        ->first();

                    if (!$exists) {
                        $isSaturday = ($w === 6);

                        // Unit R2 (Roda Dua)
                        $unitR2 = $isSaturday ? mt_rand(12, 22) : mt_rand(20, 38);
                        $avgR2 = mt_rand(210000, 280000);
                        $rpR2 = round(($unitR2 * $avgR2) / 50000) * 50000;

                        // Unit R4 (Roda Empat)
                        $unitR4 = $isSaturday ? mt_rand(4, 9) : mt_rand(7, 16);
                        $avgR4 = mt_rand(1200000, 2200000);
                        $rpR4 = round(($unitR4 * $avgR4) / 50000) * 50000;

                        $totalRp = $rpR2 + $rpR4;
                        $totalUnit = $unitR2 + $unitR4;
                        $notice = $totalUnit;

                        $jam = mt_rand(14, 16);
                        $menit = mt_rand(10, 55);
                        $detik = mt_rand(10, 55);
                        $timestamp = sprintf('%s %02d:%02d:%02d', $tglStr, $jam, $menit, $detik);

                        DB::table('rekap_penerimaan_pkb')->insert([
                            'layanan_id' => $layananId,
                            'tgl_penerimaan' => $tglStr,
                            'jumlah_rp_r2' => $rpR2,
                            'jumlah_rp_r4' => $rpR4,
                            'total_rp' => $totalRp,
                            'jumlah_unit_r2' => $unitR2,
                            'jumlah_unit_r4' => $unitR4,
                            'total_unit' => $totalUnit,
                            'jumlah_notice' => $notice,
                            'keterangan' => null,
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ]);
                    }
                }

                $curr->modify('+1 day');
            }
        }
    }
}

