<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Sanctum CSRF cookie route
Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

// Authentication routes (handled by Fortify)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/home', [ProfileController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings.show');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
});
