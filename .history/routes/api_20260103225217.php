<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\DeliveryEventController;
use Illuminate\Support\Facades\Route;


// Rota pública de teste
Route::get('/test', function () {
    return response()->json([
        'message' => 'Fleet API está funcionando!',
        'version' => '1.0.0',
        'timestamp' => now()->format('Y-m-d H:i:s'),
        'database' => 'Connected'
    ]);
});

// Rotas de Autenticação (públicas)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Rota pública de rastreamento
Route::get('/v1/deliveries/track/{trackingCode}', [DeliveryController::class, 'track']);

// Rotas protegidas por autenticação
Route::middleware('auth:sanctum')->group(function () {

    // Auth protegidas
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // User info
    Route::get('/user', function () {
        return response()->json(auth()->user());
    });

    // Rotas de Veículos
    Route::prefix('v1/vehicles')->group(function () {
        Route::get('/', [VehicleController::class, 'index']);
        Route::get('/available', [VehicleController::class, 'available']);
        Route::get('/{id}', [VehicleController::class, 'show']);
        Route::post('/', [VehicleController::class, 'store']);
        Route::put('/{id}', [VehicleController::class, 'update']);
        Route::patch('/{id}', [VehicleController::class, 'update']);
        Route::delete('/{id}', [VehicleController::class, 'destroy']);
    });

    // Rotas de Motoristas
    Route::prefix('v1/drivers')->group(function () {
        Route::get('/', [DriverController::class, 'index']);
        Route::get('/available', [DriverController::class, 'available']);
        Route::get('/{id}', [DriverController::class, 'show']);
        Route::post('/', [DriverController::class, 'store']);
        Route::put('/{id}', [DriverController::class, 'update']);
        Route::patch('/{id}', [DriverController::class, 'update']);
        Route::delete('/{id}', [DriverController::class, 'destroy']);
    });

    // Rotas de Manutenções
    Route::prefix('v1/maintenances')->group(function () {
        Route::get('/', [MaintenanceController::class, 'index']);
        Route::get('/pending', [MaintenanceController::class, 'pending']);
        Route::get('/vehicle/{vehicleId}', [MaintenanceController::class, 'byVehicle']);
        Route::get('/{id}', [MaintenanceController::class, 'show']);
        Route::post('/', [MaintenanceController::class, 'store']);
        Route::put('/{id}', [MaintenanceController::class, 'update']);
        Route::patch('/{id}', [MaintenanceController::class, 'update']);
        Route::delete('/{id}', [MaintenanceController::class, 'destroy']);
    });

    // Rotas de Entregas
    Route::prefix('v1/deliveries')->group(function () {
        Route::get('/', [DeliveryController::class, 'index']);
        Route::get('/in-transit', [DeliveryController::class, 'inTransit']);
        Route::get('/vehicle/{vehicleId}', [DeliveryController::class, 'byVehicle']);
        Route::get('/driver/{driverId}', [DeliveryController::class, 'byDriver']);
        Route::get('/{id}', [DeliveryController::class, 'show']);
        Route::post('/', [DeliveryController::class, 'store']);
        Route::put('/{id}', [DeliveryController::class, 'update']);
        Route::patch('/{id}', [DeliveryController::class, 'update']);
        Route::delete('/{id}', [DeliveryController::class, 'destroy']);

        // Eventos de entrega
        Route::get('/{deliveryId}/events', [DeliveryEventController::class, 'index']);
        Route::post('/{deliveryId}/events', [DeliveryEventController::class, 'store']);
    });

    // Eventos recentes
    Route::get('/v1/delivery-events/recent', [DeliveryEventController::class, 'recent']);

});
