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
        Schema::table('jadwal_lokasi_layanan', function (Blueprint $table) {
            $table->string('kode_jam_kerja', 10)->nullable()->after('kode_cabang');
            $table->index('kode_jam_kerja');
        });

        // Update existing rows with default jam kerja if available
        $defaultJk = DB::table('jam_kerja')->value('kode_jam_kerja');
        if ($defaultJk) {
            DB::table('jadwal_lokasi_layanan')->whereNull('kode_jam_kerja')->update([
                'kode_jam_kerja' => $defaultJk
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_lokasi_layanan', function (Blueprint $table) {
            $table->dropIndex(['kode_jam_kerja']);
            $table->dropColumn('kode_jam_kerja');
        });
    }
};

