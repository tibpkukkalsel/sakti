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
        if (!Schema::hasColumn('rekap_penerimaan_pkb', 'kode_cabang')) {
            Schema::table('rekap_penerimaan_pkb', function (Blueprint $table) {
                $table->char('kode_cabang', 5)->nullable()->after('layanan_id')->index();
            });
        }

        // Backfill data historis penerimaan agar terkunci permanen pada lokasi jadwal saat itu
        DB::statement("
            UPDATE rekap_penerimaan_pkb
            LEFT JOIN jadwal_lokasi_layanan AS j_khusus ON (
                j_khusus.layanan_id = rekap_penerimaan_pkb.layanan_id
                AND j_khusus.tanggal = rekap_penerimaan_pkb.tgl_penerimaan
                AND j_khusus.status = 1
            )
            LEFT JOIN jadwal_lokasi_layanan AS j_rutin ON (
                j_rutin.layanan_id = rekap_penerimaan_pkb.layanan_id
                AND j_rutin.jenis_jadwal = 'rutin'
                AND j_rutin.status = 1
                AND j_rutin.hari = CASE DAYOFWEEK(rekap_penerimaan_pkb.tgl_penerimaan)
                    WHEN 1 THEN 'Minggu'
                    WHEN 2 THEN 'Senin'
                    WHEN 3 THEN 'Selasa'
                    WHEN 4 THEN 'Rabu'
                    WHEN 5 THEN 'Kamis'
                    WHEN 6 THEN 'Jumat'
                    WHEN 7 THEN 'Sabtu'
                END
            )
            LEFT JOIN layanan ON layanan.id = rekap_penerimaan_pkb.layanan_id
            SET rekap_penerimaan_pkb.kode_cabang = COALESCE(j_khusus.kode_cabang, j_rutin.kode_cabang, layanan.kode_cabang, 'SM1')
            WHERE rekap_penerimaan_pkb.kode_cabang IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('rekap_penerimaan_pkb', 'kode_cabang')) {
            Schema::table('rekap_penerimaan_pkb', function (Blueprint $table) {
                $table->dropColumn('kode_cabang');
            });
        }
    }
};

