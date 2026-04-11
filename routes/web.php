<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RelayController;

// halaman utama (view monitoring)
Route::get('/', function () {
    return view('dashboard'); // nama file blade kamu
});

