<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes - Tampilan Dashboard COCAINE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth.register');
})->name('register');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');


// Proses Auth
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/login', [AuthController::class, 'login'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (Hanya bisa diakses jika sudah login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');

// Halaman Riwayat Transaksi
Route::get('/riwayat', function () {
    return view('riwayat');
});

// Halaman Lokasi Alat (Maps)
Route::get('/lokasi', function () {
    return view('lokasi');
});

// Halaman Pengaturan
Route::get('/pengaturan', function () {
    return view('pengaturan');
});