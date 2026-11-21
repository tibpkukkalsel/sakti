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

        // 1. Drop foreign keys referencing cabang.kode_cabang
        try {
            DB::statement('ALTER TABLE `konfigurasi_jk_dept` DROP FOREIGN KEY `fk_kjd_cabang`;');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE `pegawai_backup` DROP FOREIGN KEY `fk_karyawan_cabang`;');
        } catch (\Exception $e) {}

        // 2. Modify child tables and related tables
        if (Schema::hasTable('konfigurasi_jk_dept')) {
            DB::statement('ALTER TABLE `konfigurasi_jk_dept` MODIFY `kode_cabang` VARCHAR(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;');
        }

        if (Schema::hasTable('pegawai_backup')) {
            DB::statement('ALTER TABLE `pegawai_backup` MODIFY `kode_cabang` VARCHAR(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;');
        }

        if (Schema::hasTable('jadwal_lokasi_layanan')) {
            DB::statement('ALTER TABLE `jadwal_lokasi_layanan` MODIFY `kode_cabang` VARCHAR(10) NULL;');
        }

        if (Schema::hasTable('layanan')) {
            DB::statement('ALTER TABLE `layanan` MODIFY `kode_cabang` VARCHAR(10) NULL;');
        }

        if (Schema::hasTable('rekap_penerimaan_pkb')) {
            DB::statement('ALTER TABLE `rekap_penerimaan_pkb` MODIFY `kode_cabang` VARCHAR(10) NULL;');
        }

        if (Schema::hasTable('users')) {
            DB::statement('ALTER TABLE `users` MODIFY `kode_cabang` VARCHAR(10) NULL;');
        }

        // 3. Modify parent table cabang
        if (Schema::hasTable('cabang')) {
            DB::statement('ALTER TABLE `cabang` MODIFY `kode_cabang` VARCHAR(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;');
        }

        // 4. Re-add foreign keys
        try {
            DB::statement('ALTER TABLE `konfigurasi_jk_dept` ADD CONSTRAINT `fk_kjd_cabang` FOREIGN KEY (`kode_cabang`) REFERENCES `cabang` (`kode_cabang`) ON DELETE CASCADE ON UPDATE CASCADE;');
        } catch (\Exception $e) {}

        try {
            DB::statement('ALTER TABLE `pegawai_backup` ADD CONSTRAINT `fk_karyawan_cabang` FOREIGN KEY (`kode_cabang`) REFERENCES `cabang` (`kode_cabang`) ON DELETE SET NULL ON UPDATE CASCADE;');
        } catch (\Exception $e) {}

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
