<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\ComputerController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AdminStatisticsController;

// Публичные маршруты
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/zones', [ZoneController::class, 'index']);
Route::get('/zones/{zone}', [ZoneController::class, 'show']);

Route::get('/computers', [ComputerController::class, 'index']);
Route::get('/computers/{computer}', [ComputerController::class, 'show']);

Route::get('/games', [GameController::class, 'index']);
Route::get('/games/{game}', [GameController::class, 'show']);

// Защищённые маршруты
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('bookings', BookingController::class)
        ->only(['index', 'store', 'show', 'destroy', 'update']);

    Route::apiResource('payments', PaymentController::class)
        ->only(['index', 'store']);

    // Административные маршруты
    Route::middleware('admin')->group(function () {
        Route::apiResource('zones', ZoneController::class)
            ->except(['index', 'show']);

        Route::apiResource('computers', ComputerController::class)
            ->except(['index', 'show']);

        Route::apiResource('games', GameController::class)
            ->except(['index', 'show']);

        Route::apiResource('rooms', RoomController::class);

        Route::apiResource('users', AdminUserController::class)
            ->only(['index', 'store', 'show', 'update', 'destroy']);

        Route::get('/statistics', [AdminStatisticsController::class, 'index']);
    });
});
