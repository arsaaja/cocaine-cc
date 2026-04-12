<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_log';

    protected $fillable = [
        'device_id',
        'action',
        'description'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}