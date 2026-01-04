<?php

namespace Database\Factories;

use App\Models\DeliveryEvent;
use App\Models\Delivery;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryEventFactory extends Factory
{
    protected $model = DeliveryEvent::class;

    public function definition(): array
    {
        return [
            'delivery_id' => Delivery::factory(),
            'status' => $this->faker->randomElement(['pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed']),
            'observation' => $this->faker->optional()->sentence(),
            'latitude' => $this->faker->latitude(-33, 5),
            'longitude' => $this->faker->longitude(-73, -34),
            'created_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ];
    }

    public function withoutCoordinates(): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => null,
            'longitude' => null,
        ]);
    }
}
