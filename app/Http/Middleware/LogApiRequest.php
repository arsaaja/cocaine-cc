<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SecurityLog; // Diubah dari ActivityLog
use App\Models\Device;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogApiRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Jalankan request ke controller dulu
        $response = $next($request);

        // 2. Logging dilakukan SETELAH response didapat (Post-Middleware)
        try {
            $authHeader = $request->header('Authorization');
            $apiKey = null;

            if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                $apiKey = $matches[1];
            }

            if (!$apiKey) {
                $apiKey = $request->input('api_key');
            }

            // Cari device berdasarkan api_key
            $device = Device::where('api_key', $apiKey)->first();

            // LOGIKA FILTER: Hanya catat jika akses mencurigakan atau error (Security Audit)
            if (!$device || $response->getStatusCode() >= 400) {
                SecurityLog::create([
                    'description' => "API Access: " . $request->method() . " " . $request->path() .
                        " | Status: " . $response->getStatusCode() .
                        " | IP: " . $request->ip() .
                        " | Device: " . ($device ? $device->name : 'Unknown Device'),
                    'severity' => $response->getStatusCode() >= 400 ? 'critical' : 'warning',
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Middleware Log Error: " . $e->getMessage());
        }

        return $response;
    }
}