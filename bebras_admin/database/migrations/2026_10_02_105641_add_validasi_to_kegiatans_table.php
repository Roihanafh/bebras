<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            $table->enum('status_validasi', ['pending', 'approved', 'rejected'])
                ->default('pending')
                ->after('urutan');

            $table->unsignedBigInteger('dibuat_oleh')
                ->nullable()
                ->after('status_validasi');

            $table->unsignedBigInteger('divalidasi_oleh')
                ->nullable()
                ->after('dibuat_oleh');

            $table->timestamp('divalidasi_pada')
                ->nullable()
                ->after('divalidasi_oleh');

            $table->text('catatan_validasi')
                ->nullable()
                ->after('divalidasi_pada');

            $table->foreign('dibuat_oleh')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('divalidasi_oleh')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        // Pastikan semua baris lama mendapat status 'approved' agar tetap tampil di frontend
        DB::statement("UPDATE kegiatans SET status_validasi = 'approved' WHERE status_validasi IS NULL OR status_validasi = ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kegiatans', function (Blueprint $table) {
            // Drop foreign key constraints sebelum drop kolom
            $table->dropForeign(['dibuat_oleh']);
            $table->dropForeign(['divalidasi_oleh']);

            // Drop kelima kolom
            $table->dropColumn([
                'status_validasi',
                'dibuat_oleh',
                'divalidasi_oleh',
                'divalidasi_pada',
                'catatan_validasi',
            ]);
        });
    }
};
