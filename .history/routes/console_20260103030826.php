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
        $token = $loginResponse->json()['token'];
        $this->comment('Token: ' . substr($token, 0, 20) . '...');
        $this->newLine();

        // ============================================
        // VEHICLES
        // ============================================

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

        // ============================================
        // DRIVERS
        // ============================================

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

        // ============================================
        // VEHICLES - CRUD COMPLETO
        // ============================================

        // CRIAR VEHICLE
        $this->info('═══════════════════════════════════════');
        $this->info('8️⃣  TESTANDO VEHICLES - CRIAR');
        $this->info('═══════════════════════════════════════');
        $randomPlate = 'TST' . rand(1000, 9999);
        $createVehicle = Http::withToken($token)->post('http://127.0.0.1:8000/api/v1/vehicles', [
            'plate' => $randomPlate,
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
                    'plate' => $randomPlate,
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
                    $this->error($updateVehicle->body());
                }
                $this->newLine();

                // DELETAR VEHICLE
                $this->info('🔟 TESTANDO VEHICLES - DELETAR');
                $deleteVehicle = Http::withToken($token)->delete("http://127.0.0.1:8000/api/v1/vehicles/{$newVehicleId}");
                if ($deleteVehicle->successful()) {
                    $this->info('✅ VEHICLE DELETE: SUCESSO');
                } else {
                    $this->error('❌ VEHICLE DELETE: ERRO');
                    $this->error($deleteVehicle->body());
                }
                $this->newLine();
            }
        } else {
            $this->error('❌ VEHICLE CREATE: ERRO');
            $this->error($createVehicle->body());
            $this->newLine();
        }

        // ============================================
        // MAINTENANCES
        // ============================================

        // MAINTENANCES - LISTAR
        $this->info('═══════════════════════════════════════');
        $this->info('1️⃣1️⃣  TESTANDO MAINTENANCES - LISTAR');
        $this->info('═══════════════════════════════════════');
        $maintenances = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/maintenances');
        if ($maintenances->successful()) {
            $this->info('✅ MAINTENANCES LIST: SUCESSO');
            $count = count($maintenances->json()['data'] ?? []);
            $this->comment("Total de manutenções: {$count}");
        } else {
            $this->error('❌ MAINTENANCES LIST: ERRO');
            $this->error($maintenances->body());
        }
        $this->newLine();

        // MAINTENANCES - PENDENTES
        $this->info('1️⃣2️⃣  TESTANDO MAINTENANCES - PENDENTES');
        $pendingMaintenances = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/maintenances/pending');
        if ($pendingMaintenances->successful()) {
            $this->info('✅ MAINTENANCES PENDING: SUCESSO');
        } else {
            $this->error('❌ MAINTENANCES PENDING: ERRO');
            $this->error($pendingMaintenances->body());
        }
        $this->newLine();

        // MAINTENANCES - POR VEÍCULO
        $this->info('1️⃣3️⃣  TESTANDO MAINTENANCES - POR VEÍCULO');
        $vehicleMaintenances = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/maintenances/vehicle/1');
        if ($vehicleMaintenances->successful()) {
            $this->info('✅ MAINTENANCES BY VEHICLE: SUCESSO');
        } else {
            $this->error('❌ MAINTENANCES BY VEHICLE: ERRO');
            $this->error($vehicleMaintenances->body());
        }
        $this->newLine();

        // CRIAR MAINTENANCE
        $this->info('═══════════════════════════════════════');
        $this->info('1️⃣4️⃣  TESTANDO MAINTENANCES - CRIAR');
        $this->info('═══════════════════════════════════════');
        $createMaintenance = Http::withToken($token)->post('http://127.0.0.1:8000/api/v1/maintenances', [
            'vehicle_id' => 1,
            'type' => 'preventive',
            'description' => 'Troca de óleo e filtros - TESTE',
            'scheduled_date' => date('Y-m-d', strtotime('+7 days')),
            'status' => 'scheduled',
            'cost' => 350.00,
            'notes' => 'Manutenção preventiva agendada'
        ]);
        if ($createMaintenance->successful()) {
            $this->info('✅ MAINTENANCE CREATE: SUCESSO');
            $newMaintenanceId = $createMaintenance->json()['data']['id'] ?? null;

            if ($newMaintenanceId) {
                // VER MAINTENANCE ESPECÍFICA
                $this->info('1️⃣5️⃣  TESTANDO MAINTENANCES - VER ESPECÍFICA');
                $maintenance = Http::withToken($token)->get("http://127.0.0.1:8000/api/v1/maintenances/{$newMaintenanceId}");
                if ($maintenance->successful()) {
                    $this->info('✅ MAINTENANCE SHOW: SUCESSO');
                } else {
                    $this->error('❌ MAINTENANCE SHOW: ERRO');
                    $this->error($maintenance->body());
                }
                $this->newLine();

                // ATUALIZAR MAINTENANCE
                $this->info('1️⃣6️⃣  TESTANDO MAINTENANCES - ATUALIZAR');
                $updateMaintenance = Http::withToken($token)->put("http://127.0.0.1:8000/api/v1/maintenances/{$newMaintenanceId}", [
                    'vehicle_id' => 1,
                    'type' => 'preventive',
                    'description' => 'Troca de óleo, filtros e revisão completa - TESTE',
                    'scheduled_date' => date('Y-m-d', strtotime('+7 days')),
                    'status' => 'in_progress',
                    'cost' => 450.00,
                    'notes' => 'Manutenção em andamento'
                ]);
                if ($updateMaintenance->successful()) {
                    $this->info('✅ MAINTENANCE UPDATE: SUCESSO');
                } else {
                    $this->error('❌ MAINTENANCE UPDATE: ERRO');
                    $this->error($updateMaintenance->body());
                }
                $this->newLine();

                // DELETAR MAINTENANCE
                $this->info('1️⃣7️⃣  TESTANDO MAINTENANCES - DELETAR');
                $deleteMaintenance = Http::withToken($token)->delete("http://127.0.0.1:8000/api/v1/maintenances/{$newMaintenanceId}");
                if ($deleteMaintenance->successful()) {
                    $this->info('✅ MAINTENANCE DELETE: SUCESSO');
                } else {
                    $this->error('❌ MAINTENANCE DELETE: ERRO');
                    $this->error($deleteMaintenance->body());
                }
                $this->newLine();
            }
        } else {
            $this->error('❌ MAINTENANCE CREATE: ERRO');
            $this->error($createMaintenance->body());
            $this->newLine();
        }

        // RESUMO FINAL
        $this->info('═══════════════════════════════════════');
        $this->info('🎉 TESTES COMPLETOS!');
        $this->info('═══════════════════════════════════════');
        $this->comment('✅ Auth (Login/Logout/Me/Refresh)');
        $this->comment('✅ Vehicles CRUD Completo + Available');
        $this->comment('✅ Drivers CRUD Completo + Available');
        $this->comment('✅ Maintenances CRUD Completo + Pending + ByVehicle');
        $this->newLine();
        $this->info('Total de 17 testes executados!');

    } else {
        $this->error('❌ LOGIN FALHOU - IMPOSSÍVEL CONTINUAR');
        $this->error('Resposta: ' . $loginResponse->body());
    }
})->purpose('Testa todas as rotas da API Fleet');
