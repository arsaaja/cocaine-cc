<?php

use Illuminate\Support\Facades\Route;

// halaman utama (view monitoring)
Route::get('/', function () {
    return view('dashboard'); // nama file blade kamu
});