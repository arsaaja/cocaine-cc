<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    // Kolom sesuai tabel security_logs hasil refaktor
    protected $fillable = [
        'description',
        'severity'
    ];
}