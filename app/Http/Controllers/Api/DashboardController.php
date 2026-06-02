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
        $lastTransaction = Transaction::latest()->first();
        $totalSaldo = $lastTransaction ? $lastTransaction->balance_snapshot : 0;

        $deviceActive = Device::where('status', 'online')->count() ?? 0;

        $totalKoin = DB::table('sensor_data')->where('jenis_input', 'koin')->sum('nominal') ?? 0;
        $totalKertas = DB::table('sensor_data')->where('jenis_input', 'kertas')->sum('nominal') ?? 0;

        $logTransaksi = Transaction::latest()->limit(10)->get();
        $logKeamanan = SecurityLog::latest()->limit(10)->get();

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
     */
    public function getData()
    {
        $user = Auth::user();

        // 1. Ambil snapshot saldo terakhir
        $lastTx = Transaction::latest()->first();

        // 2. Ambil SEMUA riwayat transaksi, ambil id terbesar per hari
        $chartTransactions = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('MAX(id) as max_id')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->map(function ($row) {
                $tx = Transaction::find($row->max_id);
                return (object)[
                    'date'        => $row->date,
                    'max_balance' => $tx ? $tx->balance_snapshot : 0,
                ];
            });

        $chartLabels = [];
        $chartDataValues = [];

        // 3. Kalau transaksinya kosong
        if ($chartTransactions->isEmpty()) {
            $chartLabels = [now()->translatedFormat('D, d M')];
            $chartDataValues = [0];
        } else {
            // 4. Masukin data asli dari database
            foreach ($chartTransactions as $tx) {
                $chartLabels[] = \Carbon\Carbon::parse($tx->date)->translatedFormat('D, d M');
                $chartDataValues[] = (int) $tx->max_balance;
            }
        }

        // 5. FIX CHART.JS ERROR: Selipin hari kemarin kalau data baru 1
        if (count($chartDataValues) == 1) {
            $kemarin = \Carbon\Carbon::parse($chartTransactions->first()->date)->subDay()->translatedFormat('D, d M');
            array_unshift($chartLabels, $kemarin);
            array_unshift($chartDataValues, 0);
        }

        // 6. Kalau saldo terakhir 0, kosongkan chart
        if (($lastTx ? $lastTx->balance_snapshot : 0) == 0) {
            $chartLabels = [];
            $chartDataValues = [];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_balance' => $lastTx ? $lastTx->balance_snapshot : 0,
                'target_amount' => $user ? $user->target_amount : null,
                'target_title'  => $user ? $user->target_title : '',
                'breakdown' => [
                    'koin'   => DB::table('sensor_data')->where('jenis_input', 'koin')->sum('nominal') ?? 0,
                    'kertas' => DB::table('sensor_data')->where('jenis_input', 'kertas')->sum('nominal') ?? 0,
                ],
                'chart_labels' => $chartLabels,
                'chart_data'   => $chartDataValues,
            ]
        ]);
    }

    /**
     * 3. Menghapus Target Tabungan di DB
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
     * 4. Ambil Riwayat untuk API
     */
    public function logs()
    {
        $transactions = Transaction::latest()->limit(10)->get()->map(function ($t) {
            return [
                'waktu'       => $t->created_at->format('d M Y H:i'),
                'aktivitas'   => $t->activity,
                'nominal'     => number_format($t->amount, 0, ',', '.'),
                'saldo_akhir' => number_format($t->balance_snapshot, 0, ',', '.')
            ];
        });

        $security = SecurityLog::latest()->limit(10)->get()->map(function ($s) {
            return [
                'waktu'     => $s->created_at->format('d M Y H:i'),
                'aktivitas' => $s->description,
                'severity'  => $s->severity
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'transactions' => $transactions,
                'security'     => $security
            ]
        ]);
    }

    /**
     * 5. Data Grafik Tabungan
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
            'data'   => $chartData
        ]);
    }

    /**
     * 6. Reset Saldo
     */
    public function resetSaldo()
    {
        $userId = Auth::id();

        $saldoSekarang = DB::table('transactions')->where('user_id', $userId)->sum('amount');

        if ($saldoSekarang > 0) {
            DB::table('transactions')->insert([
                'user_id'          => $userId,
                'activity'         => 'RESET SALDO',
                'amount'           => -$saldoSekarang,
                'balance_snapshot' => 0,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        $deviceIds = DB::table('devices')->where('user_id', $userId)->pluck('id');

        if ($deviceIds->isNotEmpty()) {
            DB::table('sensor_data')->whereIn('device_id', $deviceIds)->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Total Saldo, Koin, dan Kertas berhasil di-reset!'
        ]);
    }
}