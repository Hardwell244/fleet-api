<?php

namespace Tests\Feature\Api;

use App\Models\Maintenance;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_list_their_company_maintenances()
    {
        $user = $this->authenticatedUser();
        $vehicle = Vehicle::factory()->create(['company_id' => $user->company_id]);

        Maintenance::factory()->count(3)->create([
            'company_id' => $user->company_id,
            'vehicle_id' => $vehicle->id,
        ]);
        Maintenance::factory()->count(2)->create(); // Outra empresa

        $response = $this->getJson('/api/v1/maintenances');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function user_can_create_maintenance()
    {
        $user = $this->authenticatedUser();
        $vehicle = Vehicle::factory()->create(['company_id' => $user->company_id]);

        $data = [
            'vehicle_id' => $vehicle->id,
            'type' => 'preventive',
            'description' => 'Troca de óleo',
            'scheduled_date' => now()->addDays(7)->format('Y-m-d'),
            'cost' => 350.00,
            'status' => 'scheduled',
        ];

        $response = $this->postJson('/api/v1/maintenances', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('maintenances', [
            'vehicle_id' => $vehicle->id,
            'company_id' => $user->company_id,
            'type' => 'preventive',
        ]);
    }

    /** @test */
    public function user_can_filter_pending_maintenances()
    {
        $user = $this->authenticatedUser();
        $vehicle = Vehicle::factory()->create(['company_id' => $user->company_id]);

        Maintenance::factory()->create([
            'company_id' => $user->company_id,
            'vehicle_id' => $vehicle->id,
            'status' => 'scheduled',
        ]);

        Maintenance::factory()->create([
            'company_id' => $user->company_id,
            'vehicle_id' => $vehicle->id,
            'status' => 'completed',
        ]);

        $response = $this->getJson('/api/v1/maintenances/pending');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function scheduled_date_must_be_in_future_for_new_maintenance()
    {
        $user = $this->authenticatedUser();
        $vehicle = Vehicle::factory()->create(['company_id' => $user->company_id]);

        $response = $this->postJson('/api/v1/maintenances', [
            'vehicle_id' => $vehicle->id,
            'type' => 'preventive',
            'description' => 'Teste',
            'scheduled_date' => now()->subDays(1)->format('Y-m-d'),
        ]);

        $response->assertStatus(422);
    }
}
