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
    Schema::create('activity_log', function (Blueprint $table) {
        $table->id();
        $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
        $table->string('action', 100); // 'WRONG_PIN', 'GPS_MOVED'
        $table->text('description')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
