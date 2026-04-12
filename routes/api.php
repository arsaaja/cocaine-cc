<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IngestController;
use App\Http\Controllers\Api\CommandController;
use App\Http\Controllers\Api\WorkerController;
use App\Http\Controllers\Api\DashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


/*
|--------------------------------------------------------------------------
| API Routes - Backend IoT Project COCAINE
|--------------------------------------------------------------------------
*/

Route::middleware(['log.api'])->group(function () {

    // 1. Ingest Data (Pemasukan Uang dari Python/ESP32)
    Route::post('/ingest', [IngestController::class, 'store']);

    // 2. Command System (Kendali Solenoid dll)
    Route::prefix('command')->group(function () {
        Route::post('/send', [CommandController::class, 'send']);
        Route::get('/get', [CommandController::class, 'getPending']);
        Route::post('/update', [CommandController::class, 'updateStatus']);
    });

    // 3. Worker Status (Heartbeat Python)
    Route::prefix('status')->group(function () {
        Route::post('/update', [WorkerController::class, 'update']);
    });

    // 4. Dashboard Data (Data JSON untuk Grafik & Tabel)
    Route::prefix('dashboard')->group(function () {
        Route::get('/data', [DashboardController::class, 'index']);
        Route::get('/log', [DashboardController::class, 'logs']);
        Route::get('/chart/{device}/{type}', [DashboardController::class, 'chartData']);
    });
});

// Fallback jika API salah ketik
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Endpoint API tidak ditemukan. Periksa kembali dokumentasi COCAINE.'
    ], 404);
});