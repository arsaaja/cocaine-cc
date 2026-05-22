<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\SecurityLog;
use App\Models\Device;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * 1. Ambil Ringkasan untuk Dashboard (Halaman Web)
     */
    public function indexWeb()
    {
        // Saldo diambil dari snapshot terbaru di tabel transactions (Ledger Finansial Utama)
        $lastTransaction = Transaction::latest()->first();
        $totalSaldo = $lastTransaction ? $lastTransaction->balance_snapshot : 0;

        $deviceActive = Device::where('status', 'online')->count() ?? 0;

        // Ambil nominal dari tabel sensor_data untuk breakdown statistik koin/kertas
        $totalKoin = DB::table('sensor_data')->where('jenis_input', 'koin')->sum('nominal') ?? 0;
        $totalKertas = DB::table('sensor_data')->where('jenis_input', 'kertas')->sum('nominal') ?? 0;

        $logTransaksi = Transaction::latest()->limit(10)->get();
        $logKeamanan = SecurityLog::latest()->limit(10)->get();

        // Mengambil data target tabungan dari user yang sedang login agar saat di-refresh tidak kosong
        $user = Auth::user();
        $targetTitle = $user->target_title ?? '';
        $targetAmount = $user->target_amount ?? null;

        return view('dashboard', compact(
            'totalSaldo',
            'deviceActive',
            'totalKoin',
            'totalKertas',
            'logTransaksi',
            'logKeamanan',
            'targetTitle',
            'targetAmount'
        ));
    }

    /**
     * 2. API untuk Auto-Update Dashboard (Setiap 5 Detik)
     * Ditambahkan field target agar sinkronisasi progress bar berjalan real-time ketika ada transaksi masuk
     */
    public function getData()
    {
        $lastTx = Transaction::latest()->first();
        $user = Auth::user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_balance' => $lastTx ? $lastTx->balance_snapshot : 0,
                'target_amount' => $user ? $user->target_amount : null,
                'target_title' => $user ? $user->target_title : '',
                'breakdown' => [
                    'koin' => DB::table('sensor_data')->where('jenis_input', 'koin')->sum('nominal') ?? 0,
                    'kertas' => DB::table('sensor_data')->where('jenis_input', 'kertas')->sum('nominal') ?? 0,
                ]
            ]
        ]);
    }

    /**
     * Baru: 5. Menyimpan/Mengubah Target Tabungan secara Permanen di DB
     */
    public function saveTarget(Request $request)
    {
        $request->validate([
            'target_amount' => 'required|integer|min:1',
            'target_title' => 'nullable|string|max:255'
        ]);

        // Strict Financial & System Data Integrity dengan DB::transaction
        DB::transaction(function () use ($request) {
            $user = Auth::user();
            if ($user) {
                $user->target_title = $request->target_title ?? 'Rencana Saya';
                $user->target_amount = $request->target_amount;
                $user->save();
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Target tabungan berhasil disimpan ke database.'
        ]);
    }

    /**
     * Baru: 6. Menghapus Target Tabungan di DB
     */
    public function clearTarget()
    {
        DB::transaction(function () {
            $user = Auth::user();
            if ($user) {
                $user->target_title = null;
                $user->target_amount = null;
                $user->save();
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Target tabungan berhasil dihapus dari database.'
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