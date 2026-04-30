<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    // Kolom yang boleh diisi sesuai UI Dashboard
    protected $fillable = [
        'activity',
        'amount',
        'balance_snapshot'
    ];
}