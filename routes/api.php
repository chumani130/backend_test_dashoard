<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HeartbeatDataController;
use App\Http\Controllers\TamperAlertController;
use App\Http\Controllers\UserController;

Route::post('/payload', [ApiController::class, 'decodePayload']);
Route::post('/login', [ApiController::class, 'login']);
Route::get('/dashboard', [ApiController::class, 'dashboard']);


//  CRUD for models
Route::apiResource('devices', DeviceController::class);
Route::apiResource('heartbeats', HeartbeatDataController::class);
Route::apiResource('alerts', TamperAlertController::class);
Route::apiResource('users', UserController::class);