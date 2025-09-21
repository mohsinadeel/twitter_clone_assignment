<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes (public)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected authentication routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
});

// API v1 routes
Route::prefix('v1')->group(function () {

    // Users group - all routes require authentication
    Route::prefix('users')->name('users.')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/username/{username}', [UserController::class, 'showByUsername'])->name('showByUsername');
        Route::get('/{userId}/posts', [PostController::class, 'getByUserId'])->where('userId', '[0-9]+')->name('posts');
        Route::get('/{id}', [UserController::class, 'show'])->name('show');
        Route::put('/{id}', [UserController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });

    // Posts group - all routes require authentication
    Route::prefix('posts')->name('posts.')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('/{id}', [PostController::class, 'show'])->where('id', '[0-9]+')->name('show');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::put('/{id}', [PostController::class, 'update'])->where('id', '[0-9]+')->name('update');
        Route::delete('/{id}', [PostController::class, 'destroy'])->where('id', '[0-9]+')->name('destroy');
    });
});

