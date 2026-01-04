<?php

namespace Tests\Security;

use App\Models\Company;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Maintenance;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MultiTenancyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_cannot_view_vehicle_from_another_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $user = User::factory()->create(['company_id' => $company1->id]);
        Sanctum::actingAs($user);

        $vehicle = Vehicle::factory()->create(['company_id' => $company2->id]);

        $response = $this->getJson("/api/v1/vehicles/{$vehicle->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_update_vehicle_from_another_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $user = User::factory()->create(['company_id' => $company1->id]);
        Sanctum::actingAs($user);

        $vehicle = Vehicle::factory()->create(['company_id' => $company2->id]);

        $response = $this->putJson("/api/v1/vehicles/{$vehicle->id}", [
            'plate' => 'XYZ9999',
            'brand' => 'Hacked',
            'model' => 'Hacked',
            'year' => 2023,
            'type' => 'car',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('vehicles', ['brand' => 'Hacked']);
    }

    /** @test */
    public function user_cannot_delete_vehicle_from_another_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $user = User::factory()->create(['company_id' => $company1->id]);
        Sanctum::actingAs($user);

        $vehicle = Vehicle::factory()->create(['company_id' => $company2->id]);

        $response = $this->deleteJson("/api/v1/vehicles/{$vehicle->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'deleted_at' => null]);
    }

    /** @test */
    public function user_cannot_view_driver_from_another_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $user = User::factory()->create(['company_id' => $company1->id]);
        Sanctum::actingAs($user);

        $driver = Driver::factory()->create(['company_id' => $company2->id]);

        $response = $this->getJson("/api/v1/drivers/{$driver->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_view_maintenance_from_another_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $user = User::factory()->create(['company_id' => $company1->id]);
        Sanctum::actingAs($user);

        $vehicle = Vehicle::factory()->create(['company_id' => $company2->id]);
        $maintenance = Maintenance::factory()->create([
            'company_id' => $company2->id,
            'vehicle_id' => $vehicle->id,
        ]);

        $response = $this->getJson("/api/v1/maintenances/{$maintenance->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_view_delivery_from_another_company()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $user = User::factory()->create(['company_id' => $company1->id]);
        Sanctum::actingAs($user);

        $delivery = Delivery::factory()->create(['company_id' => $company2->id]);

        $response = $this->getJson("/api/v1/deliveries/{$delivery->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function user_only_sees_their_company_vehicles_in_list()
    {
        $company1 = Company::factory()->create();
        $company2 = Company::factory()->create();

        $user = User::factory()->create(['company_id' => $company1->id]);
        Sanctum::actingAs($user);

        Vehicle::factory()->count(5)->create(['company_id' => $company1->id]);
        Vehicle::factory()->count(10)->create(['company_id' => $company2->id]);

        $response = $this->getJson('/api/v1/vehicles');

        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }
}
