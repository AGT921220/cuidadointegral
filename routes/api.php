<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\CareLogController;
use App\Http\Controllers\Api\CareReportController;
use App\Http\Controllers\Api\CareTaskController;
use App\Http\Controllers\Api\HealthRecordController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ShiftController;
use App\Http\Controllers\Api\ShiftLocationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('/login', LoginController::class)->middleware('throttle:5,1');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/logout', LogoutController::class);
        Route::get('/user', UserController::class);
    });
});

Route::get('/user', UserController::class)->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/shifts', [ShiftController::class, 'index']);
    Route::post('/shifts', [ShiftController::class, 'store']);
    Route::get('/shifts/{shift}', [ShiftController::class, 'show']);
    Route::post('/shifts/{shift}/check-in', [ShiftController::class, 'checkIn']);
    Route::post('/shifts/{shift}/check-out', [ShiftController::class, 'checkOut']);
    Route::post('/shifts/{shift}/locations', [ShiftLocationController::class, 'store']);
    Route::get('/shifts/{shift}/tasks', [CareTaskController::class, 'index']);
    Route::post('/shifts/{shift}/tasks/{careTask}/records', [CareTaskController::class, 'record']);
    Route::get('/shifts/{shift}/logs', [CareLogController::class, 'index']);
    Route::post('/shifts/{shift}/logs', [CareLogController::class, 'store']);
    Route::get('/shifts/{shift}/health-records', [HealthRecordController::class, 'index']);
    Route::post('/shifts/{shift}/health-records', [HealthRecordController::class, 'store']);
    Route::post('/shifts/{shift}/incidents', [IncidentController::class, 'store']);

    Route::get('/patients/{patient}', [PatientController::class, 'show']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::post('/patients/{patient}/links', [PatientController::class, 'link']);

    Route::post('/care-tasks', [CareTaskController::class, 'store']);
    Route::put('/care-logs/{careLog}', [CareLogController::class, 'update']);
    Route::get('/incidents/{incident}', [IncidentController::class, 'show']);
    Route::post('/incidents/{incident}/events', [IncidentController::class, 'attend']);
    Route::get('/reports', [CareReportController::class, 'index']);
});
