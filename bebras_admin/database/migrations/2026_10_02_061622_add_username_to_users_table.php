<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Kolom username mungkin sudah ada (jika migrasi create_users_table
     * dijalankan fresh), atau belum ada (jika DB dibuat sebelum kolom ini
     * ditambahkan ke create_users_table). Migration ini idempotent.
     */
    public function up(): void
    {
        // Hanya tambah kolom jika belum ada
        if (!Schema::hasColumn('users', 'username')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable()->after('name');
            });

            // Isi nilai default untuk baris yang sudah ada
            DB::statement("UPDATE users SET username = LOWER(REPLACE(name, ' ', '_')) WHERE username IS NULL OR username = ''");

            Schema::table('users', function (Blueprint $table) {
                $table->string('username')->nullable(false)->unique()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * Hanya drop jika kolom ini ada DAN kolom tersebut tidak didefinisikan
     * di migrasi create_users_table (yaitu, kita yang menambahkannya).
     * Karena kita tidak bisa mengetahui hal itu secara pasti, kita skip
     * down() agar aman — fresh migrate akan menangani full reset.
     */
    public function down(): void
    {
        // Sengaja kosong — kolom bisa saja sudah ada di create_users_table.
        // Gunakan `php artisan migrate:fresh` untuk reset penuh.
    }
};
