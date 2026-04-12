<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\WorkerStatus;

class WorkerController extends Controller
{
    public function update(Request $request)
    {
        // Debugging: Cek apakah api_key masuk
        if (!$request->has('api_key')) {
            return response()->json(['message' => 'API Key missing'], 400);
        }

        $device = Device::where('api_key', $request->api_key)->first();

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        // Gunakan updateOrCreate agar lebih simpel
        WorkerStatus::updateOrCreate(
            ['device_id' => $device->id, 'worker_name' => $request->worker_name],
            ['status' => $request->status, 'last_run' => now()]
        );

        return response()->json(['status' => 'success', 'message' => 'Worker updated']);
    }
}