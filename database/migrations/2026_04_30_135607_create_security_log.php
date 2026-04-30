<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Langsung buat dengan nama security_logs
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->text('description'); // Untuk kolom "Aktivitas" di UI
            $table->enum('severity', ['warning', 'critical'])->default('warning');
            $table->timestamps(); // Untuk kolom "Waktu" di UI
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_log');
    }
};
