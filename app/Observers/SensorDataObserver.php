<?php

namespace App\Observers;

use App\Models\SensorData;
use App\Models\Transaction;
class SensorDataObserver
{
    /**
     * Handle the SensorData "created" event.
     */
    public function created(SensorData $sensorData): void
    {
        // Hanya proses jika jenis_input adalah koin
        if ($sensorData->jenis_input === 'koin') {

            // 1. Hitung Saldo Terakhir
            $lastTransaction = Transaction::latest()->first();
            $currentBalance = $lastTransaction ? $lastTransaction->balance_snapshot : 0;

            // 2. Tambahkan nominal koin baru ke saldo
            $newBalance = $currentBalance + $sensorData->nominal;

            // 3. Masukkan ke tabel Transactions otomatis
            Transaction::create([
                'activity' => 'DEBIT',
                'amount' => $sensorData->nominal,
                'balance_snapshot' => $newBalance,
                'created_at' => $sensorData->created_at,
            ]);
        }
    }

    /**
     * Handle the SensorData "updated" event.
     */
    public function updated(SensorData $sensorData): void
    {
        //
    }

    /**
     * Handle the SensorData "deleted" event.
     */
    public function deleted(SensorData $sensorData): void
    {
        //
    }

    /**
     * Handle the SensorData "restored" event.
     */
    public function restored(SensorData $sensorData): void
    {
        //
    }

    /**
     * Handle the SensorData "force deleted" event.
     */
    public function forceDeleted(SensorData $sensorData): void
    {
        //
    }
}
