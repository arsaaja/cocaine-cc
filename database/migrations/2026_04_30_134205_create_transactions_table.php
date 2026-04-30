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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('activity');          // Deskripsi aktivitas
            $table->decimal('amount', 12, 2);    // Nominal transaksi
            $table->decimal('balance_snapshot', 12, 2); // Saldo akhir setelah transaksi
            $table->timestamps();                // Kolom Waktu
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
