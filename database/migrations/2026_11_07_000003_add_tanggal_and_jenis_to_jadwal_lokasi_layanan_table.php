<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwal_lokasi_layanan', function (Blueprint $table) {
            // Drop unique index uk_layanan_hari to allow event schedules
            $table->dropUnique('uk_layanan_hari');

            // Add jenis_jadwal and tanggal
            $table->string('jenis_jadwal', 20)->default('rutin')->after('hari');
            $table->date('tanggal')->nullable()->after('jenis_jadwal');

            // Add indexes
            $table->index(['layanan_id', 'hari'], 'idx_layanan_hari');
            $table->index(['layanan_id', 'tanggal'], 'idx_layanan_tanggal');
            $table->index('tanggal');
            $table->index('jenis_jadwal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_lokasi_layanan', function (Blueprint $table) {
            $table->dropIndex('idx_layanan_hari');
            $table->dropIndex('idx_layanan_tanggal');
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['jenis_jadwal']);

            $table->dropColumn(['jenis_jadwal', 'tanggal']);
            $table->unique(['layanan_id', 'hari'], 'uk_layanan_hari');
        });
    }
};

