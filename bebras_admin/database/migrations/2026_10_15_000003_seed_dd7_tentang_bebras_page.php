<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('tentang_bebras')) {
            $exists = DB::table('tentang_bebras')->where('slug', 'dd_7')->orWhere('template', 'dd_7')->exists();
            if (!$exists) {
                DB::table('tentang_bebras')->insert([
                    'slug'       => 'dd_7',
                    'judul'      => 'Koordinator Bebras Biro',
                    'konten'     => '<p>Berikut adalah daftar Bebras Biro dan koordinatornya masing-masing.</p>',
                    'urutan'     => 7,
                    'template'   => 'dd_7',
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
        if (Schema::hasTable('tentang_bebras')) {
            DB::table('tentang_bebras')->where('slug', 'dd_7')->delete();
        }
    }
};
