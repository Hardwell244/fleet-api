<?php

namespace Tests\Feature\Api;

use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_list_their_company_deliveries()
    {
        $user = $this->authenticatedUser();

        Delivery::factory()->count(3)->create(['company_id' => $user->company_id]);
        Delivery::factory()->count(2)->create(); // Outra empresa

        $response = $this->getJson('/api/v1/deliveries');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function user_can_create_delivery()
    {
        $user = $this->authenticatedUser();
        $vehicle = Vehicle::factory()->create(['company_id' => $user->company_id]);
        $driver = Driver::factory()->create(['company_id' => $user->company_id]);

        $data = [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'origin_address' => 'Rua A, 123',
            'origin_lat' => -23.5505199,
            'origin_lng' => -46.6333094,
            'destination_address' => 'Rua B, 456',
            'destination_lat' => -23.5613991,
            'destination_lng' => -46.6565712,
            'recipient_name' => 'João Silva',
            'recipient_phone' => '11999999999',
            'status' => 'pending',
        ];

        $response = $this->postJson('/api/v1/deliveries', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['data' => ['tracking_code']]);
        $this->assertDatabaseHas('deliveries', [
            'company_id' => $user->company_id,
            'recipient_name' => 'João Silva',
        ]);
    }

    /** @test */
    public function tracking_code_is_generated_automatically()
    {
        $user = $this->authenticatedUser();
        $delivery = Delivery::factory()->create(['company_id' => $user->company_id]);

        $this->assertNotNull($delivery->tracking_code);
        $this->assertStringStartsWith('FLT', $delivery->tracking_code);
    }

    /** @test */
    public function public_can_track_delivery_by_code()
    {
        $delivery = Delivery::factory()->create();

        $response = $this->getJson("/api/v1/deliveries/track/{$delivery->tracking_code}");

        $response->assertStatus(200);
        $response->assertJsonPath('data.tracking_code', $delivery->tracking_code);
    }

    /** @test */
    public function invalid_tracking_code_returns_404()
    {
        $response = $this->getJson('/api/v1/deliveries/track/INVALID123');

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_filter_deliveries_in_transit()
    {
        $user = $this->authenticatedUser();

        Delivery::factory()->create([
            'company_id' => $user->company_id,
            'status' => 'in_transit',
        ]);

        Delivery::factory()->create([
            'company_id' => $user->company_id,
            'status' => 'delivered',
        ]);

        $response = $this->getJson('/api/v1/deliveries/in-transit');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
}
