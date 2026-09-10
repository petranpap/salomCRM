<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppointmentApiController;
use App\Http\Controllers\Api\CustomerApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\TodoApiController;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('appointments', AppointmentApiController::class);
    Route::apiResource('customers', CustomerApiController::class);
    Route::apiResource('products', ProductApiController::class);

    Route::get('inventory/alerts', [ProductApiController::class, 'lowStockAlerts']);
    Route::get('todos', [TodoApiController::class, 'index']);
});
