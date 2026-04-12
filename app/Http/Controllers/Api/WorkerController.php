<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkerStatus;
use App\Models\Device;

class WorkerController extends Controller
{
    public function update(Request $request)
    {
        // Validasi simpel
        $request->validate([
            'api_key' => 'required|exists:devices,api_key',
            'worker_name' => 'required|string',
            'status' => 'required|string'
        ]);

        $device = Device::where('api_key', $request->api_key)->first();

        // Update atau buat status worker baru
        $worker = WorkerStatus::updateOrCreate(
            ['worker_name' => $request->worker_name],
            [
                'device_id' => $device->id,
                'last_run' => now(),
                'status' => $request->status
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Worker heartbeat received'
        ]);
    }
}