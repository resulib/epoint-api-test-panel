<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EpointLogsController;
use App\Http\Controllers\EpointTestController;
use Illuminate\Support\Facades\Route;

// Authentication routes (publicly accessible)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Protected routes (require authentication)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Home redirect
    Route::get('/', function () {
        return redirect()->route('epoint.test');
    });

    // Epoint Test Routes
    Route::prefix('epoint-test')->group(function () {
        Route::get('/', [EpointTestController::class, 'index'])->name('epoint.test');
        Route::post('/execute', [EpointTestController::class, 'execute'])->name('epoint.execute');

        // Invoice API routes
        Route::get('/invoices', [EpointTestController::class, 'invoiceIndex'])->name('epoint.invoice');
        Route::post('/invoices/execute', [EpointTestController::class, 'invoiceExecute'])->name('epoint.invoice.execute');
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
