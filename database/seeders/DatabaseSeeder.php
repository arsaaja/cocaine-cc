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
        // 1. Seed Tabel Users (Sesuai Dashboard image_b5d89b.png)
        DB::table('users')->updateOrInsert(['id' => 1], [
            'username' => 'Budi',
            'name' => 'Budi Gaming',
            'email' => 'budi@gmail.com',
            'password' => Hash::make('password123'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 2. Seed Tabel Devices (ESP32 DOIT V1 DevKit)
        DB::table('devices')->updateOrInsert(['id' => 3], [
            'device_name' => 'ESP32_Celengan_01',
            'api_key' => 'api_cocaine_01',
            'status' => 'online',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // 3. Masukkan Data Sensor Dulu (Berdasarkan Screenshot Dashboard Kamu)
        // Kita masukan data agar totalnya Rp 165.000 (Koin 170k - Kertas 0, atau sesuai saldo di gambar)
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
                'nominal' => 0, // Inisialisasi awal kertas nol sesuai image_b5d89b.png
                'created_at' => Carbon::now()->subDays(1),
                'updated_at' => Carbon::now()->subDays(1),
            ]
        ];
        DB::table('sensor_data')->insert($sensorData);

        // 4. Hitung Saldo Berdasarkan Sensor Data untuk Transaksi
        $calculatedBalance = DB::table('sensor_data')->sum('nominal');

        // 5. Seed Tabel Transactions (Log Transaksi Riwayat)
        // Snapshot diambil dari total nominal sensor data yang baru saja dimasukkan
        $transactions = [
            [
                'activity' => 'DEBIT',
                'amount' => 170000,
                'balance_snapshot' => $calculatedBalance,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            [
                'activity' => 'UANG TIDAK VALID',
                'amount' => 0,
                'balance_snapshot' => $calculatedBalance, // Saldo tidak berubah jika tidak valid
                'created_at' => Carbon::now()->subHours(1),
                'updated_at' => Carbon::now()->subHours(1),
            ],
        ];
        DB::table('transactions')->insert($transactions);

        // 6. Seed Tabel Security Logs (Log Keamanan Riwayat)
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