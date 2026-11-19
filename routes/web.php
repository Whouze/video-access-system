<?php

use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\AccessRequestController as AdminAccessRequestController;
use App\Http\Controllers\Customer\VideoController as CustomerVideoController;
use App\Http\Controllers\Customer\AccessRequestController as CustomerAccessRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// setelah login, Breeze mengarah ke 'dashboard'; kita arahkan sesuai role
Route::get('/dashboard', function () {
    return auth()->user()->role->role_name === 'admin'
        ? redirect()->route('admin.customers.index')
        : redirect()->route('customer.videos.index');
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('/customers', AdminCustomerController::class)->except(['show', 'create', 'edit']);
    Route::get('/videos/{video}/stream', [AdminVideoController::class, 'stream'])->name('videos.stream');
    Route::resource('/videos', AdminVideoController::class)->except('show');
    Route::get('/requests', [AdminAccessRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests/{accessRequest}/approve', [AdminAccessRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/requests/{accessRequest}/reject', [AdminAccessRequestController::class, 'reject'])->name('requests.reject');
});

Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/videos', [CustomerVideoController::class, 'index'])->name('videos.index');
    Route::get('/videos/{video}/stream', [CustomerVideoController::class, 'stream'])->name('videos.stream');
    Route::get('/requests', [CustomerAccessRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests', [CustomerAccessRequestController::class, 'store'])->name('requests.store');
    Route::post('/videos/{video}/request', [CustomerAccessRequestController::class, 'store'])->name('videos.request');
});

require __DIR__.'/auth.php';
