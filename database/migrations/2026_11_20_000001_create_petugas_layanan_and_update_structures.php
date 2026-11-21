<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat tabel petugas_layanan (Akun Perorangan di dalam Layanan)
        if (!Schema::hasTable('petugas_layanan')) {
            Schema::create('petugas_layanan', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('layanan_id')->index();
                $table->string('nama_petugas', 150);
                $table->string('nip', 50)->nullable();
                $table->string('jabatan', 100)->nullable()->default('Petugas Layanan');
                $table->string('no_hp', 25)->nullable();
                $table->string('username', 50)->unique();
                $table->string('password', 255);
                $table->string('foto', 100)->nullable();
                $table->boolean('status_aktif')->default(true);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2. Tambah kolom mode_presensi pada tabel layanan (pilihan: 'bebas' atau 'jadwal')
        if (Schema::hasTable('layanan')) {
            Schema::table('layanan', function (Blueprint $table) {
                if (!Schema::hasColumn('layanan', 'mode_presensi')) {
                    $table->string('mode_presensi', 20)->default('bebas')->after('status_scan');
                }
            });
        }

        // 3. Tambah kolom audit petugas pada tabel presensi
        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) {
                if (!Schema::hasColumn('presensi', 'petugas_id')) {
                    $table->unsignedBigInteger('petugas_id')->nullable()->after('nik');
                }
                if (!Schema::hasColumn('presensi', 'petugas_nama')) {
                    $table->string('petugas_nama', 150)->nullable()->after('petugas_id');
                }
            });
        }

        // 4. Tambah kolom audit petugas input pada tabel rekap_penerimaan_pkb
        if (Schema::hasTable('rekap_penerimaan_pkb')) {
            Schema::table('rekap_penerimaan_pkb', function (Blueprint $table) {
                if (!Schema::hasColumn('rekap_penerimaan_pkb', 'input_by_petugas_id')) {
                    $table->unsignedBigInteger('input_by_petugas_id')->nullable()->after('is_locked');
                }
                if (!Schema::hasColumn('rekap_penerimaan_pkb', 'input_by_nama')) {
                    $table->string('input_by_nama', 150)->nullable()->after('input_by_petugas_id');
                }
            });
        }

        // 5. Buat tabel jadwal_petugas_layanan (Untuk Mode Jadwal Mingguan)
        if (!Schema::hasTable('jadwal_petugas_layanan')) {
            Schema::create('jadwal_petugas_layanan', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('layanan_id')->index();
                $table->unsignedBigInteger('petugas_id')->index();
                $table->string('hari', 20)->nullable(); // 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'
                $table->date('tanggal')->nullable(); // Tanggal spesifik jika berlaku untuk tanggal tertentu
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }

        // 6. Migrasikan data PIC layanan yang sudah ada ke petugas_layanan jika belum ada data
        $existingLayanan = DB::table('layanan')->get();
        foreach ($existingLayanan as $lay) {
            $exists = DB::table('petugas_layanan')->where('username', $lay->username)->first();
            if (!$exists && !empty($lay->nama_pic)) {
                DB::table('petugas_layanan')->insert([
                    'layanan_id' => $lay->id,
                    'nama_petugas' => $lay->nama_pic,
                    'nip' => $lay->nip_pic,
                    'jabatan' => $lay->jabatan_pic ?: 'Petugas ' . $lay->nama_layanan,
                    'no_hp' => $lay->no_hp,
                    'username' => $lay->username,
                    'password' => $lay->password ?: Hash::make('12345'),
                    'foto' => $lay->foto,
                    'status_aktif' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_petugas_layanan');
        Schema::dropIfExists('petugas_layanan');

        if (Schema::hasTable('layanan') && Schema::hasColumn('layanan', 'mode_presensi')) {
            Schema::table('layanan', function (Blueprint $table) {
                $table->dropColumn('mode_presensi');
            });
        }

        if (Schema::hasTable('presensi')) {
            Schema::table('presensi', function (Blueprint $table) {
                if (Schema::hasColumn('presensi', 'petugas_id')) {
                    $table->dropColumn('petugas_id');
                }
                if (Schema::hasColumn('presensi', 'petugas_nama')) {
                    $table->dropColumn('petugas_nama');
                }
            });
        }

        if (Schema::hasTable('rekap_penerimaan_pkb')) {
            Schema::table('rekap_penerimaan_pkb', function (Blueprint $table) {
                if (Schema::hasColumn('rekap_penerimaan_pkb', 'input_by_petugas_id')) {
                    $table->dropColumn('input_by_petugas_id');
                }
                if (Schema::hasColumn('rekap_penerimaan_pkb', 'input_by_nama')) {
                    $table->dropColumn('input_by_nama');
                }
            });
        }
    }
};
