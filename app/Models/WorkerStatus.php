<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerStatus extends Model
{
    protected $table = 'worker_status';

    protected $fillable = [
        'device_id',
        'worker_name',
        'status',
        'last_run'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}