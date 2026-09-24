<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:staff,admin')
        ->name('dashboard');
    Route::middleware('role:staff,admin')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [ScheduleController::class, 'show'])->name('orders.show');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/orders/{order}/status', [OrderStatusController::class, 'update'])->name('orders.status.update');
        Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
        Route::post('/orders/update', [DashboardController::class, 'updateOrder'])->name('orders.update');
        Route::post('/orders/add', [OrderController::class, 'store'])->name('orders.add');
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/billing', [DashboardController::class, 'billing'])->name('billing');
        Route::get('/settings/services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('/settings/services', [ServiceController::class, 'store'])->name('services.store');
        Route::put('/settings/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::patch('/settings/services/{service}/active', [ServiceController::class, 'toggleActive'])->name('services.toggle-active');
        Route::delete('/settings/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    });
});
