<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;
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
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->middleware('role:admin')->name('customers.update');
        Route::post('/customers/{customer}/merge', [CustomerController::class, 'merge'])->middleware('role:admin')->name('customers.merge');
        Route::patch('/customers/{customer}/active', [CustomerController::class, 'toggleActive'])->middleware('role:admin')->name('customers.toggle-active');
        Route::get('/schedule', [OrderStatusController::class, 'index'])->name('schedule.index');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [ScheduleController::class, 'show'])->name('orders.show');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/orders/{order}/status', [OrderStatusController::class, 'update'])->name('orders.status.update');
        Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
        Route::post('/orders/{order}/payments/{payment}/refund', [PaymentController::class, 'refund'])->name('orders.payments.refund');
        Route::post('/orders/update', [DashboardController::class, 'updateOrder'])->name('orders.update');
        Route::post('/orders/add', [OrderController::class, 'store'])->name('orders.add');
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
        Route::get('/reports/print', [DashboardController::class, 'printReports'])->name('reports.print');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::post('/inventory/{inventoryItem}/movements', [InventoryController::class, 'movement'])->name('inventory.movement');
        Route::post('/inventory/service-usage', [InventoryController::class, 'configureUsage'])->name('inventory.usage.store');
        Route::delete('/inventory/service-usage/{usage}', [InventoryController::class, 'deleteUsage'])->name('inventory.usage.destroy');
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update-details');
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
