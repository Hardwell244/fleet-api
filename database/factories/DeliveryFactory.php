<?php

namespace Database\Factories;

use App\Models\Delivery;
use App\Models\Company;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => Driver::factory(),
            // tracking_code is auto-generated in model boot
            'status' => $this->faker->randomElement(['pending', 'assigned', 'in_transit', 'delivered', 'failed', 'cancelled']),

            // Origem
            'origin_address' => $this->faker->address(),
            'origin_lat' => $this->faker->latitude(-33, 5),
            'origin_lng' => $this->faker->longitude(-73, -34),

            // Destino
            'destination_address' => $this->faker->address(),
            'destination_lat' => $this->faker->latitude(-33, 5),
            'destination_lng' => $this->faker->longitude(-73, -34),

            // Métricas
            'distance_km' => $this->faker->randomFloat(2, 1, 500),
            'estimated_time_minutes' => $this->faker->numberBetween(10, 480),

            // Dados do cliente
            'recipient_name' => $this->faker->name(),
            'recipient_phone' => $this->faker->numerify('###########'),

            // Controle
            'assigned_at' => null,
            'picked_up_at' => null,
            'delivered_at' => null,
            'delivery_notes' => null,
        ];
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'delivered_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function inTransit(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_transit',
        ]);
    }
}
