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
        Schema::create('gambar_berita', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kegiatan_id');
            $table->string('gambar');
            $table->integer('urutan')->default(0);
            $table->string('caption')->nullable();
            $table->timestamps();

            $table->foreign('kegiatan_id')
                ->references('id')
                ->on('kegiatans')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gambar_berita');
    }
};
