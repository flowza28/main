<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PoolGroupController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin,technisi'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('customers', CustomerController::class)->except(['show']);
    Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('packages', PackageController::class)->except(['show']);
    Route::resource('pool-groups', PoolGroupController::class)->except(['show']);
    Route::get('invoices/generate', [InvoiceController::class, 'generateMonthly'])->name('invoices.generate');
    Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])->name('payments.store');
});
