<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement("ALTER TABLE konfigurasi_jk_dept DROP FOREIGN KEY fk_kjd_dept");
        } catch (\Exception $e) {
            // Already dropped or non-existent
        }

        try {
            DB::statement("ALTER TABLE pegawai_backup DROP FOREIGN KEY fk_karyawan_dept");
        } catch (\Exception $e) {
            // Already dropped or non-existent
        }

        try {
            DB::statement("DROP VIEW IF EXISTS departemen");
        } catch (\Exception $e) {
            // View does not exist
        }

        try {
            DB::statement("DROP TABLE IF EXISTS seksi");
        } catch (\Exception $e) {
            // Table does not exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Table seksi is no longer needed
    }
};
