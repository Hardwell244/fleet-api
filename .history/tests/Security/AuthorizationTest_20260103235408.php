<?php

namespace Tests\Security;

use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Maintenance;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function vehicle_policy_prevents_unauthorized_view()
    {
        $user = $this->authenticatedUser();
        $otherCompanyVehicle = Vehicle::factory()->create(); // Outra empresa

        $response = $this->getJson("/api/v1/vehicles/{$otherCompanyVehicle->id}");

        $response->assertStatus(403);
        $response->assertJsonFragment(['message' => 'This action is unauthorized.']);
    }

    /** @test */
    public function vehicle_policy_allows_authorized_view()
    {
        $user = $this->authenticatedUser();
        $vehicle = Vehicle::factory()->create(['company_id' => $user->company_id]);

        $response = $this->getJson("/api/v1/vehicles/{$vehicle->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function driver_policy_prevents_unauthorized_update()
    {
        $user = $this->authenticatedUser();
        $otherCompanyDriver = Driver::factory()->create();

        $response = $this->putJson("/api/v1/drivers/{$otherCompanyDriver->id}", [
            'name' => 'Hacked Name',
            'cpf' => $otherCompanyDriver->cpf,
            'cnh' => $otherCompanyDriver->cnh,
            'cnh_category' => $otherCompanyDriver->cnh_category,
            'cnh_expiry' => $otherCompanyDriver->cnh_expiry,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('drivers', ['name' => 'Hacked Name']);
    }

    /** @test */
    public function maintenance_policy_prevents_unauthorized_delete()
    {
        $user = $this->authenticatedUser();
        $vehicle = Vehicle::factory()->create(); // Outra empresa
        $maintenance = Maintenance::factory()->create([
            'vehicle_id' => $vehicle->id,
            'company_id' => $vehicle->company_id,
        ]);

        $response = $this->deleteJson("/api/v1/maintenances/{$maintenance->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('maintenances', [
            'id' => $maintenance->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function delivery_policy_prevents_unauthorized_access()
    {
        $user = $this->authenticatedUser();
        $otherCompanyDelivery = Delivery::factory()->create();

        $response = $this->getJson("/api/v1/deliveries/{$otherCompanyDelivery->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function policies_allow_access_to_own_company_resources()
    {
        $user = $this->authenticatedUser();

        $vehicle = Vehicle::factory()->create(['company_id' => $user->company_id]);
        $driver = Driver::factory()->create(['company_id' => $user->company_id]);
        $maintenance = Maintenance::factory()->create([
            'company_id' => $user->company_id,
            'vehicle_id' => $vehicle->id,
        ]);
        $delivery = Delivery::factory()->create(['company_id' => $user->company_id]);

        $this->getJson("/api/v1/vehicles/{$vehicle->id}")->assertStatus(200);
        $this->getJson("/api/v1/drivers/{$driver->id}")->assertStatus(200);
        $this->getJson("/api/v1/maintenances/{$maintenance->id}")->assertStatus(200);
        $this->getJson("/api/v1/deliveries/{$delivery->id}")->assertStatus(200);
    }
}
