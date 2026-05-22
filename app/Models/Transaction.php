<?php

namespace App\Http\Controllers\Api; // sesuaikan jika namespace Anda App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    // Tambahkan 'user_id' ke dalam fillable untuk aspek finansial auditing multi-user
    protected $fillable = [
        'user_id', // PENTING: Untuk mencatat transaksi ini milik siapa
        'activity',
        'amount',
        'balance_snapshot'
    ];

    /**
     * Relasi balik ke User (Setiap transaksi dimiliki oleh satu User tertentu)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}