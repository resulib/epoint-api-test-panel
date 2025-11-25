<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EpointLogsController;
use App\Http\Controllers\EpointTestController;
use Illuminate\Support\Facades\Route;

// Authentication routes (publicly accessible with rate limiting)
Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Protected routes (require authentication)
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Home redirect+
    Route::get('/', function () {
        return redirect()->route('epoint.test');
    });

    // Documentation Routes
    Route::prefix('docs')->name('docs.')->group(function () {
        Route::get('/', [App\Http\Controllers\DocsController::class, 'index'])->name('index');
        Route::get('/quick-start', [App\Http\Controllers\DocsController::class, 'quickStart'])->name('quick-start');
        Route::get('/refactoring', [App\Http\Controllers\DocsController::class, 'refactoring'])->name('refactoring');
    });

    // Epoint Test Routes - with stricter rate limiting for API calls
    Route::prefix('epoint-test')->group(function () {
        Route::get('/', [EpointTestController::class, 'index'])->name('epoint.test');

        // API execution endpoints with stricter rate limiting
        Route::middleware('throttle:30,1')->group(function () {
            Route::post('/execute', [EpointTestController::class, 'execute'])->name('epoint.execute');
            Route::post('/checkout/execute', [EpointTestController::class, 'checkoutExecute'])->name('epoint.checkout.execute');
            Route::post('/invoices/execute', [EpointTestController::class, 'invoiceExecute'])->name('epoint.invoice.execute');
        });

        // Checkout API routes
        Route::get('/checkout', [EpointTestController::class, 'checkoutIndex'])->name('epoint.checkout');

        // Invoice API routes
        Route::get('/invoices', [EpointTestController::class, 'invoiceIndex'])->name('epoint.invoice');
    });

    // Epoint Logs Routes
    Route::prefix('epoint-logs')->group(function () {
        Route::get('/', [EpointLogsController::class, 'index'])->name('epoint.logs.index');
        Route::get('/dashboard', [EpointLogsController::class, 'dashboard'])->name('epoint.logs.dashboard');
        Route::get('/{id}', [EpointLogsController::class, 'show'])->name('epoint.logs.show');
        Route::delete('/{id}', [EpointLogsController::class, 'destroy'])->name('epoint.logs.destroy');
        Route::post('/clear', [EpointLogsController::class, 'clear'])->name('epoint.logs.clear');
        Route::get('/export/json', [EpointLogsController::class, 'export'])->name('epoint.logs.export');
    });
});
