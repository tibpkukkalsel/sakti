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
        if (!Schema::hasTable('jadwal_lokasi_layanan')) {
            Schema::create('jadwal_lokasi_layanan', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('layanan_id');
                $table->string('hari', 20);
                $table->char('kode_cabang', 3);
                $table->string('keterangan', 255)->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();

                $table->unique(['layanan_id', 'hari'], 'uk_layanan_hari');
                $table->index('layanan_id');
                $table->index('kode_cabang');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_lokasi_layanan');
    }
};
