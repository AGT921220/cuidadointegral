<?php

use App\Http\Controllers\Admin\RoleHeadersController;
use App\Http\Controllers\Admin\UserHeadersController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('user-headers', [UserHeadersController::class, 'index'])
        ->name('user-headers.index');
    Route::resource('users', UserController::class)->except(['show']);

    Route::get('role-headers', [RoleHeadersController::class, 'index'])
        ->name('role-headers.index');
    Route::resource('roles', RoleController::class)->except(['show']);
});
