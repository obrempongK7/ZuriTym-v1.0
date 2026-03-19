<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TaskAdminController;
use App\Http\Controllers\Admin\WithdrawalController;
use App\Http\Controllers\Admin\OfferwallAdminController;
use App\Http\Controllers\Admin\SpinAdminController;
use App\Http\Controllers\Admin\PromoCodeController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AdNetworkController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use Illuminate\Support\Facades\Route;

// Admin Auth
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login',  [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('logout',[AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {
        Route::get('dashboard',            [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',                [UserController::class, 'index'])->name('index');
            Route::get('{user}',           [UserController::class, 'show'])->name('show');
            Route::put('{user}',           [UserController::class, 'update'])->name('update');
            Route::post('{user}/block',    [UserController::class, 'block'])->name('block');
            Route::post('{user}/unblock',  [UserController::class, 'unblock'])->name('unblock');
            Route::post('{user}/credit',   [UserController::class, 'creditWallet'])->name('credit');
            Route::post('{user}/debit',    [UserController::class, 'debitWallet'])->name('debit');
            Route::delete('{user}',        [UserController::class, 'destroy'])->name('destroy');
        });

        // Tasks
        Route::resource('tasks', TaskAdminController::class)->except(['show']);

        // Withdrawals
        Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
            Route::get('/',                     [WithdrawalController::class, 'index'])->name('index');
            Route::get('{withdrawal}',          [WithdrawalController::class, 'show'])->name('show');
            Route::post('{withdrawal}/approve', [WithdrawalController::class, 'approve'])->name('approve');
            Route::post('{withdrawal}/reject',  [WithdrawalController::class, 'reject'])->name('reject');
        });

        // Offerwalls
        Route::resource('offerwalls', OfferwallAdminController::class)->except(['show']);

        // Spin Wheel
        Route::resource('spin-rewards', SpinAdminController::class)->except(['show']);

        // Promo Codes
        Route::resource('promo-codes', PromoCodeController::class)->except(['show']);

        // Notifications
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/',          [NotificationController::class, 'index'])->name('index');
            Route::get('create',     [NotificationController::class, 'create'])->name('create');
            Route::post('send',      [NotificationController::class, 'send'])->name('send');
        });

        // Settings
        Route::get('settings',              [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings',             [SettingController::class, 'update'])->name('settings.update');
        Route::get('ad-networks',           [AdNetworkController::class, 'index'])->name('ad-networks.index');
        Route::post('ad-networks',          [AdNetworkController::class, 'update'])->name('ad-networks.update');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('transactions',  [ReportController::class, 'transactions'])->name('transactions');
            Route::get('export',        [ReportController::class, 'export'])->name('export');
        });
    });
});

Route::redirect('/', '/admin/login');
