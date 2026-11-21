<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rekap_penerimaan_pkb', function (Blueprint $table) {
            $table->string('no_awal_notice', 50)->nullable()->after('jumlah_notice');
            $table->string('foto_awal_notice', 255)->nullable()->after('no_awal_notice');
            $table->string('no_akhir_notice', 50)->nullable()->after('foto_awal_notice');
            $table->string('foto_akhir_notice', 255)->nullable()->after('no_akhir_notice');
            $table->enum('status_verifikasi', ['draft', 'menunggu', 'disetujui', 'ditolak'])
                ->default('disetujui')
                ->after('foto_akhir_notice');
            $table->text('catatan_verifikasi')->nullable()->after('status_verifikasi');
            $table->timestamp('verified_at')->nullable()->after('catatan_verifikasi');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('rekap_penerimaan_pkb', function (Blueprint $table) {
            $table->dropColumn([
                'no_awal_notice',
                'foto_awal_notice',
                'no_akhir_notice',
                'foto_akhir_notice',
                'status_verifikasi',
                'catatan_verifikasi',
                'verified_at',
                'verified_by',
            ]);
        });
    }
};
