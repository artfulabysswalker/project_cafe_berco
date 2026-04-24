<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Fortify;

// Custom login route tanpa redirect untuk user yang sudah auth
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

// Register route
Route::post('/register', function () {
    return view('auth.register');
})->name('register');

// Logout route
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');
