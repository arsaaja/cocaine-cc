<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction; //
use App\Models\SecurityLog; //
use Carbon\Carbon;

class IngestController extends Controller
{
    private function getDeviceFromToken(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token)
            return null;
        return DB::table('devices')->where('api_key', $token)->first();
    }

    // Fungsi 1: Menerima Uang Masuk (/ingest)
    public function store(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device)
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

        $request->validate([
            'jenis_input' => 'required|string',
            'nominal' => 'required|integer'
        ]);

        return DB::transaction(function () use ($request, $device) {
            // 1. Tetap simpan ke sensor_data sebagai backup log mentah
            DB::table('sensor_data')->insert([
                'device_id' => $device->id,
                'jenis_input' => $request->jenis_input,
                'nominal' => $request->nominal,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // 2. Ambil saldo terakhir dari tabel transactions untuk kalkulasi balance_snapshot
            $lastTx = Transaction::latest()->first();
            $lastBalance = $lastTx ? $lastTx->balance_snapshot : 0;
            $newBalance = $lastBalance + $request->nominal;

            // 3. Simpan ke tabel TRANSACTIONS agar muncul di DASHBOARD
            Transaction::create([
                'activity' => "Setor Uang (" . $request->jenis_input . ")",
                'amount' => $request->nominal,
                'balance_snapshot' => $newBalance,
            ]);

            return response()->json(['status' => 'success', 'message' => 'Uang masuk & Riwayat diperbarui!']);
        });
    }

    public function storeGps(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device)
            return response()->json(['status' => 'error'], 401);

        // 1. Simpan data ke tabel gps_data (log mentah)
        DB::table('gps_data')->insert([
            'device_id' => $device->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'speed' => $request->speed,
            'created_at' => now(),
        ]);

        // 2. Logika Deteksi Perpindahan (Contoh sederhana: jika kecepatan > 5 km/jam)
        // Atau bandingkan dengan koordinat sebelumnya
        if ($request->speed > 5) {
            SecurityLog::create([
                'description' => "Peringatan: Celengan terdeteksi berpindah tempat! (Kecepatan: {$request->speed} km/h)",
                'severity' => 'critical'
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    // Fungsi 2: Menerima Sinyal Pembobolan (/alert)
    public function alert(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device)
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

        $request->validate([
            'action' => 'required|string',
            'description' => 'required|string'
        ]);

        // Simpan ke SECURITY_LOGS sesuai struktur baru untuk UI
        SecurityLog::create([
            'description' => "[" . $request->action . "] " . $request->description,
            'severity' => ($request->action == 'Pencurian') ? 'critical' : 'warning',
        ]);

        return response()->json(['status' => 'success', 'message' => 'Log keamanan dicatat!']);
    }

}