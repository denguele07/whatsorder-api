<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/run-migrations-now', function () {
    Artisan::call('migrate', ['--force' => true]);

    return response()->json([
        'message' => 'Migrations executed',
        'output' => \Artisan::output(),
    ]);
});

Route::prefix('v1')->name('api.v1.')->group(function () {

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

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/orders/{order}/advance', [OrderController::class, 'advance'])->name('orders.advance');
        Route::apiResource('orders', OrderController::class);
    });
});
