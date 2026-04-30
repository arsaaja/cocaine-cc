<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Penting untuk hashing password

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Tabel Users
        DB::table('users')->insert([
            'id' => 1,
            'username' => 'Budi',
            'name' => 'Budi Gaming',
            'email' => 'budi@gmail.com',
            // Menggunakan Hash::make untuk keamanan dan agar password bisa diketahui
            'password' => Hash::make('password123'),
            'created_at' => '2026-04-28 04:18:32',
            'updated_at' => '2026-04-28 04:18:32',
        ]);

        // 2. Seed Tabel Devices[cite: 1]
        DB::table('devices')->insert([
            'id' => 3,
            'device_name' => 'tes1',
            'api_key' => 'api_cocaine_01',
            'status' => 'online',
            'created_at' => '2026-04-21 06:26:42',
            'updated_at' => '2026-04-21 06:26:42',
        ]);

        // 3. Seed Tabel Sensor Data (Ringkasan dari dump SQL)[cite: 1]
        $sensorData = [];

        // Pola data koin (100, 200, 500, 1000)
        for ($i = 1; $i <= 178; $i++) {
            $nominals = [100, 200, 500, 1000];
            $sensorData[] = [
                'device_id' => 3,
                'jenis_input' => 'koin',
                'nominal' => $nominals[($i - 1) % 4],
                'created_at' => '2026-04-28 05:25:26',
                'updated_at' => '2026-04-28 05:25:26',
            ];
        }

        // Sisa data dengan nominal 500
        for ($i = 179; $i <= 247; $i++) {
            $sensorData[] = [
                'device_id' => 3,
                'jenis_input' => 'koin',
                'nominal' => 500,
                'created_at' => '2026-04-28 05:42:55',
                'updated_at' => '2026-04-28 05:42:55',
            ];
        }

        // Masukkan data dalam potongan (chunks) agar lebih efisien
        foreach (array_chunk($sensorData, 100) as $chunk) {
            DB::table('sensor_data')->insert($chunk);
        }
    }
}