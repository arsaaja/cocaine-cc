<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IngestController;
use App\Http\Controllers\Api\CommandController;
use App\Http\Controllers\Api\WorkerController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\TargetController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// 1. Ingest Data (Pemasukan Uang & Sinyal Bahaya)
Route::post('/ingest', [IngestController::class, 'store']);
Route::post('/alert', [IngestController::class, 'alert']);

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

// 4. Dashboard Data 
Route::prefix('dashboard')->group(function () {
    Route::get('/data', [DashboardController::class, 'getData']);
    Route::get('/log', [DashboardController::class, 'logs']);
    Route::get('/chart/{device}/{type}', [DashboardController::class, 'chartData']);
});

Route::get('/latest', [LocationController::class, 'getLatestLocation']);

Route::middleware(['auth'])->group(function () {
    Route::post('/dashboard/save-target', [TargetController::class, 'save']);
    Route::post('/dashboard/clear-target', [TargetController::class, 'clear']);
    Route::post('/saldo/reset', [DashboardController::class, 'resetSaldo']);
});