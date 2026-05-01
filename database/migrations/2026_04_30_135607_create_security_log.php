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
            $table->string('description'); // Akan diisi: SALAH PIN atau PINDAH LOKASI
            $table->enum('severity', ['warning', 'critical'])->default('warning');
            $table->timestamps();
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
