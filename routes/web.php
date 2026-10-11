<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPortalController;
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

// Customer Public Auth & Verification Routes
Route::get('/customer/register', [CustomerPortalController::class, 'showRegister'])->name('customer.register');
Route::post('/customer/register', [CustomerPortalController::class, 'processRegister'])->name('customer.register.post');
Route::get('/customer/verify', [CustomerPortalController::class, 'showVerify'])->name('customer.verify');
Route::post('/customer/verify', [CustomerPortalController::class, 'processVerify'])->name('customer.verify.post');
Route::post('/customer/resend-code', [CustomerPortalController::class, 'resendCode'])->name('customer.resend-code');
Route::get('/customer/login', [CustomerPortalController::class, 'showLogin'])->name('customer.login');
Route::post('/customer/login', [CustomerPortalController::class, 'processLogin'])->name('customer.login.post');
Route::post('/customer/logout', [CustomerPortalController::class, 'logout'])->name('customer.logout');

// Customer Authenticated Portal Routes
Route::middleware(['web', 'auth', 'role:customer'])->group(function () {
    Route::get('/customer/portal', [CustomerPortalController::class, 'portal'])->name('customer.portal');
    Route::post('/customer/orders', [CustomerPortalController::class, 'submitIntake'])->name('customer.orders.submit');
    Route::post('/customer/orders/{order}/rate', [CustomerPortalController::class, 'submitRating'])->name('customer.orders.rate');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:staff,admin')
        ->name('dashboard');
    Route::middleware('role:staff,admin')->group(function () {
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->middleware('role:admin')->name('customers.update');
        Route::post('/customers/{customer}/merge', [CustomerController::class, 'merge'])->middleware('role:admin')->name('customers.merge');
        Route::post('/customers/{customer}/send-email', [CustomerController::class, 'sendDirectEmail'])->name('customers.send-email');
        Route::patch('/customers/{customer}/active', [CustomerController::class, 'toggleActive'])->middleware('role:admin')->name('customers.toggle-active');
        Route::get('/schedule', [OrderStatusController::class, 'index'])->name('schedule.index');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::get('/orders/{order}', [ScheduleController::class, 'show'])->name('orders.show');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/orders/{order}/status', [OrderStatusController::class, 'update'])->name('orders.status.update');
        Route::post('/orders/{order}/resend-ready-email', [OrderStatusController::class, 'resendReadyEmail'])->name('orders.resend-ready-email');
        Route::post('/orders/{order}/confirm-online', [OrderStatusController::class, 'confirmOnlineOrder'])->name('orders.confirm-online');
        Route::post('/orders/{order}/reject-online', [OrderStatusController::class, 'rejectOnlineOrder'])->name('orders.reject-online');
        Route::post('/orders/{order}/payments', [PaymentController::class, 'store'])->name('orders.payments.store');
        Route::post('/orders/{order}/payments/{payment}/refund', [PaymentController::class, 'refund'])->name('orders.payments.refund');
        Route::post('/orders/update', [DashboardController::class, 'updateOrder'])->name('orders.update');
        Route::post('/orders/add', [OrderController::class, 'store'])->name('orders.add');
    });
    Route::middleware('role:admin')->group(function () {
        Route::get('/feedbacks', [CustomerController::class, 'feedbacks'])->name('customers.feedbacks');
        Route::get('/admin/customer-insights', [CustomerController::class, 'insights'])->name('customers.insights');
        Route::get('/reports', [DashboardController::class, 'reports'])->name('reports');
        Route::get('/reports/print', [DashboardController::class, 'printReports'])->name('reports.print');
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::post('/inventory/{inventoryItem}/movements', [InventoryController::class, 'movement'])->name('inventory.movement');
        Route::post('/inventory/service-usage', [InventoryController::class, 'configureUsage'])->name('inventory.usage.store');
        Route::delete('/inventory/service-usage/{usage}', [InventoryController::class, 'deleteUsage'])->name('inventory.usage.destroy');
        Route::post('/inventory/suppliers', [InventoryController::class, 'storeSupplier'])->name('inventory.suppliers.store');
        Route::put('/inventory/suppliers/{supplier}', [InventoryController::class, 'updateSupplier'])->name('inventory.suppliers.update');
        Route::delete('/inventory/suppliers/{supplier}', [InventoryController::class, 'deleteSupplier'])->name('inventory.suppliers.destroy');
        Route::post('/inventory/suppliers/{supplier}/send-email', [InventoryController::class, 'sendSupplierEmail'])->name('inventory.suppliers.send-email');
        Route::post('/inventory/auto-restock', [InventoryController::class, 'autoRestock'])->name('inventory.auto-restock');
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::post('/expenses/auto-log-utilities', [ExpenseController::class, 'autoLogUtilities'])->name('expenses.auto-log-utilities');
        Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update-details');

        // Staff & Operator Account Management (Admin CRUD)
        Route::get('/admin/staff', [\App\Http\Controllers\StaffManagementController::class, 'index'])->name('admin.staff.index');
        Route::post('/admin/staff', [\App\Http\Controllers\StaffManagementController::class, 'store'])->name('admin.staff.store');
        Route::put('/admin/staff/{staff}', [\App\Http\Controllers\StaffManagementController::class, 'update'])->name('admin.staff.update');
        Route::delete('/admin/staff/{staff}', [\App\Http\Controllers\StaffManagementController::class, 'destroy'])->name('admin.staff.destroy');
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

// Cloud Deployment Helper: Run migrations and seeders remotely with secret APP_KEY
Route::get('/artisan/migrate', function (\Illuminate\Http\Request $request) {
    $key = (string) $request->query('key');
    $expectedKey = (string) config('app.key');

    if (blank($expectedKey) || ! hash_equals($expectedKey, $key)) {
        abort(403, 'Unauthorized. Please provide ?key=YOUR_APP_KEY');
    }

    try {
        $isFresh = $request->boolean('fresh');
        if ($isFresh) {
            \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
            $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
        } else {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
            } catch (\Throwable $migrationError) {
                if (str_contains($migrationError->getMessage(), '42S01') || str_contains($migrationError->getMessage(), 'already exists')) {
                    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true]);
                    $migrateOutput = "Auto-recovered from desynced tables (ran migrate:fresh):\n".\Illuminate\Support\Facades\Artisan::output();
                } else {
                    throw $migrationError;
                }
            }
        }

        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $seedOutput = \Illuminate\Support\Facades\Artisan::output();

        return response(
            "<pre style='font-family:monospace;padding:24px;background:#0f172a;color:#38bdf8;border-radius:8px;line-height:1.6;'>"
            ."<h2 style='color:#4ade80;margin-top:0;'>Migration and Database Seeding Succeeded</h2>\n"
            ."<strong style='color:#f8fafc;'>--- MIGRATIONS ---</strong>\n".e($migrateOutput)."\n\n"
            ."<strong style='color:#f8fafc;'>--- SEEDING ---</strong>\n".e($seedOutput)."\n\n"
            ."<span style='color:#4ade80;'>All tables created and initial accounts (admin123, staff123) are ready!</span>\n"
            ."<a href='".route('login')."' style='color:#fbbf24;display:inline-block;margin-top:16px;'>Go to Sign In &rarr;</a>"
            ."</pre>"
        );
    } catch (\Throwable $e) {
        return response(
            "<pre style='font-family:monospace;padding:24px;background:#450a0a;color:#fca5a5;border-radius:8px;line-height:1.6;'>"
            ."<h2 style='color:#ef4444;margin-top:0;'>Migration Error</h2>\n"
            ."<strong>Message:</strong> ".e($e->getMessage())."\n\n"
            ."<strong>File:</strong> ".e($e->getFile()).':'.e($e->getLine())."\n\n"
            ."<strong>Stack Trace:</strong>\n".e($e->getTraceAsString())
            ."</pre>",
            500
        );
    }
})->withoutMiddleware([
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\Session\Middleware\AuthenticateSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
]);
