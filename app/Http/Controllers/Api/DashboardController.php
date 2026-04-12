<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorData;
use App\Models\Device;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * 1. Ambil Ringkasan Saldo & Status Device
     * GET /api/dashboard/data
     */
    public function index()
    {
        // Menghitung total saldo dari semua device (atau bisa difilter per user nanti)
        $totalSaldo = SensorData::sum('nominal');
        $deviceActive = Device::where('status', 'online')->count();
        $totalKoin = SensorData::where('jenis_input', 'koin')->sum('nominal');
        $totalKertas = SensorData::where('jenis_input', 'kertas')->sum('nominal');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_balance' => $totalSaldo,
                'active_devices' => $deviceActive,
                'breakdown' => [
                    'koin' => $totalKoin,
                    'kertas' => $totalKertas
                ]
            ]
        ]);
    }

    /**
     * 2. Ambil Log Aktivitas Terbaru
     * GET /api/dashboard/log
     */
    public function logs()
    {
        $logs = ActivityLog::with('device')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'device' => $log->device->device_name ?? 'Unknown',
                    'action' => $log->action,
                    'description' => $log->description,
                    'time' => $log->created_at->diffForHumans()
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $logs
        ]);
    }

    /**
     * 3. Data Grafik Tabungan
     * GET /api/dashboard/chart/{device}/{type}
     */
    public function chartData($deviceId, $type)
    {
        // Mengambil data tabungan 7 hari terakhir
        $chartData = SensorData::where('device_id', $deviceId)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(nominal) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json([
            'status' => 'success',
            'device_id' => $deviceId,
            'period' => '7_days',
            'data' => $chartData
        ]);
    }
}