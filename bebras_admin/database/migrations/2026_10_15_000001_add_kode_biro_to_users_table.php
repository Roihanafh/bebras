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
        if (!Schema::hasColumn('users', 'kode_biro')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('kode_biro', 3)->nullable()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'kode_biro')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('kode_biro');
            });
        }
    }
};
