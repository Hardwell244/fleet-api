<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'plate' => strtoupper($this->faker->bothify('???####')),
            'brand' => $this->faker->randomElement(['Toyota', 'Volkswagen', 'Ford', 'Chevrolet', 'Fiat']),
            'model' => $this->faker->randomElement(['Corolla', 'Civic', 'Gol', 'Onix', 'HB20']),
            'year' => $this->faker->numberBetween(2015, 2024),
            'type' => $this->faker->randomElement(['car', 'truck', 'van', 'motorcycle']),
            'status' => $this->faker->randomElement(['available', 'in_use', 'maintenance', 'inactive']),
            'fuel_capacity' => $this->faker->randomFloat(2, 40, 100),
            'current_km' => $this->faker->numberBetween(0, 200000),
        ];
    }
}
