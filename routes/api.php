<?php

use App\Http\Controllers\Api\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;

// Auth routes
Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('/products', ProductController::class);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('/cart', CartController::class);
    Route::post('/transactions', [TransactionController::class, 'store']);
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show']);
});
