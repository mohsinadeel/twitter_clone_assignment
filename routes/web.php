<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes (handled by Fortify)
Route::middleware('auth')->group(function () {
    Route::get('/home', [ProfileController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings.show');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});
