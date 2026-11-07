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
        Schema::table('rekap_penerimaan_pkb', function (Blueprint $table) {
            if (!Schema::hasColumn('rekap_penerimaan_pkb', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekap_penerimaan_pkb', function (Blueprint $table) {
            if (Schema::hasColumn('rekap_penerimaan_pkb', 'is_locked')) {
                $table->dropColumn('is_locked');
            }
        });
    }
};

