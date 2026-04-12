<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Device;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;

class LogApiRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jalankan request-nya dulu (Post-Middleware)
        $response = $next($request);

        // Hanya log request yang memiliki api_key (filter untuk IoT/API)
        if ($request->has('api_key')) {
            $device = Device::where('api_key', $request->api_key)->first();

            ActivityLog::create([
                'device_id' => $device ? $device->id : null,
                'action' => $request->method() . ' ' . $request->path(),
                'description' => "Status: " . $response->getStatusCode() . " | IP: " . $request->ip(),
                // Opsional: Jika ingin simpan payload, pastikan di-json_encode
                // 'payload'  => json_encode($request->except(['api_key', 'password'])),
            ]);
        }

        return $response;
    }
}

// halaman utama (view monitoring)
Route::get('/', function () {
    return view('dashboard'); 
});

// Halaman baru untuk riwayat
Route::get('/riwayat', function () {
    return view('riwayat');
});

Route::get('/lokasi', function () {
    return view('lokasi');
});