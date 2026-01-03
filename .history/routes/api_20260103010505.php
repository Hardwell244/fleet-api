<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

    // ========== ROTAS DE RECURSOS (v1) ==========
    Route::prefix('v1')->group(function () {
        // Vehicles, Drivers, Deliveries, etc. (serão criadas depois)
    });
});
```

---

### **PASSO 6: Testar Autenticação**

#### **🔐 Teste 1: LOGIN (Postman ou Insomnia)**

**Método:** `POST`
**URL:** `http://localhost:8000/api/auth/login`
**Headers:**
```
Content-Type: application/json
Accept: application/json
