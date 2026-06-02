<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek apakah kolom 'target_title' belum ada
            if (!Schema::hasColumn('users', 'target_title')) {
                $table->string('target_title')->nullable();
            }

            // Cek apakah kolom 'target_amount' belum ada
            if (!Schema::hasColumn('users', 'target_amount')) {
                $table->integer('target_amount')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kolom jika migrasi di-rollback
            if (Schema::hasColumn('users', 'target_title')) {
                $table->dropColumn('target_title');
            }

            if (Schema::hasColumn('users', 'target_amount')) {
                $table->dropColumn('target_amount');
            }
        });
    }
};