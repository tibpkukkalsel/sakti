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
        DB::statement("
            CREATE OR REPLACE VIEW `karyawan` AS 
            SELECT 
                `layanan`.`id` AS `nik`,
                `layanan`.`nama_layanan` AS `nama_lengkap`,
                `layanan`.`jabatan_pic` AS `jabatan`,
                `layanan`.`no_hp` AS `no_hp`,
                `layanan`.`foto` AS `foto`,
                'L01' AS `kode_dept`,
                `layanan`.`kode_cabang` AS `kode_cabang`,
                `layanan`.`status_location` AS `status_location`,
                `layanan`.`status_jam_kerja` AS `status_jam_kerja`,
                `layanan`.`status_scan` AS `status_scan`,
                `layanan`.`password` AS `password`,
                `layanan`.`remember_token` AS `remember_token`,
                `layanan`.`username` AS `username`,
                `layanan`.`nama_layanan` AS `nama_layanan`
            FROM `layanan`
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `karyawan`");
    }
};
