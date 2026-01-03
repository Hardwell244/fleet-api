<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rota de teste pública (SEM autenticação)
Route::get('/test', function () {
    return response()->json([
        'message' => 'Fleet API está funcionando!',
        'version' => '1.0.0',
        'timestamp' => now()->toDateTimeString(),
    ]);
});

// Rota protegida - retorna usuário autenticado
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user(),
        'company' => $request->user()->company,
    ]);
});

// Grupo de rotas API v1 - Públicas
Route::prefix('v1')->group(function () {
    // Rotas de autenticação (serão criadas depois)
    // Route::post('/login', [AuthController::class, 'login']);
    // Route::post('/register', [AuthController::class, 'register']);
});

// Grupo de rotas API v1 - Protegidas
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Rotas de recursos (serão criadas depois)
    // Route::apiResource('vehicles', VehicleController::class);
    // Route::apiResource('drivers', DriverController::class);
    // Route::apiResource('deliveries', DeliveryController::class);
});
