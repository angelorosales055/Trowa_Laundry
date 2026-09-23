<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:staff,manager,admin')
        ->name('dashboard');
    Route::middleware('role:staff,manager,admin')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
        Route::post('/orders/update', [DashboardController::class, 'updateOrder'])->name('orders.update');
        Route::post('/orders/add', [DashboardController::class, 'addOrder'])->name('orders.add');
    });
    Route::middleware('role:manager,admin')->group(function () {
        Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/billing', [DashboardController::class, 'billing'])->name('billing');
        Route::get('/settings/services', [ServiceController::class, 'index'])->name('services.index');
        Route::post('/settings/services', [ServiceController::class, 'store'])->name('services.store');
        Route::put('/settings/services/{service}', [ServiceController::class, 'update'])->name('services.update');
        Route::delete('/settings/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    });
});
