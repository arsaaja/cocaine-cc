<?php

use Illuminate\Support\Facades\Route;

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

