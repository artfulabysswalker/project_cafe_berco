<?php

use Illuminate\Support\Facades\Route;

// Halaman Utama
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Halaman Login (Biasanya otomatis ada kalau pakai Breeze, kita pastikan name-nya benar)
Route::get('/login', function () {
    return view('auth.login'); 
})->name('login');

// Halaman Dashboard (Setelah Login)
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php'; // Pastikan ini ada untuk sistem loginnya