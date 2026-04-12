<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    protected $fillable = [
        'device_id',
        'command_type',
        'status' // pending, processing, completed, failed
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}