<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SecurityLog;
use App\Models\Device;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogApiRequest
{
    /**
     * Handle an incoming request.
     * 
     * Middleware ini bertugas mencatat setiap upaya akses yang gagal atau mencurigakan 
     * ke dalam tabel SecurityLog agar muncul di dashboard riwayat keamanan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jalankan request ke controller terlebih dahulu
        $response = $next($request);

        // Logging dilakukan SETELAH response didapat (Post-Middleware)
        try {
            // Ambil API Key (mendukung Bearer Token atau Query String untuk ESP32)
            $apiKey = $request->bearerToken() ?: $request->input('api_key');

            // Cari device berdasarkan api_key
            $device = $apiKey ? Device::where('api_key', $apiKey)->first() : null;

            // Tentukan status code
            $statusCode = $response->getStatusCode();

            /**
             * LOGIKA FILTER KEAMANAN:
             * Mencatat jika:
             * 1. Perangkat tidak dikenal (Tanpa API Key yang valid)
             * 2. Terjadi Error pada Server atau Client (4xx atau 5xx)
             */
            if (!$device || $statusCode >= 400) {

                // Format Deskripsi agar rapi di tabel Dashboard
                $method = strtoupper($request->method());
                $path = $request->path();
                $deviceName = $device ? $device->name : 'UNKNOWN_DEVICE';
                $ip = $request->ip();

                $description = "AKSES_API: [{$method}] /{$path} | Status: {$statusCode} | IP: {$ip} | Device: {$deviceName}";

                // Tentukan Severity
                // Status 401/403 (Unauthorized) atau 500 (Internal Error) dianggap Critical
                $severity = ($statusCode == 401 || $statusCode == 403 || $statusCode >= 500)
                    ? 'critical'
                    : 'warning';

                SecurityLog::create([
                    'description' => $description,
                    'severity' => $severity,
                ]);
            }

        } catch (\Exception $e) {
            // Log ke file laravel.log jika database sedang bermasalah agar tidak mengganggu aliran data ESP32
            Log::error("Middleware Log Error: " . $e->getMessage());
        }

        return $response;
    }
}