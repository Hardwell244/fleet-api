<?php

namespace Tests\Feature\Api;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_list_their_company_vehicles()
    {
        $user = $this->authenticatedUser();

        Vehicle::factory()->count(3)->create(['company_id' => $user->company_id]);
        Vehicle::factory()->count(2)->create(); // Outra empresa

        $response = $this->getJson('/api/v1/vehicles');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function user_can_create_vehicle()
    {
        $user = $this->authenticatedUser();

        $data = [
            'plate' => 'ABC1234',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2023,
            'type' => 'car',
            'status' => 'available',
            'fuel_capacity' => 50,
            'current_km' => 0,
        ];

        $response = $this->postJson('/api/v1/vehicles', $data);

        $response->assertStatus(201);
        $response->assertJsonPath('data.plate', 'ABC1234');
        $this->assertDatabaseHas('vehicles', [
            'plate' => 'ABC1234',
            'company_id' => $user->company_id,
        ]);
    }

    /** @test */
    public function user_can_update_their_vehicle()
    {
        $user = $this->authenticatedUser();
        $vehicle = Vehicle::factory()->create(['company_id' => $user->company_id]);

        $response = $this->putJson("/api/v1/vehicles/{$vehicle->id}", [
            'plate' => $vehicle->plate,
            'brand' => $vehicle->brand,
            'model' => 'Updated Model',
            'year' => $vehicle->year,
            'type' => $vehicle->type,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'model' => 'Updated Model',
        ]);
    }

    /** @test */
    public function user_can_delete_their_vehicle()
    {
        $user = $this->authenticatedUser();
        $vehicle = Vehicle::factory()->create(['company_id' => $user->company_id]);

        $response = $this->deleteJson("/api/v1/vehicles/{$vehicle->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_vehicles()
    {
        $response = $this->getJson('/api/v1/vehicles');

        $response->assertStatus(401);
    }

    /** @test */
    public function plate_must_be_unique()
    {
        $user = $this->authenticatedUser();
        Vehicle::factory()->create(['plate' => 'ABC1234', 'company_id' => $user->company_id]);

        $response = $this->postJson('/api/v1/vehicles', [
            'plate' => 'ABC1234',
            'brand' => 'Ford',
            'model' => 'Focus',
            'year' => 2023,
            'type' => 'car',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('plate');
    }
}
