<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rekap_penerimaan_pkb', function (Blueprint $table) {
            if (Schema::hasColumn('rekap_penerimaan_pkb', 'is_locked')) {
                $table->dropColumn('is_locked');
            }
            if (Schema::hasColumn('rekap_penerimaan_pkb', 'input_by_nama')) {
                $table->dropColumn('input_by_nama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rekap_penerimaan_pkb', function (Blueprint $table) {
            if (!Schema::hasColumn('rekap_penerimaan_pkb', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('keterangan');
            }
            if (!Schema::hasColumn('rekap_penerimaan_pkb', 'input_by_nama')) {
                $table->string('input_by_nama', 100)->nullable()->after('input_by_petugas_id');
            }
        });
    }
};
