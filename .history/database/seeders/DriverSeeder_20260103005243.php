<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            [
                'company_id' => 1,
                'name' => 'João Silva',
                'cpf' => '12345678901',
                'cnh' => '12345678901',
                'cnh_category' => 'B',
                'cnh_expires_at' => now()->addYears(2),
                'phone' => '11987654321',
                'is_available' => true,
            ],
            [
                'company_id' => 1,
                'name' => 'Maria Santos',
                'cpf' => '98765432109',
                'cnh' => '98765432109',
                'cnh_category' => 'AB',
                'cnh_expires_at' => now()->addYears(3),
                'phone' => '11912345678',
                'is_available' => true,
            ],
            [
                'company_id' => 1,
                'name' => 'Pedro Oliveira',
                'cpf' => '45678912301',
                'cnh' => '45678912301',
                'cnh_category' => 'D',
                'cnh_expires_at' => now()->addYear(),
                'phone' => '11998877665',
                'is_available' => false,
            ],
            [
                'company_id' => 2,
                'name' => 'Carlos Souza',
                'cpf' => '78912345601',
                'cnh' => '78912345601',
                'cnh_category' => 'E',
                'cnh_expires_at' => now()->addMonths(6),
                'phone' => '11955443322',
                'is_available' => true,
            ],
        ];

        foreach ($drivers as $driver) {
            Driver::create($driver);
        }
    }
}
