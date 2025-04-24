<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HeartbeatDataController;
use App\Http\Controllers\TamperAlertController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/devices/data', [ApiController::class, 'store']); // For device data ingestion

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Route::get('/user', function (Request $request) {
    //     return $request->user();
    // });
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Devices
    Route::get('/devices', [DeviceController::class, 'index']);
    Route::post('/devices', [DeviceController::class, 'store']);
    Route::get('/devices/{id}', [DeviceController::class, 'show']);
    Route::put('/devices/{id}', [DeviceController::class, 'update']);
    Route::delete('/devices/{id}', [DeviceController::class, 'destroy']);
    Route::get('/devices/{id}/stats', [DeviceController::class, 'getDeviceStats']);
    
    // Heartbeat Data
    Route::get('/heartbeats', [HeartbeatDataController::class, 'index']);
    Route::post('/heartbeats', [HeartbeatDataController::class, 'store']);
    Route::get('/heartbeats/{id}', [HeartbeatDataController::class, 'show']);
    Route::put('/heartbeats/{id}', [HeartbeatDataController::class, 'update']);
    Route::delete('/heartbeats/{id}', [HeartbeatDataController::class, 'destroy']);
    
    // Tamper Alerts
    Route::get('/tamper-alerts', [TamperAlertController::class, 'index']);
    Route::post('/tamper-alerts', [TamperAlertController::class, 'store']);
    Route::get('/tamper-alerts/{id}', [TamperAlertController::class, 'show']);
    Route::put('/tamper-alerts/{id}', [TamperAlertController::class, 'update']);
    Route::delete('/tamper-alerts/{id}', [TamperAlertController::class, 'destroy']);
    
    // Users (if needed)
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});