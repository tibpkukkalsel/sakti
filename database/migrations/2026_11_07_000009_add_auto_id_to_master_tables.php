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
        // 1. Table cabang (Lokasi)
        if (Schema::hasTable('cabang') && !Schema::hasColumn('cabang', 'id')) {
            try {
                DB::statement('ALTER TABLE cabang ADD UNIQUE INDEX cabang_kode_cabang_unique (kode_cabang)');
            } catch (\Exception $e) {
                // Index might already exist
            }
            DB::statement('ALTER TABLE cabang DROP PRIMARY KEY, ADD COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
        }

        // 2. Table jam_kerja
        if (Schema::hasTable('jam_kerja') && !Schema::hasColumn('jam_kerja', 'id')) {
            try {
                DB::statement('ALTER TABLE jam_kerja ADD UNIQUE INDEX jam_kerja_kode_jam_kerja_unique (kode_jam_kerja)');
            } catch (\Exception $e) {
                // Index might already exist
            }
            DB::statement('ALTER TABLE jam_kerja DROP PRIMARY KEY, ADD COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
        }

        // 3. Table master_cuti
        if (Schema::hasTable('master_cuti') && !Schema::hasColumn('master_cuti', 'id')) {
            try {
                DB::statement('ALTER TABLE master_cuti ADD UNIQUE INDEX master_cuti_kode_cuti_unique (kode_cuti)');
            } catch (\Exception $e) {
                // Index might already exist
            }
            DB::statement('ALTER TABLE master_cuti DROP PRIMARY KEY, ADD COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reversal logic if rolled back
        if (Schema::hasTable('cabang') && Schema::hasColumn('cabang', 'id')) {
            DB::statement('ALTER TABLE cabang DROP COLUMN id, ADD PRIMARY KEY (kode_cabang)');
            DB::statement('ALTER TABLE cabang DROP INDEX cabang_kode_cabang_unique');
        }

        if (Schema::hasTable('jam_kerja') && Schema::hasColumn('jam_kerja', 'id')) {
            DB::statement('ALTER TABLE jam_kerja DROP COLUMN id, ADD PRIMARY KEY (kode_jam_kerja)');
            DB::statement('ALTER TABLE jam_kerja DROP INDEX jam_kerja_kode_jam_kerja_unique');
        }

        if (Schema::hasTable('master_cuti') && Schema::hasColumn('master_cuti', 'id')) {
            DB::statement('ALTER TABLE master_cuti DROP COLUMN id, ADD PRIMARY KEY (kode_cuti)');
            DB::statement('ALTER TABLE master_cuti DROP INDEX master_cuti_kode_cuti_unique');
        }
    }
};

