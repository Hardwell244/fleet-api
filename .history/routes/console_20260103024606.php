<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('test:api', function () {
    $this->info('🔥 TESTANDO API COMPLETA...');

    // LOGIN
    $this->info('1. Testando Login...');
    $loginResponse = Http::post('http://127.0.0.1:8000/api/auth/login', [
        'email' => 'admin@logitech.com',
        'password' => 'password'
    ]);

    if ($loginResponse->successful()) {
        $this->info('✅ LOGIN OK');
        $token = $loginResponse->json()['data']['token'];

        // VEHICLES
        $this->info('2. Testando Vehicles...');
        $vehicles = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/vehicles');
        $this->info($vehicles->successful() ? '✅ VEHICLES OK' : '❌ VEHICLES ERRO');

        // DRIVERS
        $this->info('3. Testando Drivers...');
        $drivers = Http::withToken($token)->get('http://127.0.0.1:8000/api/v1/drivers');
        $this->info($drivers->successful() ? '✅ DRIVERS OK' : '❌ DRIVERS ERRO');

        $this->info('🎉 TESTES COMPLETOS!');
    } else {
        $this->error('❌ LOGIN FALHOU');
    }
});
