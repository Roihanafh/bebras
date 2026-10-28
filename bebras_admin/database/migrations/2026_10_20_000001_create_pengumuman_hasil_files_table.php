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
        Schema::create('pengumuman_hasil_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_kegiatan_id')
                ->constrained('menu_kegiatan')
                ->onDelete('cascade');
            $table->enum('kategori', ['sikecil', 'siaga', 'penggalang', 'penegak']);
            $table->string('file_path');
            $table->string('original_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();

            $table->unique(['menu_kegiatan_id', 'kategori']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman_hasil_files');
    }
};
