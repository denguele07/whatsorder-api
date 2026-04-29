<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Version 1
|--------------------------------------------------------------------------
|
| Préfixe automatique : /api/v1/
|
*/
use Illuminate\Support\Facades\DB;

Route::get('/db-test', function() {
    try {
        $count = DB::table('users')->count();
        return response()->json([
            'success' => true,
            'users_count' => $count,
            'connection' => config('database.default'),
            'host' => config('database.connections.mysql.host'),
            'database' => config('database.connections.mysql.database'),
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});

Route::prefix('v1')->name('api.v1.')->group(function () {

        // ROUTES PUBLIQUES (pas d'authentification)

        Route::prefix('auth')->name('auth.')->group(function () {
            Route::post('register', [AuthController::class, 'register'])->name('register');
            Route::post('login',    [AuthController::class, 'login'])->name('login');
        });


        // ROUTES PROTÉGÉES (auth:sanctum requis)

        Route::middleware('auth:sanctum')->group(function () {

            // Auth
            Route::post('auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

            // User
            Route::get('user', fn (Request $request) => $request->user())->name('user');

            // Commandes — route custom avant apiResource
            Route::post('orders/{order}/advance', [OrderController::class, 'advance'])->name('orders.advance');

            Route::apiResource('orders', OrderController::class);
        });
    });
