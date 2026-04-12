<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Tampilan Dashboard COCAINE
|--------------------------------------------------------------------------
*/

// Halaman Utama (Monitoring Real-time)
Route::get('/', function () {
    return view('dashboard');
});

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