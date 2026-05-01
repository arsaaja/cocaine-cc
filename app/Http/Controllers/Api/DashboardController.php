<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction; // Tambahkan ini
use App\Models\SecurityLog; // Tambahkan ini
use App\Models\Device;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * 1. Ambil Ringkasan Saldo & Riwayat untuk Dashboard
     */
    public function indexWeb()
    {
        // Saldo diambil dari transaksi terakhir (Snapshot saldo terbaru)
        $lastTransaction = Transaction::latest()->first();
        $totalSaldo = $lastTransaction ? $lastTransaction->balance_snapshot : 0;

        $deviceActive = Device::where('status', 'online')->count() ?? 0;

        // Filter nominal berdasarkan jenis aktivitas (DEBIT)
        $totalKoin = Transaction::where('activity', 'DEBIT')->sum('amount') ?? 0;
        $totalKertas = 0; // Sesuaikan jika ada logika pembeda koin/kertas di tabel transaksi

        // Ambil Data untuk Tabel Riwayat di Dashboard
        $logTransaksi = Transaction::latest()->limit(10)->get();
        $logKeamanan = SecurityLog::latest()->limit(10)->get();

        return view('dashboard', compact('totalSaldo', 'deviceActive', 'totalKoin', 'totalKertas', 'logTransaksi', 'logKeamanan'));
    }

    /**
     * 2. Ambil Riwayat untuk API (Halaman Riwayat image_670660.png)
     * GET /api/dashboard/log
     */
    public function logs()
    {
        // Ambil data dari tabel TRANSACTIONS
        $transactions = Transaction::latest()->limit(10)->get()->map(function ($t) {
            return [
                'waktu' => $t->created_at->format('d M Y H:i'),
                'aktivitas' => $t->activity,
                'nominal' => number_format($t->amount, 0, ',', '.'),
                'saldo_akhir' => number_format($t->balance_snapshot, 0, ',', '.')
            ];
        });

        // Ambil data dari tabel SECURITY_LOGS
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
     * 3. Data Grafik Tabungan
     * GET /api/dashboard/chart
     */
    public function chartData()
    {
        // Mengambil pertumbuhan saldo berdasarkan balance_snapshot terakhir setiap hari
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