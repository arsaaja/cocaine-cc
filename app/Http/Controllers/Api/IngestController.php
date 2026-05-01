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
        // Mendukung Bearer Token atau API Key di body untuk kemudahan ESP32
        $token = $request->bearerToken() ?: $request->input('api_key');
        if (!$token)
            return null;
        return DB::table('devices')->where('api_key', $token)->first();
    }

    /**
     * Fungsi 1: Menerima Transaksi Keuangan (/ingest)
     * Opsi status_transaksi: debit, kredit, uang tidak valid
     */
    public function store(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device)
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

        $request->validate([
            'status_transaksi' => 'required|string', // debit / kredit / uang tidak valid
            'nominal' => 'required|integer'
        ]);

        return DB::transaction(function () use ($request, $device) {
            // 1. Log mentah ke sensor_data
            DB::table('sensor_data')->insert([
                'device_id' => $device->id,
                'jenis_input' => $request->status_transaksi,
                'nominal' => $request->nominal,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // 2. Kalkulasi Saldo Otomatis
            $lastTx = Transaction::latest()->first();
            $currentBalance = $lastTx ? $lastTx->balance_snapshot : 0;

            $status = strtolower($request->status_transaksi);
            $newBalance = $currentBalance;

            if ($status === 'debit') {
                $newBalance += $request->nominal;
            } elseif ($status === 'kredit') {
                $newBalance -= $request->nominal;
            }
            // Jika 'uang tidak valid', newBalance tetap (tidak berubah)

            // 3. Simpan ke Tabel TRANSACTIONS (Muncul di Log Transaksi Dashboard)
            Transaction::create([
                'activity' => strtoupper($status),
                'amount' => $request->nominal,
                'balance_snapshot' => $newBalance,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi ' . $status . ' berhasil dicatat',
                'current_balance' => $newBalance
            ]);
        });
    }

    /**
     * Fungsi 2: Menerima Data GPS
     */
    public function storeGps(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device)
            return response()->json(['status' => 'error'], 401);

        // Simpan data GPS mentah
        DB::table('gps_data')->insert([
            'device_id' => $device->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'speed' => $request->speed,
            'created_at' => Carbon::now(),
        ]);

        // Cek anomali pindah lokasi berdasarkan kecepatan (Threshold: 5 km/jam)
        if ($request->speed > 5) {
            SecurityLog::create([
                'description' => "ANOMALI: PINDAH LOKASI (Kecepatan: {$request->speed} km/h)",
                'severity' => 'critical'
            ]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Fungsi 3: Menerima Anomali / Alert (/alert)
     * Opsi jenis_anomali: salah pin, pindah lokasi
     */
    public function alert(Request $request)
    {
        $device = $this->getDeviceFromToken($request);
        if (!$device)
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);

        $request->validate([
            'jenis_anomali' => 'required|string', // salah pin / pindah lokasi
            'description' => 'nullable|string'
        ]);

        $anomali = strtolower($request->jenis_anomali);

        // Simpan ke SECURITY_LOGS (Muncul di Log Keamanan Dashboard)
        SecurityLog::create([
            'description' => "ANOMALI: " . strtoupper($anomali) . " | " . ($request->description ?? 'Terdeteksi oleh sensor'),
            'severity' => ($anomali === 'pindah lokasi') ? 'critical' : 'warning',
        ]);

        return response()->json(['status' => 'success', 'message' => 'Anomali berhasil dicatat!']);
    }
}