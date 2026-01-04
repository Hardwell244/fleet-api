<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:api', function () {
    $this->info('🔥 TESTANDO API FLEET COMPLETA...');
    $this->newLine();

    // LOGIN
    $this->info('═══════════════════════════════════════');
    $this->info('1️⃣  TESTANDO LOGIN');
    $this->info('═══════════════════════════════════════');

    $loginResponse = Http::post('http://127.0.0.1:8000/api/auth/login', [
        'email' => 'admin@logitech.com',
        'password' => 'password'
    ]);

    if ($loginResponse->successful()) {
        $this->info('✅ LOGIN: SUCESSO');
        $token = $loginResponse->json()['token'];  // ✅ CORRIGIDO AQUI
        $this->comment('Token: ' . substr($token, 0, 20) . '...');
        $this->newLine();

        // VEHICLES - LISTAR
        $this->info('═══════════════════════════════════════');
        $this->info('2️⃣  TESTANDO VEHICLES - LISTAR');
        $this->info('═══════════════════════════════════════');
        $vehicles = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/vehicles');
        if ($vehicles->successful()) {
            $this->info('✅ VEHICLES LIST: SUCESSO');
            $count = count($vehicles->json()['data'] ?? []);
            $this->comment("Total de veículos: {$count}");
        } else {
            $this->error('❌ VEHICLES LIST: ERRO');
            $this->error($vehicles->body());
        }
        $this->newLine();

        // VEHICLES - DISPONÍVEIS
        $this->info('3️⃣  TESTANDO VEHICLES - DISPONÍVEIS');
        $vehiclesAvailable = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/vehicles/available');
        if ($vehiclesAvailable->successful()) {
            $this->info('✅ VEHICLES AVAILABLE: SUCESSO');
        } else {
            $this->error('❌ VEHICLES AVAILABLE: ERRO');
        }
        $this->newLine();

        // VEHICLES - VER ESPECÍFICO
        $this->info('4️⃣  TESTANDO VEHICLES - VER ESPECÍFICO (ID 1)');
        $vehicle = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/vehicles/1');
        if ($vehicle->successful()) {
            $this->info('✅ VEHICLE SHOW: SUCESSO');
        } else {
            $this->error('❌ VEHICLE SHOW: ERRO');
        }
        $this->newLine();

        // DRIVERS - LISTAR
        $this->info('═══════════════════════════════════════');
        $this->info('5️⃣  TESTANDO DRIVERS - LISTAR');
        $this->info('═══════════════════════════════════════');
        $drivers = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/drivers');
        if ($drivers->successful()) {
            $this->info('✅ DRIVERS LIST: SUCESSO');
            $count = count($drivers->json()['data'] ?? []);
            $this->comment("Total de motoristas: {$count}");
        } else {
            $this->error('❌ DRIVERS LIST: ERRO');
            $this->error($drivers->body());
        }
        $this->newLine();

        // DRIVERS - DISPONÍVEIS
        $this->info('6️⃣  TESTANDO DRIVERS - DISPONÍVEIS');
        $driversAvailable = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/drivers/available');
        if ($driversAvailable->successful()) {
            $this->info('✅ DRIVERS AVAILABLE: SUCESSO');
        } else {
            $this->error('❌ DRIVERS AVAILABLE: ERRO');
        }
        $this->newLine();

        // DRIVERS - VER ESPECÍFICO
        $this->info('7️⃣  TESTANDO DRIVERS - VER ESPECÍFICO (ID 1)');
        $driver = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/drivers/1');
        if ($driver->successful()) {
            $this->info('✅ DRIVER SHOW: SUCESSO');
        } else {
            $this->error('❌ DRIVER SHOW: ERRO');
        }
        $this->newLine();

        // CRIAR VEHICLE
        $this->info('8️⃣  TESTANDO VEHICLES - CRIAR');
        $createVehicle = Http::withToken($token)->post('http://127.0.0.1:8000/api/v1/vehicles', [
            'plate' => 'TEST999',
            'brand' => 'Mercedes-Benz',
            'model' => 'Sprinter',
            'year' => 2024,
            'type' => 'van',
            'status' => 'available',
            'fuel_capacity' => 100,
            'current_km' => 0
        ]);
        if ($createVehicle->successful()) {
            $this->info('✅ VEHICLE CREATE: SUCESSO');
            $newVehicleId = $createVehicle->json()['data']['id'] ?? null;

            if ($newVehicleId) {
                // ATUALIZAR VEHICLE
                $this->info('9️⃣  TESTANDO VEHICLES - ATUALIZAR');
                $updateVehicle = Http::withToken($token)->put("http://127.0.0.1:8000/api/v1/vehicles/{$newVehicleId}", [
                    'plate' => 'TEST999',
                    'brand' => 'Mercedes-Benz',
                    'model' => 'Sprinter 415',
                    'year' => 2024,
                    'type' => 'van',
                    'status' => 'in_use',
                    'fuel_capacity' => 100,
                    'current_km' => 1500
                ]);
                if ($updateVehicle->successful()) {
                    $this->info('✅ VEHICLE UPDATE: SUCESSO');
                } else {
                    $this->error('❌ VEHICLE UPDATE: ERRO');
                }
                $this->newLine();

                // DELETAR VEHICLE
                $this->info('🔟 TESTANDO VEHICLES - DELETAR');
                $deleteVehicle = Http::withToken($token)->delete("http://127.0.0.1:8000/api/v1/vehicles/{$newVehicleId}");
                if ($deleteVehicle->successful()) {
                    $this->info('✅ VEHICLE DELETE: SUCESSO');
                } else {
                    $this->error('❌ VEHICLE DELETE: ERRO');
                }
            }
        } else {
            $this->error('❌ VEHICLE CREATE: ERRO');
            $this->error($createVehicle->body());
        }
        $this->newLine();

        // RESUMO FINAL
        $this->info('═══════════════════════════════════════');
        $this->info('🎉 TESTES COMPLETOS!');
        $this->info('═══════════════════════════════════════');
        $this->comment('Todas as rotas principais foram testadas.');
        $this->comment('Verifique os resultados acima.');

    } else {
        $this->error('❌ LOGIN FALHOU - IMPOSSÍVEL CONTINUAR');
        $this->error('Resposta: ' . $loginResponse->body());
    }
})->purpose('Testa todas as rotas da API Fleet');
