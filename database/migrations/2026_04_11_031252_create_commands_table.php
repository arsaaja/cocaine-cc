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
    Schema::create('commands', function (Blueprint $table) {
        $table->id();
        $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
        $table->string('command_type', 100); // 'ALARM_ON', 'LOCK_DOOR'
        $table->enum('status', ['pending', 'processing', 'completed'])->default('pending');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commands');
    }
};
