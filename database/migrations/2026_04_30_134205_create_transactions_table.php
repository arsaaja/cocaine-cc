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
            $table->string('activity'); // Akan diisi: DEBIT, KREDIT, atau UANG TIDAK VALID
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_snapshot', 12, 2); // Saldo akhir setelah aksi
            $table->timestamps();
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
