<?php

namespace Database\Factories;

use App\Models\Maintenance;
use App\Models\Vehicle;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceFactory extends Factory
{
    protected $model = Maintenance::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'vehicle_id' => Vehicle::factory(),
            'type' => $this->faker->randomElement(['preventive', 'corrective', 'inspection']),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['scheduled', 'in_progress', 'completed', 'cancelled']),
            'scheduled_date' => $this->faker->dateTimeBetween('now', '+90 days'),
            'completed_date' => null,
            'cost' => $this->faker->randomFloat(2, 100, 5000),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'scheduled',
            'completed_date' => null,
        ]);
    }
}
