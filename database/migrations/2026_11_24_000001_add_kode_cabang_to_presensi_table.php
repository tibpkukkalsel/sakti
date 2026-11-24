<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('presensi', 'kode_cabang')) {
            Schema::table('presensi', function (Blueprint $table) {
                $table->string('kode_cabang', 10)->nullable()->after('nik')->index();
            });
        }

        // Backfill data historis presensi jika ada rekap penerimaan atau jadwal pada hari tersebut
        try {
            DB::statement("
                UPDATE presensi
                LEFT JOIN rekap_penerimaan_pkb ON (
                    rekap_penerimaan_pkb.layanan_id = presensi.nik
                    AND rekap_penerimaan_pkb.tgl_penerimaan = presensi.tgl_presensi
                )
                LEFT JOIN jadwal_lokasi_layanan AS j_khusus ON (
                    j_khusus.layanan_id = presensi.nik
                    AND j_khusus.tanggal = presensi.tgl_presensi
                    AND j_khusus.status = 1
                )
                LEFT JOIN jadwal_lokasi_layanan AS j_rutin ON (
                    j_rutin.layanan_id = presensi.nik
                    AND j_rutin.jenis_jadwal = 'rutin'
                    AND j_rutin.status = 1
                    AND j_rutin.hari = CASE DAYOFWEEK(presensi.tgl_presensi)
                        WHEN 1 THEN 'Minggu'
                        WHEN 2 THEN 'Senin'
                        WHEN 3 THEN 'Selasa'
                        WHEN 4 THEN 'Rabu'
                        WHEN 5 THEN 'Kamis'
                        WHEN 6 THEN 'Jumat'
                        WHEN 7 THEN 'Sabtu'
                    END
                )
                LEFT JOIN layanan ON layanan.id = presensi.nik
                SET presensi.kode_cabang = COALESCE(rekap_penerimaan_pkb.kode_cabang, j_khusus.kode_cabang, j_rutin.kode_cabang, layanan.kode_cabang)
                WHERE presensi.kode_cabang IS NULL
            ");
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('presensi', 'kode_cabang')) {
            Schema::table('presensi', function (Blueprint $table) {
                $table->dropColumn('kode_cabang');
            });
        }
    }
};
