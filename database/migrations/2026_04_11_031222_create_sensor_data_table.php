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
    Schema::create('sensor_data', function (Blueprint $table) {
        $table->id();
        $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
        $table->string('jenis_input', 50); // 'koin' atau 'kertas'
        $table->integer('nominal');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
