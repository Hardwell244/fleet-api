<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DriverController;

/*
|--------------------------------------------------------------------------
| API Routes - Fleet Management System
|--------------------------------------------------------------------------
*/

// ========== ROTAS PÚBLICAS (SEM AUTENTICAÇÃO) ==========

// Health Check
Route::get('/test', function () {
    return response()->json([
        'message' => 'Fleet API está funcionando!',
        'version' => '1.0.0',
        'timestamp' => now()->toDateTimeString(),
        'database' => 'Connected',
    ]);
});

// Autenticação
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// ========== ROTAS PROTEGIDAS (REQUER AUTENTICAÇÃO) ==========

Route::middleware(['auth:sanctum'])->group(function () {

    // Autenticação
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // Rota de teste protegida
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
            'company' => $request->user()->company,
        ]);
    });

    // Rotas de Motoristas
    Route::prefix('v1/drivers')->group(function () {
        Route::get('/', [DriverController::class, 'index']);
        Route::get('/available', [DriverController::class, 'available']);
        Route::get('/{id}', [DriverController::class, 'show']);
        Route::post('/', [DriverController::class, 'store']);
        Route::put('/{id}', [DriverController::class, 'update']);
        Route::delete('/{id}', [DriverController::class, 'destroy']);
    });


    // ========== ROTAS DE RECURSOS (v1) ==========
    Route::prefix('v1')->group(function () {
        // Vehicles
        Route::apiResource('vehicles', VehicleController::class);
    });
});
