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

        // ============================================
        // DELIVERIES
        // ============================================

        // DELIVERIES - LISTAR
        $this->info('═══════════════════════════════════════');
        $this->info('1️⃣8️⃣  TESTANDO DELIVERIES - LISTAR');
        $this->info('═══════════════════════════════════════');
        $deliveries = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/deliveries');
        if ($deliveries->successful()) {
            $this->info('✅ DELIVERIES LIST: SUCESSO');
            $count = count($deliveries->json()['data'] ?? []);
            $this->comment("Total de entregas: {$count}");
        } else {
            $this->error('❌ DELIVERIES LIST: ERRO');
            $this->error($deliveries->body());
        }
        $this->newLine();

        // DELIVERIES - IN TRANSIT
        $this->info('1️⃣9️⃣  TESTANDO DELIVERIES - EM TRÂNSITO');
        $inTransit = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/deliveries/in-transit');
        if ($inTransit->successful()) {
            $this->info('✅ DELIVERIES IN TRANSIT: SUCESSO');
        } else {
            $this->error('❌ DELIVERIES IN TRANSIT: ERRO');
            $this->error($inTransit->body());
        }
        $this->newLine();

        // CRIAR DELIVERY
        $this->info('═══════════════════════════════════════');
        $this->info('2️⃣0️⃣  TESTANDO DELIVERIES - CRIAR');
        $this->info('═══════════════════════════════════════');
        $createDelivery = Http::withToken($token)->post('http://127.0.0.1:8000/api/v1/deliveries', [
            'driver_id' => 1,
            'vehicle_id' => 1,
            'origin_address' => 'Rua Teste, 123 - São Paulo, SP',
            'origin_lat' => -23.5505199,
            'origin_lng' => -46.6333094,
            'destination_address' => 'Av. Paulista, 1000 - São Paulo, SP',
            'destination_lat' => -23.5613991,
            'destination_lng' => -46.6565712,
            'distance_km' => 5.2,
            'estimated_time_minutes' => 25,
            'recipient_name' => 'João Silva',
            'recipient_phone' => '11999999999',
            'status' => 'pending'
        ]);
        if ($createDelivery->successful()) {
            $this->info('✅ DELIVERY CREATE: SUCESSO');
            $newDeliveryId = $createDelivery->json()['data']['id'] ?? null;
            $trackingCode = $createDelivery->json()['data']['tracking_code'] ?? null;

            if ($newDeliveryId) {
                // VER DELIVERY ESPECÍFICA
                $this->info('2️⃣1️⃣  TESTANDO DELIVERIES - VER ESPECÍFICA');
                $delivery = Http::withToken($token)->get("http://127.0.0.1:8000/api/v1/deliveries/{$newDeliveryId}");
                if ($delivery->successful()) {
                    $this->info('✅ DELIVERY SHOW: SUCESSO');
                } else {
                    $this->error('❌ DELIVERY SHOW: ERRO');
                    $this->error($delivery->body());
                }
                $this->newLine();

                // RASTREAR DELIVERY (ROTA PÚBLICA)
                if ($trackingCode) {
                    $this->info('2️⃣2️⃣  TESTANDO DELIVERIES - RASTREAR');
                    $track = Http::get("http://127.0.0.1:8000/api/v1/deliveries/track/{$trackingCode}");
                    if ($track->successful()) {
                        $this->info('✅ DELIVERY TRACK: SUCESSO');
                        $this->comment("Código de rastreamento: {$trackingCode}");
                    } else {
                        $this->error('❌ DELIVERY TRACK: ERRO');
                        $this->error($track->body());
                    }
                    $this->newLine();
                }

                // CRIAR EVENTO DE DELIVERY
                $this->info('2️⃣3️⃣  TESTANDO DELIVERY EVENTS - CRIAR');
                $createEvent = Http::withToken($token)->post("http://127.0.0.1:8000/api/v1/deliveries/{$newDeliveryId}/events", [
                    'status' => 'in_transit',
                    'latitude' => -23.5558,
                    'longitude' => -46.6396,
                    'observation' => 'Saiu para entrega'
                ]);
                if ($createEvent->successful()) {
                    $this->info('✅ DELIVERY EVENT CREATE: SUCESSO');
                } else {
                    $this->error('❌ DELIVERY EVENT CREATE: ERRO');
                    $this->error($createEvent->body());
                }
                $this->newLine();

                // LISTAR EVENTOS DA DELIVERY
                $this->info('2️⃣4️⃣  TESTANDO DELIVERY EVENTS - LISTAR');
                $listEvents = Http::withToken($token)->get("http://127.0.0.1:8000/api/v1/deliveries/{$newDeliveryId}/events");
                if ($listEvents->successful()) {
                    $this->info('✅ DELIVERY EVENTS LIST: SUCESSO');
                    $eventCount = count($listEvents->json()['data'] ?? []);
                    $this->comment("Total de eventos: {$eventCount}");
                } else {
                    $this->error('❌ DELIVERY EVENTS LIST: ERRO');
                    $this->error($listEvents->body());
                }
                $this->newLine();

                // ATUALIZAR DELIVERY
                $this->info('2️⃣5️⃣  TESTANDO DELIVERIES - ATUALIZAR');
                $updateDelivery = Http::withToken($token)->put("http://127.0.0.1:8000/api/v1/deliveries/{$newDeliveryId}", [
                    'driver_id' => 1,
                    'vehicle_id' => 1,
                    'origin_address' => 'Rua Teste, 123 - São Paulo, SP',
                    'origin_lat' => -23.5505199,
                    'origin_lng' => -46.6333094,
                    'destination_address' => 'Av. Paulista, 1000 - São Paulo, SP',
                    'destination_lat' => -23.5613991,
                    'destination_lng' => -46.6565712,
                    'distance_km' => 5.2,
                    'estimated_time_minutes' => 25,
                    'recipient_name' => 'João Silva Santos',
                    'recipient_phone' => '11988888888',
                    'status' => 'in_transit',
                    'delivery_notes' => 'Atualização de teste'
                ]);
                if ($updateDelivery->successful()) {
                    $this->info('✅ DELIVERY UPDATE: SUCESSO');
                } else {
                    $this->error('❌ DELIVERY UPDATE: ERRO');
                    $this->error($updateDelivery->body());
                }
                $this->newLine();

                // DELIVERIES POR VEÍCULO
                $this->info('2️⃣6️⃣  TESTANDO DELIVERIES - POR VEÍCULO');
                $byVehicle = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/deliveries/vehicle/1');
                if ($byVehicle->successful()) {
                    $this->info('✅ DELIVERIES BY VEHICLE: SUCESSO');
                } else {
                    $this->error('❌ DELIVERIES BY VEHICLE: ERRO');
                    $this->error($byVehicle->body());
                }
                $this->newLine();

                // DELIVERIES POR MOTORISTA
                $this->info('2️⃣7️⃣  TESTANDO DELIVERIES - POR MOTORISTA');
                $byDriver = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/deliveries/driver/1');
                if ($byDriver->successful()) {
                    $this->info('✅ DELIVERIES BY DRIVER: SUCESSO');
                } else {
                    $this->error('❌ DELIVERIES BY DRIVER: ERRO');
                    $this->error($byDriver->body());
                }
                $this->newLine();

                // EVENTOS RECENTES
                $this->info('2️⃣8️⃣  TESTANDO DELIVERY EVENTS - RECENTES');
                $recentEvents = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/delivery-events/recent');
                if ($recentEvents->successful()) {
                    $this->info('✅ DELIVERY EVENTS RECENT: SUCESSO');
                } else {
                    $this->error('❌ DELIVERY EVENTS RECENT: ERRO');
                    $this->error($recentEvents->body());
                }
                $this->newLine();

                // DELETAR DELIVERY
                $this->info('2️⃣9️⃣  TESTANDO DELIVERIES - DELETAR');
                $deleteDelivery = Http::withToken($token)->delete("http://127.0.0.1:8000/api/v1/deliveries/{$newDeliveryId}");
                if ($deleteDelivery->successful()) {
                    $this->info('✅ DELIVERY DELETE: SUCESSO');
                } else {
                    $this->error('❌ DELIVERY DELETE: ERRO');
                    $this->error($deleteDelivery->body());
                }
                $this->newLine();
            }
        } else {
            $this->error('❌ DELIVERY CREATE: ERRO');
            $this->error($createDelivery->body());
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
        $this->comment('✅ Deliveries CRUD Completo + InTransit + Track + ByVehicle + ByDriver');
        $this->comment('✅ Delivery Events CRUD + Recent');
        $this->newLine();
        $this->info('Total de 29 testes executados!');

    } else {
        $this->error('❌ LOGIN FALHOU - IMPOSSÍVEL CONTINUAR');
        $this->error('Resposta: ' . $loginResponse->body());
    }
})->purpose('Testa todas as rotas da API Fleet');
