<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\ComputerController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;

// Публичные маршруты (доступны гостям)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/zones', [ZoneController::class, 'index']);
Route::get('/zones/{zone}', [ZoneController::class, 'show']);
Route::get('/computers', [ComputerController::class, 'index']);
Route::get('/computers/{computer}', [ComputerController::class, 'show']);
Route::get('/games', [GameController::class, 'index']);
Route::get('/games/{game}', [GameController::class, 'show']);

// Защищенные маршруты (требуют Bearer-токен Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Бронирования и платежи (доступны клиентам и админам)
    Route::apiResource('bookings', BookingController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::apiResource('payments', PaymentController::class)->only(['index', 'store']);

    // Административные маршруты (проверка роли через middleware 'admin')
    Route::middleware('admin')->group(function () {
    Route::apiResource('zones', ZoneController::class)->except(['index', 'show']);        Route::apiResource('computers', ComputerController::class)->except(['index', 'show']);
    Route::apiResource('games', GameController::class)->except(['index', 'show']);    });
});