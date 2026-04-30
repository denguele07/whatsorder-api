<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/force-clear-cache-now', function () {
    Artisan::call('optimize:clear');

    return response()->json([
        'message' => 'Cache cleared',
    ]);
});

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Auth
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('/me', function (Request $request) {
                return response()->json($request->user());
            })->name('me');
        });
    });

    // Orders (protégées)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/orders/{order}/advance', [OrderController::class, 'advance'])->name('orders.advance');
        Route::apiResource('orders', OrderController::class);
    });

});
