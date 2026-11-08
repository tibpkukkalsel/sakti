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
        if (Schema::hasTable('harilibur')) {
            // Modify kode_cabang to varchar(20)
            DB::statement("ALTER TABLE harilibur MODIFY kode_cabang VARCHAR(20) DEFAULT 'ALL'");

            // Add auto id primary key if not present
            if (!Schema::hasColumn('harilibur', 'id')) {
                try {
                    DB::statement('ALTER TABLE harilibur ADD UNIQUE INDEX harilibur_kode_libur_unique (kode_libur)');
                } catch (\Exception $e) {
                    // Index may already exist
                }
                DB::statement('ALTER TABLE harilibur DROP PRIMARY KEY, ADD COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('harilibur') && Schema::hasColumn('harilibur', 'id')) {
            DB::statement('ALTER TABLE harilibur DROP COLUMN id, ADD PRIMARY KEY (kode_libur)');
            DB::statement('ALTER TABLE harilibur DROP INDEX harilibur_kode_libur_unique');
        }
    }
};

