<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // 1. Update relational tables from old kode_cabang string to cabang.id
        if (Schema::hasColumn('cabang', 'kode_cabang')) {
            if (Schema::hasTable('layanan') && Schema::hasColumn('layanan', 'kode_cabang')) {
                DB::statement('UPDATE `layanan` l JOIN `cabang` c ON l.kode_cabang = c.kode_cabang SET l.kode_cabang = c.id;');
            }
            if (Schema::hasTable('jadwal_lokasi_layanan') && Schema::hasColumn('jadwal_lokasi_layanan', 'kode_cabang')) {
                DB::statement('UPDATE `jadwal_lokasi_layanan` j JOIN `cabang` c ON j.kode_cabang = c.kode_cabang SET j.kode_cabang = c.id;');
            }
            if (Schema::hasTable('rekap_penerimaan_pkb') && Schema::hasColumn('rekap_penerimaan_pkb', 'kode_cabang')) {
                DB::statement('UPDATE `rekap_penerimaan_pkb` r JOIN `cabang` c ON r.kode_cabang = c.kode_cabang SET r.kode_cabang = c.id;');
            }
            if (Schema::hasTable('users') && Schema::hasColumn('users', 'kode_cabang')) {
                DB::statement('UPDATE `users` u JOIN `cabang` c ON u.kode_cabang = c.kode_cabang SET u.kode_cabang = c.id;');
            }
            if (Schema::hasTable('konfigurasi_jk_dept') && Schema::hasColumn('konfigurasi_jk_dept', 'kode_cabang')) {
                DB::statement('UPDATE `konfigurasi_jk_dept` k JOIN `cabang` c ON k.kode_cabang = c.kode_cabang SET k.kode_cabang = c.id;');
            }
            if (Schema::hasTable('pegawai_backup') && Schema::hasColumn('pegawai_backup', 'kode_cabang')) {
                DB::statement('UPDATE `pegawai_backup` p JOIN `cabang` c ON p.kode_cabang = c.kode_cabang SET p.kode_cabang = c.id;');
            }
        }

        // 2. Drop foreign keys referencing cabang.kode_cabang
        try {
            DB::statement('ALTER TABLE `konfigurasi_jk_dept` DROP FOREIGN KEY `fk_kjd_cabang`;');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE `pegawai_backup` DROP FOREIGN KEY `fk_karyawan_cabang`;');
        } catch (\Exception $e) {}

        // 3. Drop column kode_cabang from table cabang
        if (Schema::hasColumn('cabang', 'kode_cabang')) {
            try {
                DB::statement('ALTER TABLE `cabang` DROP INDEX `cabang_kode_cabang_unique`;');
            } catch (\Exception $e) {}

            DB::statement('ALTER TABLE `cabang` DROP COLUMN `kode_cabang`;');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversible if needed
    }
};
