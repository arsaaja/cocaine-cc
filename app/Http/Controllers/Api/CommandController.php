<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Command;
use App\Models\Device;
use Illuminate\Support\Facades\Validator;

class CommandController extends Controller
{
    /**
     * 1. Dashboard mengirim perintah (misal: Buka Solenoid)
     * POST /api/command/send
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'api_key' => 'required|exists:devices,api_key',
            'command_type' => 'required|string', // Contoh: 'open_door', 'reset_pin', 'alarm_off'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $device = Device::where('api_key', $request->api_key)->first();

        $command = Command::create([
            'device_id' => $device->id,
            'command_type' => $request->command_type,
            'status' => 'pending' // Default status
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Perintah berhasil dijadwalkan',
            'data' => $command
        ]);
    }

    /**
     * 2. ESP32 mengambil perintah yang berstatus 'pending'
     * GET /api/command/get?api_key=XYZ
     */
    public function getPending(Request $request)
    {
        $request->validate([
            'api_key' => 'required|exists:devices,api_key'
        ]);

        $device = Device::where('api_key', $request->api_key)->first();

        // Ambil 1 perintah paling lama (FIFO) yang belum dikerjakan
        $command = Command::where('device_id', $device->id)
            ->where('status', 'pending')
            ->oldest()
            ->first();

        if (!$command) {
            return response()->json(['status' => 'none', 'message' => 'No pending commands']);
        }

        // Update status ke 'processing' agar tidak diambil berkali-kali
        $command->update(['status' => 'processing']);

        return response()->json([
            'status' => 'success',
            'command_id' => $command->id,
            'action' => $command->command_type
        ]);
    }

    /**
     * 3. ESP32 mengonfirmasi perintah selesai dijalankan
     * POST /api/command/update
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'command_id' => 'required|exists:commands,id',
            'status' => 'required|in:completed,failed'
        ]);

        $command = Command::find($request->command_id);
        $command->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status perintah diperbarui ke ' . $request->status
        ]);
    }
}