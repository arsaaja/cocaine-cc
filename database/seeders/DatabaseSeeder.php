<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Device;
use App\Models\SensorData;
use App\Models\WorkerStatus;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat User untuk Login Dashboard
        User::factory()->create([
            'name' => 'Admin Cocaine',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Buat Device Dummy untuk koneksi Python/IoT
        $device = Device::create([
            'device_name' => 'Simulasi Celengan 01',
            'api_key' => 'DUMMY-123', // Gunakan ini di sensor.py kamu
            'status' => 'online',
            'last_seen' => now(),
        ]);

        // 3. Buat Contoh Data Transaksi Awal
        SensorData::create([
            'device_id' => $device->id,
            'jenis_input' => 'koin',
            'nominal' => 1000,
        ]);

        SensorData::create([
            'device_id' => $device->id,
            'jenis_input' => 'kertas',
            'nominal' => 5000,
        ]);

        // 4. Buat Status Worker Awal
        WorkerStatus::create([
            'device_id' => $device->id,
            'worker_name' => 'Python_Dummy_Sensor',
            'status' => 'active',
            'last_run' => now(),
        ]);
    }
}