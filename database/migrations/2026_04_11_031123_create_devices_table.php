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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            // Menghubungkan perangkat ke user pemiliknya
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('device_name');
            $table->string('api_key', 100)->unique();
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->timestamp('last_seen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};