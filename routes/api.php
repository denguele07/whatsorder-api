<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Version 1
|--------------------------------------------------------------------------
|
| Préfixe automatique : /api/v1/
|
*/

Route::get('/setup-database-secret-xyz', function() {
    try {
        Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = Artisan::output();

        Artisan::call('db:seed', ['--force' => true]);
        $seedOutput = Artisan::output();

        return response()->json([
            'success' => true,
            'migrate' => $migrateOutput,
            'seed' => $seedOutput,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
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
