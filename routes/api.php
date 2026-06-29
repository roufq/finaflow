<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BudgetApiController;
use App\Http\Controllers\Api\EmailWebhookController;
use App\Http\Controllers\Api\GoalApiController;
use App\Http\Controllers\Api\MobileController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\TransactionApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// API Version 1
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/login', [AuthController::class, 'login']);

    // Public Webhooks
    Route::post('/webhooks/email-parser', [EmailWebhookController::class, 'handle']);

    // Protected Endpoints (Requires Sanctum Token)
    Route::middleware('auth:sanctum')->group(function () {
        // User & Profile
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::prefix('profile')->group(function () {
            Route::put('/update', [ProfileApiController::class, 'update']);
            Route::put('/password', [ProfileApiController::class, 'updatePassword']);
            Route::post('/avatar', [ProfileApiController::class, 'updateAvatar']);
        });

        Route::post('/logout', [AuthController::class, 'logout']);

        // Transactions (Full CRUD + OCR)
        Route::apiResource('transactions', TransactionApiController::class);
        Route::post('transactions/scan-receipt', [TransactionApiController::class, 'scanReceipt']);

        // Financial Planning
        Route::apiResource('goals', GoalApiController::class);
        Route::post('goals/{goal}/progress', [GoalApiController::class, 'updateProgress']);

        Route::apiResource('budgets', BudgetApiController::class)->only(['index', 'show']);
        Route::patch('budgets/{budget}/spent', [BudgetApiController::class, 'updateSpent']);

        // Metadata for Mobile Forms
        Route::get('/categories', [MobileController::class, 'categories']);
        Route::get('/accounts', [MobileController::class, 'accounts']);

        // Dashboard/Summary
        Route::get('/dashboard', [MobileController::class, 'dashboard']);
    });
});

// Root fallback for quick access (Redirect or simple response)
Route::get('/', function () {
    return response()->json([
        'app' => config('app.name'),
        'version' => '1.0.0',
        'api_version' => 'v1',
    ]);
});
