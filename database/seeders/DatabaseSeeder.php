<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Tabel Users (Sesuai Dashboard image_66099b.png)
        DB::table('users')->updateOrInsert(['id' => 1], [
            'username' => 'Budi',
            'name' => 'Budi Gaming',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password123'),
            'created_at' => '2026-04-28 04:18:32',
            'updated_at' => '2026-04-28 04:18:32',
        ]);

        // 2. Seed Tabel Devices (ESP32 DOIT V1 DevKit)
        DB::table('devices')->updateOrInsert(['id' => 3], [
            'device_name' => 'ESP32_Celengan_01',
            'api_key' => 'api_cocaine_01',
            'status' => 'online',
            'created_at' => '2026-04-21 06:26:42',
            'updated_at' => '2026-04-21 06:26:42',
        ]);

        // 3. Seed Tabel Transactions (Log Transaksi Riwayat)
        // Kita simulasikan saldo awal Rp 114.000 sesuai image_66099b.png
        $transactions = [
            [
                'activity' => 'DEBIT',
                'amount' => 100000,
                'balance_snapshot' => 100000,
                'created_at' => Carbon::now()->subDays(2)
            ],
            [
                'activity' => 'DEBIT',
                'amount' => 14000,
                'balance_snapshot' => 114000,
                'created_at' => Carbon::now()->subDays(1)
            ],
            [
                'activity' => 'UANG TIDAK VALID',
                'amount' => 0,
                'balance_snapshot' => 114000,
                'created_at' => Carbon::now()->subHours(5)
            ],
            [
                'activity' => 'KREDIT',
                'amount' => 5000,
                'balance_snapshot' => 109000,
                'created_at' => Carbon::now()->subHours(2)
            ],
        ];

        DB::table('transactions')->insert($transactions);

        // 4. Seed Tabel Security Logs (Log Keamanan Riwayat)
        DB::table('security_logs')->insert([
            [
                'description' => 'ANOMALI: SALAH PIN | Upaya akses gagal di panel fisik',
                'severity' => 'warning',
                'created_at' => Carbon::now()->subHours(10),
            ],
            [
                'description' => 'ANOMALI: PINDAH LOKASI | Terdeteksi koordinat berubah drastis',
                'severity' => 'critical',
                'created_at' => Carbon::now()->subMinutes(30),
            ],
        ]);

        // 5. Seed Data Mentah (Sensor Data & GPS) sebagai Backup
        DB::table('sensor_data')->insert([
            'device_id' => 3,
            'jenis_input' => 'koin',
            'nominal' => 500,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('gps_data')->insert([
            'device_id' => 3,
            'latitude' => -7.9666,
            'longitude' => 112.6326,
            'speed' => 0.0,
            'created_at' => Carbon::now(),
        ]);
    }
}