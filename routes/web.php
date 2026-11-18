<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\HallController as AdminHallController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Admin\ComputerController as AdminComputerController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PriceController as AdminPriceController;
use App\Http\Controllers\Admin\StatisticsController as AdminStatisticsController;

// =====================================================
// ГЛАВНАЯ СТРАНИЦА
// =====================================================

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// =====================================================
// ИГРОВЫЕ ЗОНЫ
// =====================================================

Route::get('/halls', [HallController::class, 'index'])
    ->name('halls.index');

Route::get('/halls/{id}', [HallController::class, 'show'])
    ->name('halls.show');

// =====================================================
// РЕГИСТРАЦИЯ
// =====================================================

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');

// =====================================================
// ВХОД
// =====================================================

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

// =====================================================
// ВЫХОД
// =====================================================

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// =====================================================
// ЛИЧНЫЙ КАБИНЕТ
// =====================================================

Route::get('/profile', [ProfileController::class, 'index'])
    ->middleware('auth')
    ->name('profile');

Route::patch('/profile/bookings/{booking}/cancel', [ProfileController::class, 'cancel'])
    ->middleware('auth')
    ->name('bookings.cancel');

// =====================================================
// БРОНИРОВАНИЕ
// =====================================================

Route::get('/booking/create', [BookingController::class, 'create'])
    ->middleware('auth')
    ->name('booking.create');

Route::post('/booking', [BookingController::class, 'store'])
    ->middleware('auth')
    ->name('booking.store');

Route::get('/booking/{booking}/success', [BookingController::class, 'success'])
    ->middleware('auth')
    ->name('booking.success');

Route::post('/booking/{booking}/cancel', [BookingController::class, 'cancel'])
    ->middleware('auth')
    ->name('booking.cancel');

// =====================================================
// ОПЛАТА
// =====================================================

Route::get('/payment/{booking}', [PaymentController::class, 'show'])
    ->middleware('auth')
    ->name('payment.show');

Route::post('/payment/{booking}/demo', [PaymentController::class, 'demoPay'])
    ->middleware('auth')
    ->name('payment.demo');

// =====================================================
// АДМИН-ПАНЕЛЬ
// =====================================================

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Главная админ-панели
        Route::get('/', [DashboardController::class, 'index'])
            ->name('dashboard');

        // Управление бронированиями
        Route::get('/bookings', [AdminBookingController::class, 'index'])
            ->name('bookings.index');

        Route::patch('/bookings/{booking}/confirm-payment', [AdminBookingController::class, 'confirmPayment'])
            ->name('bookings.confirm-payment');

        Route::patch('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])
            ->name('bookings.cancel');

        Route::delete('/bookings/{booking}', [AdminBookingController::class, 'destroy'])
            ->name('bookings.destroy');

        // Управление игровыми зонами
        Route::resource('/halls', AdminHallController::class)
            ->except(['show']);

        // Управление комнатами
        Route::resource('/rooms', AdminRoomController::class)
            ->except(['show']);

        // Управление компьютерами
        Route::resource('/computers', AdminComputerController::class)
            ->except(['show']);

        // Управление пользователями
        Route::resource('/users', AdminUserController::class)
            ->only(['index', 'edit', 'update', 'destroy']);

        // Управление тарифами
        Route::get('/prices', [AdminPriceController::class, 'index'])
            ->name('prices.index');

        Route::get('/prices/{zone}/edit', [AdminPriceController::class, 'edit'])
            ->name('prices.edit');

        Route::put('/prices/{zone}', [AdminPriceController::class, 'update'])
            ->name('prices.update');

        // Статистика
        Route::get('/statistics', [AdminStatisticsController::class, 'index'])
            ->name('statistics.index');
});
        // Vue SPA
        Route::view('/app/{any?}', 'vue')
            ->where('any', '.*')
            ->name('vue.app');