<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SensorController extends Controller
{
    // Menerima data dari ESP32
    public function receiveData(Request $request)
    {
        // Simpan data sensor ke log atau cache agar bisa dilihat di web
        // Untuk sementara kita simpan di cache agar cepat
        Cache::put('last_sensor_data', $request->all(), 600);

        // Ambil status relay yang diinginkan user dari web (default 0/mati)
        $relayStatus = Cache::get('relay_status', 0);

        // Balas ke ESP32 dengan status relay
        return response()->json([
            'status' => 'success',
            'relay' => (int)$relayStatus 
        ]);
    }

    // Mengubah status relay dari tombol di Web
    // Toggle relay — update status di relay_controls
    public function toggleRelay(Request $request)
    {
        $status = $request->input('status');

        DB::table('relay_controls')
            ->orderBy('updated_at', 'desc')
            ->limit(1)
            ->update(['status' => $status, 'updated_at' => now()]);

        return response()->json(['status' => 'success', 'relay' => $status]);
    }

    // Ambil data terbaru untuk ditampilkan di UI Web
    public function getLatestData()
    {
        return response()->json([
            'sensor' => Cache::get('last_sensor_data'),
            'relay' => Cache::get('relay_status', 0)
        ]);
    }
}