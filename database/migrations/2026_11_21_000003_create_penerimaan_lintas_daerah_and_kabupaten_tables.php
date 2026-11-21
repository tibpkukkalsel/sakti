<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Kabupaten/Kota Kalimantan Selatan
        if (!Schema::hasTable('kabupaten')) {
            Schema::create('kabupaten', function (Blueprint $table) {
                $table->id();
                $table->string('nama_kabupaten', 100);
                $table->timestamps();
            });

            // Seeder 13 Kabupaten / Kota di Kalimantan Selatan
            $kabupaten = [
                'Kota Banjarmasin',
                'Kota Banjarbaru',
                'Kabupaten Banjar',
                'Kabupaten Barito Kuala',
                'Kabupaten Tanah Laut',
                'Kabupaten Kotabaru',
                'Kabupaten Tanah Bumbu',
                'Kabupaten Tapin',
                'Kabupaten Hulu Sungai Selatan',
                'Kabupaten Hulu Sungai Tengah',
                'Kabupaten Hulu Sungai Utara',
                'Kabupaten Balangan',
                'Kabupaten Tabalong',
            ];

            foreach ($kabupaten as $nama) {
                DB::table('kabupaten')->insert([
                    'nama_kabupaten' => $nama,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Tabel Data Cetak Notice Lintas Luar Daerah
        if (!Schema::hasTable('penerimaan_lintas_daerah')) {
            Schema::create('penerimaan_lintas_daerah', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('penerimaan_id'); // Match rekap_penerimaan_pkb.id int(10) unsigned
                $table->unsignedBigInteger('kabupaten_id'); // Match kabupaten.id bigint(20) unsigned
                $table->string('nopol', 20);
                $table->string('nama_wp', 100);
                $table->enum('jenis_kendaraan', ['R2', 'R4'])->default('R2');
                $table->decimal('nominal', 15, 2)->default(0);
                $table->string('keterangan', 255)->nullable();
                $table->timestamps();

                $table->foreign('penerimaan_id')
                    ->references('id')
                    ->on('rekap_penerimaan_pkb')
                    ->onDelete('cascade');

                $table->foreign('kabupaten_id')
                    ->references('id')
                    ->on('kabupaten')
                    ->onDelete('cascade');

                $table->index('penerimaan_id');
                $table->index('kabupaten_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('penerimaan_lintas_daerah');
        Schema::dropIfExists('kabupaten');
    }
};
