<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes (handled by Fortify)
Route::middleware('auth')->group(function () {
    Route::get('/home', [ProfileController::class, 'index'])->name('home');
});
