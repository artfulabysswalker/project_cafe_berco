<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Fortify;

// Custom login route tanpa redirect untuk user yang sudah auth
Route::get('/login', function () {
    return view('pages.auth.login');
})->name('login')->middleware('guest');

// Register route
Route::get('/register', function () {
    return view('pages.auth.register');
})->name('register')->middleware('guest');
