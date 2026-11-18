<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', LoginController::class)->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', LogoutController::class);
        Route::get('/user', UserController::class);
    });
});

Route::get('/user', UserController::class)->middleware('auth:sanctum');
