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
        // 1. Seed Tabel Users
        DB::table('users')->updateOrInsert(['id' => 1], [
            'username' => 'Budi',
            'name' => 'Budi Gaming',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password123'),
            'target_title' => 'Beli Sepatu Baru', // Ditambahkan agar fitur target ter-seed awal
            'target_amount' => 500000,           // Target tabungan Rp 500.000
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. Seed Tabel Devices (Sekarang terikat ke User ID 1)
        DB::table('devices')->updateOrInsert(['id' => 3], [
            'user_id' => 1, // PENTING: Sambungkan ke Budi
            'device_name' => 'ESP32_Celengan_01',
            'api_key' => 'api_cocaine_01',
            'status' => 'online',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 3. Masukkan Data Sensor (Breakdown Kotak Statistik Fisik)
        // Menghapus data lama agar tidak terjadi duplikasi saat re-seed
        DB::table('sensor_data')->where('device_id', 3)->delete();

        $sensorData = [
            [
                'device_id' => 3,
                'jenis_input' => 'koin',
                'nominal' => 170000,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'device_id' => 3,
                'jenis_input' => 'kertas',
                'nominal' => 0,
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ]
        ];
        DB::table('sensor_data')->insert($sensorData);

        // 4. Hitung Saldo Berdasarkan Sensor Data untuk Ledger Transaksi
        $calculatedBalance = DB::table('sensor_data')->where('device_id', 3)->sum('nominal');

        // 5. Seed Tabel Transactions (Ledger Utama Finansial Aplikasi)
        // PENTING: Wajib menyertakan 'id' berurutan dan 'user_id' agar query ::latest() milik Eloquent bekerja sempurna
        DB::table('transactions')->delete(); // Bersihkan log lama

        $transactions = [
            [
                'id' => 1,
                'user_id' => 1, // Diikat ke User Budi (ID: 1)
                'activity' => 'DEBIT',
                'amount' => 170000,
                'balance_snapshot' => $calculatedBalance,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            [
                'id' => 2,
                'user_id' => 1, // Diikat ke User Budi (ID: 1)
                'activity' => 'UANG TIDAK VALID',
                'amount' => 0,
                'balance_snapshot' => $calculatedBalance, // Saldo snapshot tetap Rp 170.000
                'created_at' => Carbon::now()->subHours(1),
                'updated_at' => Carbon::now()->subHours(1),
            ],
        ];
        DB::table('transactions')->insert($transactions);

        // 6. Seed Tabel Security Logs
        DB::table('security_logs')->delete();
        DB::table('security_logs')->insert([
            [
                'description' => 'ANOMALI: SALAH PIN | Upaya akses gagal di panel fisik',
                'severity' => 'warning',
                'created_at' => Carbon::now()->subHours(10),
                'updated_at' => Carbon::now()->subHours(10),
            ],
            [
                'description' => 'ANOMALI: PINDAH LOKASI | Terdeteksi koordinat berubah drastis',
                'severity' => 'critical',
                'created_at' => Carbon::now()->subMinutes(30),
                'updated_at' => Carbon::now()->subMinutes(30),
            ],
        ]);

        // 7. Seed GPS Data
        DB::table('gps_data')->where('device_id', 3)->delete();
        DB::table('gps_data')->insert([
            'device_id' => 3,
            'latitude' => -7.9666,
            'longitude' => 112.6326,
            'speed' => 0.0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}