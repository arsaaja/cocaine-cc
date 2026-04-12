<?php

namespace App\Services;

use App\Models\Device;
use App\Models\SensorData;
use Illuminate\Support\Facades\DB;

class SensorService
{
    public function storeData(array $validatedData)
    {
        return DB::transaction(function () use ($validatedData) {
            $device = Device::where('api_key', $validatedData['api_key'])->first();

            // 1. Catat data sensor
            $data = SensorData::create([
                'device_id' => $device->id,
                'jenis_input' => $validatedData['jenis_input'],
                'nominal' => $validatedData['nominal'],
            ]);

            // 2. Update status terakhir device (Heartbeat)
            $device->update(['last_seen' => now(), 'status' => 'online']);

            return $data;
        });
    }
}