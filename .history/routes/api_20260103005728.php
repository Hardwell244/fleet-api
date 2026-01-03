<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas públicas (sem autenticação)
Route::prefix('v1')->group(function () {
    // Auth routes serão adicionadas depois
});

// Rotas protegidas (com autenticação)
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    // Rotas da API serão adicionadas depois
});
