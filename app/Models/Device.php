<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'device_name',
        'api_key',
        'status', // online/offline
        'last_seen'
    ];

    // Relasi ke data sensor
    public function sensorData()
    {
        return $this->hasMany(SensorData::class);
    }

    // Relasi ke antrean perintah
    public function commands()
    {
        return $this->hasMany(Command::class);
    }

    // Relasi ke log aktivitas
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}