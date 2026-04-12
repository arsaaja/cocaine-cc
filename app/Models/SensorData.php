<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $table = 'sensor_data';

    protected $fillable = [
        'device_id',
        'jenis_input',
        'nominal'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}