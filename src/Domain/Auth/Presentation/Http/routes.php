<?php

use Illuminate\Support\Facades\Route;
use Domain\Auth\Presentation\Http\Controllers\AuthController;
Route::group([
    'namespace' => 'App\Http\Controllers\Api',
    'prefix' => 'auth'
], function () {
    Route::post('/signup', [AuthController::class, 'signup']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
    Route::post('/forgot-password', [AuthController::class, 'forgot'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.reset');
});