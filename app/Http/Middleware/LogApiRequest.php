<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Device;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Jalankan request ke controller dulu
        $response = $next($request);

        // 2. Logging dilakukan SETELAH response didapat (Terminable Middleware style)
        try {
            if ($request->has('api_key')) {
                // Cari device dengan api_key
                $device = Device::where('api_key', $request->api_key)->first();

                // Hanya buat log jika device ditemukan (opsional, tapi lebih aman)
                ActivityLog::create([
                    'device_id' => $device ? $device->id : null,
                    'action' => $request->method() . ' ' . $request->path(),
                    'description' => "Status: " . $response->getStatusCode() . " | IP: " . $request->ip(),
                ]);
            }
        } catch (\Exception $e) {
            // Jika logging gagal, jangan hentikan aplikasi, cukup catat di file log Laravel
            Log::error("Middleware Log Error: " . $e->getMessage());
        }

        return $response;
    }
}