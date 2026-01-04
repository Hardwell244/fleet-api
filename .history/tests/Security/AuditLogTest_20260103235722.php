<?php

namespace Tests\Security;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_vehicle_generates_audit_log()
    {
        Log::shouldReceive('channel')
            ->with('audit')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($action, $data) {
                return $action === 'CREATED'
                    && $data['model'] === 'Vehicle'
                    && isset($data['user_id'])
                    && isset($data['ip']);
            });

        $user = $this->authenticatedUser();

        $this->postJson('/api/v1/vehicles', [
            'plate' => 'ABC1234',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2023,
            'type' => 'car',
        ]);
    }

    /** @test */
    public function updating_vehicle_generates_audit_log_with_changes()
    {
        Log::shouldReceive('channel')->andReturnSelf();
        Log::shouldReceive('info')->andReturnSelf();
        Log::shouldReceive('warning')->andReturnSelf();

        $user = $this->authenticatedUser();
        $vehicle = Vehicle::factory()->create([
            'company_id' => $user->company_id,
            'model' => 'Corolla',
        ]);

        Log::shouldReceive('channel')
            ->with('audit')
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($action, $data) {
                return $action === 'UPDATED'
                    && isset($data['changes'])
                    && isset($data['original']);
            });

        $this->putJson("/api/v1/vehicles/{$vehicle->id}", [
            'plate' => $vehicle->plate,
            'brand' => $vehicle->brand,
            'model' => 'Updated Model',
            'year' => $vehicle->year,
            'type' => $vehicle->type,
        ]);
    }

    /** @test */
    public function deleting_vehicle_generates_audit_log()
    {
        Log::shouldReceive('channel')->andReturnSelf();
        Log::shouldReceive('info')->andReturnSelf();

        $user = $this->authenticatedUser();
        $vehicle = Vehicle::factory()->create(['company_id' => $user->company_id]);

        Log::shouldReceive('channel')
            ->with('audit')
            ->andReturnSelf();

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($action, $data) {
                return $action === 'DELETED'
                    && $data['model'] === 'Vehicle';
            });

        $this->deleteJson("/api/v1/vehicles/{$vehicle->id}");
    }

    /** @test */
    public function audit_log_includes_user_information()
    {
        Log::shouldReceive('channel')->andReturnSelf();

        $user = $this->authenticatedUser();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($action, $data) use ($user) {
                return $data['user_id'] === $user->id
                    && $data['user_email'] === $user->email
                    && isset($data['ip'])
                    && isset($data['company_id']);
            });

        Vehicle::factory()->create(['company_id' => $user->company_id]);
    }
}
