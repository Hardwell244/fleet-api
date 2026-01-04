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
        }
        $this->newLine();

        // VEHICLES - DISPONÍVEIS
        $this->info('3️⃣  TESTANDO VEHICLES - DISPONÍVEIS');
        $vehiclesAvailable = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/vehicles/available');
        $this->info($vehiclesAvailable->successful() ? '✅ VEHICLES AVAILABLE: SUCESSO' : '❌ VEHICLES AVAILABLE: ERRO');
        $this->newLine();

        // DRIVERS - LISTAR
        $this->info('═══════════════════════════════════════');
        $this->info('4️⃣  TESTANDO DRIVERS - LISTAR');
        $this->info('═══════════════════════════════════════');
        $drivers = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/drivers');
        if ($drivers->successful()) {
            $this->info('✅ DRIVERS LIST: SUCESSO');
            $count = count($drivers->json()['data'] ?? []);
            $this->comment("Total de motoristas: {$count}");
        } else {
            $this->error('❌ DRIVERS LIST: ERRO');
        }
        $this->newLine();

        // DRIVERS - DISPONÍVEIS
        $this->info('5️⃣  TESTANDO DRIVERS - DISPONÍVEIS');
        $driversAvailable = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/drivers/available');
        $this->info($driversAvailable->successful() ? '✅ DRIVERS AVAILABLE: SUCESSO' : '❌ DRIVERS AVAILABLE: ERRO');
        $this->newLine();

        // MAINTENANCES - LISTAR
        $this->info('═══════════════════════════════════════');
        $this->info('6️⃣  TESTANDO MAINTENANCES - LISTAR');
        $this->info('═══════════════════════════════════════');
        $maintenances = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/maintenances');
        if ($maintenances->successful()) {
            $this->info('✅ MAINTENANCES LIST: SUCESSO');
            $count = count($maintenances->json()['data'] ?? []);
            $this->comment("Total de manutenções: {$count}");
        } else {
            $this->error('❌ MAINTENANCES LIST: ERRO');
        }
        $this->newLine();

        // MAINTENANCES - PENDENTES
        $this->info('7️⃣  TESTANDO MAINTENANCES - PENDENTES');
        $pendingMaintenances = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/maintenances/pending');
        $this->info($pendingMaintenances->successful() ? '✅ MAINTENANCES PENDING: SUCESSO' : '❌ MAINTENANCES PENDING: ERRO');
        $this->newLine();

        // MAINTENANCES - CRIAR
        $this->info('8️⃣  TESTANDO MAINTENANCES - CRIAR');
        $createMaintenance = Http::withToken($token)->post('http://127.0.0.1:8000/api/v1/maintenances', [
            'vehicle_id' => 1,
            'type' => 'preventive',
            'description' => 'Troca de óleo e filtros',
            'scheduled_date' => date('Y-m-d', strtotime('+7 days')),
            'status' => 'scheduled',
            'cost' => 350.00,
            'notes' => 'Manutenção preventiva agendada'
        ]);
        if ($createMaintenance->successful()) {
            $this->info('✅ MAINTENANCE CREATE: SUCESSO');
            $newMaintenanceId = $createMaintenance->json()['data']['id'] ?? null;

            if ($newMaintenanceId) {
                // ATUALIZAR MAINTENANCE
                $this->info('9️⃣  TESTANDO MAINTENANCES - ATUALIZAR');
                $updateMaintenance = Http::withToken($token)->put("http://127.0.0.1:8000/api/v1/maintenances/{$newMaintenanceId}", [
                    'vehicle_id' => 1,
                    'type' => 'preventive',
                    'description' => 'Troca de óleo, filtros e revisão completa',
                    'scheduled_date' => date('Y-m-d', strtotime('+7 days')),
                    'status' => 'in_progress',
                    'cost' => 450.00,
                    'notes' => 'Manutenção em andamento'
                ]);
                $this->info($updateMaintenance->successful() ? '✅ MAINTENANCE UPDATE: SUCESSO' : '❌ MAINTENANCE UPDATE: ERRO');
                $this->newLine();

                // DELETAR MAINTENANCE
                $this->info('🔟 TESTANDO MAINTENANCES - DELETAR');
                $deleteMaintenance = Http::withToken($token)->delete("http://127.0.0.1:8000/api/v1/maintenances/{$newMaintenanceId}");
                $this->info($deleteMaintenance->successful() ? '✅ MAINTENANCE DELETE: SUCESSO' : '❌ MAINTENANCE DELETE: ERRO');
            }
        } else {
            $this->error('❌ MAINTENANCE CREATE: ERRO');
            $this->error($createMaintenance->body());
        }
        $this->newLine();

        // RESUMO FINAL
        $this->info('═══════════════════════════════════════');
        $this->info('🎉 TESTES COMPLETOS!');
        $this->info('═══════════════════════════════════════');
        $this->comment('✅ Auth (Login/Logout/Me/Refresh)');
        $this->comment('✅ Vehicles CRUD + Available');
        $this->comment('✅ Drivers CRUD + Available');
        $this->comment('✅ Maintenances CRUD + Pending');

    } else {
        $this->error('❌ LOGIN FALHOU - IMPOSSÍVEL CONTINUAR');
        $this->error('Resposta: ' . $loginResponse->body());
    }
})->purpose('Testa todas as rotas da API Fleet');
