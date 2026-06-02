<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\SecurityLog;
use Carbon\Carbon;

class IngestController extends Controller
{
    private function getDeviceFromToken(Request $request)
    {
        // Standar API: Mengambil token murni dari Authorization Header (Bearer Token)
        $token = $request->bearerToken();

        // JIKA ingin tetap mendukung fallback input api_key dari body (opsional), gunakan baris di bawah:
        // $token = $request->bearerToken() ?: $request->input('api_key');

        if (!$token) {
            return null;
        }

        // Mengambil data dari tabel devices berdasarkan api_key yang dikirim
        return DB::table('devices')->first();
    }

    public function store(Request $request)
    {
        $device = $this->getDeviceFromToken($request);

        if (!$device) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized Device'], 401);
        }

        // PERBAIKAN: Mencari user berdasarkan 'user_id' yang berada di dalam tabel 'devices'
        $user = DB::table('users')->where('id', $device->user_id)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Device not linked to any user'], 404);
        }

        $request->validate([
            'jenis_input' => 'required|string',
            'nominal' => 'nullable|integer'
        ]);

        return DB::transaction(function () use ($request, $device, $user) {
            $nominal = $request->nominal;
            $jenisUang = strtolower($request->jenis_input);

            if (empty($nominal) || $nominal <= 0) {
                $status = 'UANG TIDAK VALID';
                $amountToRecord = 0;
            } else {
                $status = 'DEBIT';
                $amountToRecord = $nominal;
            }

            // 1. Logika Web Dashboard breakdown boxes: Simpan data mentah ke sensor_data
            DB::table('sensor_data')->insert([
                'device_id' => $device->id,
                'jenis_input' => $jenisUang,
                'nominal' => $amountToRecord,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // 2. Logika Financial Ledger Audit: Ambil snapshot saldo terakhir khusus milik user ini
            $lastTx = Transaction::where('user_id', $user->id)->latest()->first();
            $currentBalance = $lastTx ? $lastTx->balance_snapshot : 0;

            $newBalance = ($status === 'DEBIT') ? ($currentBalance + $amountToRecord) : $currentBalance;

            // Simpan ke tabel transactions dengan menyertakan user_id pemilik celengan
            Transaction::create([
                'user_id' => $user->id,
                'activity' => $status,
                'amount' => $amountToRecord,
                'balance_snapshot' => $newBalance,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data ' . $jenisUang . ' (' . $status . ') berhasil dicatat',
                'current_balance' => $newBalance
            ]);
        });
    }

    public function storeGps(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device)
            return response()->json(['status' => 'error'], 401);

        DB::table('gps_data')->insert([
            'device_id' => $device->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'speed' => $request->speed,
            'created_at' => Carbon::now(),
        ]);

        if ($request->speed > 5) {
            SecurityLog::create([
                'description' => "ANOMALI: PINDAH LOKASI (Kecepatan: {$request->speed} km/h)",
                'severity' => 'critical'
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    public function alert(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device)
            return response()->json(['status' => 'error'], 401);

        $request->validate([
            'jenis_anomali' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $anomali = strtolower($request->jenis_anomali);

        SecurityLog::create([
            'description' => "ANOMALI: " . strtoupper($anomali) . " | " . ($request->description ?? 'Terdeteksi sensor'),
            'severity' => ($anomali === 'pindah lokasi') ? 'critical' : 'warning',
        ]);

        return response()->json(['status' => 'success']);
    }
}