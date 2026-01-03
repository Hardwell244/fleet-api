<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'company_id' => 1,
                'plate' => 'ABC1234',
                'brand' => 'Fiat',
                'model' => 'Fiorino',
                'year' => 2022,
                'type' => 'van',
                'status' => 'available',
                'fuel_capacity' => 50.00,
                'current_km' => 15000.00,
            ],
            [
                'company_id' => 1,
                'plate' => 'DEF5678',
                'brand' => 'Honda',
                'model' => 'CG 160',
                'year' => 2023,
                'type' => 'motorcycle',
                'status' => 'available',
                'fuel_capacity' => 12.00,
                'current_km' => 5000.00,
            ],
            [
                'company_id' => 1,
                'plate' => 'GHI9012',
                'brand' => 'Mercedes',
                'model' => 'Sprinter',
                'year' => 2021,
                'type' => 'van',
                'status' => 'in_use',
                'fuel_capacity' => 70.00,
                'current_km' => 45000.00,
            ],
            [
                'company_id' => 2,
                'plate' => 'JKL3456',
                'brand' => 'Volkswagen',
                'model' => 'Delivery',
                'year' => 2020,
                'type' => 'truck',
                'status' => 'available',
                'fuel_capacity' => 120.00,
                'current_km' => 80000.00,
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
