<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\SecurityLog;
use App\Models\Device;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * 1. Ambil Ringkasan untuk Dashboard (Halaman Web)
     */
    public function indexWeb()
    {
        // Saldo diambil dari snapshot terbaru di tabel transactions
        $lastTransaction = Transaction::latest()->first();
        $totalSaldo = $lastTransaction ? $lastTransaction->balance_snapshot : 0;

        $deviceActive = Device::where('status', 'online')->count() ?? 0;

        // PERBAIKAN: Ambil nominal dari tabel sensor_data agar bisa dibedakan koin/kertas
        $totalKoin = DB::table('sensor_data')->where('jenis_input', 'koin')->sum('nominal') ?? 0;
        $totalKertas = DB::table('sensor_data')->where('jenis_input', 'kertas')->sum('nominal') ?? 0;

        $logTransaksi = Transaction::latest()->limit(10)->get();
        $logKeamanan = SecurityLog::latest()->limit(10)->get();

        return view('dashboard', compact('totalSaldo', 'deviceActive', 'totalKoin', 'totalKertas', 'logTransaksi', 'logKeamanan'));
    }

    /**
     * 2. API untuk Auto-Update Dashboard (Setiap 5 Detik)
     * Mengikuti struktur JSON di JavaScript: data.total_balance & data.breakdown
     */
    public function getData()
    {
        $lastTx = Transaction::latest()->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_balance' => $lastTx ? $lastTx->balance_snapshot : 0,
                'breakdown' => [
                    // Ambil dari sensor_data agar sinkron dengan jenis_input dari IngestController
                    'koin' => DB::table('sensor_data')->where('jenis_input', 'koin')->sum('nominal') ?? 0,
                    'kertas' => DB::table('sensor_data')->where('jenis_input', 'kertas')->sum('nominal') ?? 0,
                ]
            ]
        ]);
    }

    /**
     * 3. Ambil Riwayat untuk API
     */
    public function logs()
    {
        $transactions = Transaction::latest()->limit(10)->get()->map(function ($t) {
            return [
                'waktu' => $t->created_at->format('d M Y H:i'),
                'aktivitas' => $t->activity,
                'nominal' => number_format($t->amount, 0, ',', '.'),
                'saldo_akhir' => number_format($t->balance_snapshot, 0, ',', '.')
            ];
        });

        $security = SecurityLog::latest()->limit(10)->get()->map(function ($s) {
            return [
                'waktu' => $s->created_at->format('d M Y H:i'),
                'aktivitas' => $s->description,
                'severity' => $s->severity
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'transactions' => $transactions,
                'security' => $security
            ]
        ]);
    }

    /**
     * 4. Data Grafik Tabungan
     */
    public function chartData()
    {
        $chartData = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('MAX(balance_snapshot) as total')
        )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $chartData
        ]);
    }
}