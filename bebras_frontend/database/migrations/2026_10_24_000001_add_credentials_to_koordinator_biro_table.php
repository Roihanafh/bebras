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
        if (Schema::hasTable('koordinator_biro')) {
            Schema::table('koordinator_biro', function (Blueprint $table) {
                if (!Schema::hasColumn('koordinator_biro', 'username')) {
                    $table->string('username')->nullable()->after('email');
                }
                if (!Schema::hasColumn('koordinator_biro', 'password')) {
                    $table->string('password')->nullable()->after('username');
                }
                if (!Schema::hasColumn('koordinator_biro', 'telepon')) {
                    $table->string('telepon')->nullable()->after('password');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('koordinator_biro')) {
            Schema::table('koordinator_biro', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('koordinator_biro', 'username')) {
                    $columns[] = 'username';
                }
                if (Schema::hasColumn('koordinator_biro', 'password')) {
                    $columns[] = 'password';
                }
                if (Schema::hasColumn('koordinator_biro', 'telepon')) {
                    $columns[] = 'telepon';
                }
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
