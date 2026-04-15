<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeviceController extends Controller
{
    /**
     * Bantuan internal untuk ngecek API Key dari Header
     */
    private function getDeviceFromToken(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) return null;
        
        // Cari device berdasarkan api_key
        return DB::table('devices')->where('api_key', $token)->first();
    }

    /**
     * 1. Menerima Data Uang Masuk dari ESP32/Python
     * POST /api/ingest
     */
    public function ingest(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized. API Key tidak valid.'], 401);
        }

        // Validasi input dari ESP32
        $request->validate([
            'jenis_input' => 'required|string',
            'nominal' => 'required|integer'
        ]);

        // Simpan data permanen ke Database (BUKAN CACHE!)
        DB::table('sensor_data')->insert([
            'device_id'   => $device->id,
            'jenis_input' => $request->jenis_input,
            'nominal'     => $request->nominal,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        // Update status device jadi online
        DB::table('devices')->where('id', $device->id)->update([
            'status' => 'online',
            'last_seen' => Carbon::now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Data tabungan berhasil dicatat!'
        ]);
    }

    /**
     * 2. Menerima Sinyal Pembobolan (Salah PIN / GPS Pindah)
     * POST /api/alert
     */
    public function alert(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        // Simpan log ke tabel activity_log
        DB::table('activity_log')->insert([
            'device_id'   => $device->id,
            'action'      => $request->action, // contoh: 'WRONG_PIN_ATTEMPT'
            'description' => $request->description,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Peringatan keamanan telah dicatat ke sistem!'
        ]);
    }

    /**
     * 3. Ambil Summary untuk Dashboard Web (AJAX)
     * GET /api/dashboard/summary
     */
    public function dashboardSummary()
    {
        // Hitung total saldo tabungan dari semua uang masuk
        $totalTabungan = DB::table('sensor_data')->sum('nominal');
        
        // Ambil 5 aktivitas log terakhir (misal ada peringatan)
        $logTerbaru = DB::table('activity_log')->orderBy('created_at', 'desc')->limit(5)->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_saldo' => $totalTabungan,
                'log_terbaru' => $logTerbaru
            ]
        ]);
    }
}