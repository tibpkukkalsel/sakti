<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $fks = [
            ['table' => 'pengajuan_izin', 'fk' => 'fk_pengajuan_karyawan'],
            ['table' => 'harilibur_detail', 'fk' => 'fk_hldetail_karyawan'],
            ['table' => 'konfigurasi_jamkerja', 'fk' => 'fk_kj_karyawan'],
            ['table' => 'konfigurasi_jamkerja_by_date', 'fk' => 'fk_kjbydate_karyawan'],
        ];

        foreach ($fks as $item) {
            $table = $item['table'];
            $fk = $item['fk'];

            $exists = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = ? 
                  AND CONSTRAINT_NAME = ? 
                  AND CONSTRAINT_TYPE = 'FOREIGN KEY'
            ", [$table, $fk]);

            if (!empty($exists)) {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$fk}`");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Foreign keys to obsolete pegawai_backup table should not be restored.
    }
};

