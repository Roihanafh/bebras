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
        if (!Schema::hasTable('anggota_biro')) {
            Schema::create('anggota_biro', function (Blueprint $table) {
                $table->id();
                $table->foreignId('biro_id')->constrained('users')->onDelete('cascade');
                $table->string('nama');
                $table->integer('tahun_masuk');
                $table->date('tanggal_lahir');
                $table->string('no_anggota', 3);
                $table->enum('status_validasi', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();

                $table->unique(['biro_id', 'no_anggota']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_biro');
    }
};
