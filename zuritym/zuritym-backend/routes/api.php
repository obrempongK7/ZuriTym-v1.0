<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\SpinController;
use App\Http\Controllers\Api\ScratchCardController;
use App\Http\Controllers\Api\OfferwallController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\HomeController;
use Illuminate\Support\Facades\Route;

// ── Public routes ──────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('register',       [AuthController::class, 'register']);
        Route::post('login',          [AuthController::class, 'login']);
        Route::post('google',         [AuthController::class, 'googleAuth']);
    });

    // Offerwall postbacks (unauthenticated, secret-verified)
    Route::post('postback/{slug}', [OfferwallController::class, 'postback']);

    // ── Authenticated routes ───────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::get('me',                [AuthController::class, 'me']);
            Route::post('logout',           [AuthController::class, 'logout']);
            Route::put('profile',           [AuthController::class, 'updateProfile']);
            Route::post('change-password',  [AuthController::class, 'changePassword']);
        });

        // Home
        Route::get('home',              [HomeController::class, 'index']);
        Route::get('app-settings',      [HomeController::class, 'appSettings']);

        // Wallet
        Route::prefix('wallet')->group(function () {
            Route::get('balance',           [WalletController::class, 'balance']);
            Route::get('transactions',      [WalletController::class, 'transactions']);
            Route::post('redeem-promo',     [WalletController::class, 'redeemPromoCode']);
            Route::post('withdraw',         [WalletController::class, 'requestWithdrawal']);
            Route::get('withdrawals',       [WalletController::class, 'withdrawalHistory']);
            Route::get('payment-methods',   [WalletController::class, 'paymentMethods']);
        });

        // Tasks
        Route::prefix('tasks')->group(function () {
            Route::get('/',                 [TaskController::class, 'index']);
            Route::get('{task}',            [TaskController::class, 'show']);
            Route::post('{task}/start',     [TaskController::class, 'start']);
            Route::post('{userTaskId}/complete', [TaskController::class, 'complete']);
        });

        // Spin & Earn
        Route::prefix('spin')->group(function () {
            Route::get('config',            [SpinController::class, 'getWheelConfig']);
            Route::post('spin',             [SpinController::class, 'spin']);
            Route::get('history',           [SpinController::class, 'history']);
        });

        // Scratch Cards
        Route::prefix('scratch')->group(function () {
            Route::post('issue',            [ScratchCardController::class, 'issue']);
            Route::post('{cardId}/scratch', [ScratchCardController::class, 'scratch']);
        });

        // Offerwalls
        Route::prefix('offerwalls')->group(function () {
            Route::get('/',                 [OfferwallController::class, 'index']);
            Route::get('{offerwall}/url',   [OfferwallController::class, 'getUrl']);
        });

        // Leaderboard
        Route::get('leaderboard',       [LeaderboardController::class, 'index']);

        // Chat
        Route::prefix('chat')->group(function () {
            Route::get('messages',          [ChatController::class, 'index']);
            Route::post('send',             [ChatController::class, 'send']);
        });
    });
});
