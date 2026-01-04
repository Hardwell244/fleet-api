<?php

namespace Tests\Feature\Api;

use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_list_their_company_drivers()
    {
        $user = $this->authenticatedUser();

        Driver::factory()->count(3)->create(['company_id' => $user->company_id]);
        Driver::factory()->count(2)->create(); // Outra empresa

        $response = $this->getJson('/api/v1/drivers');

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    /** @test */
    public function user_can_create_driver()
    {
        $user = $this->authenticatedUser();

        $data = [
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'cnh' => 'ABC123456',
            'cnh_category' => 'D',
            'cnh_expiry' => '2026-12-31',
            'phone' => '11999999999',
            'email' => 'joao@example.com',
            'status' => 'active',
        ];

        $response = $this->postJson('/api/v1/drivers', $data);

        $response->assertStatus(201);
        $response->assertJsonPath('data.name', 'João Silva');
        $this->assertDatabaseHas('drivers', [
            'cpf' => '12345678901',
            'company_id' => $user->company_id,
        ]);
    }

    /** @test */
    public function cpf_must_be_unique()
    {
        $user = $this->authenticatedUser();
        Driver::factory()->create([
            'cpf' => '12345678901',
            'company_id' => $user->company_id
        ]);

        $response = $this->postJson('/api/v1/drivers', [
            'name' => 'Outro Motorista',
            'cpf' => '12345678901',
            'cnh' => 'XYZ789012',
            'cnh_category' => 'D',
            'cnh_expiry' => '2026-12-31',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['Este CPF já está cadastrado.']);
    }

    /** @test */
    public function cnh_must_be_unique()
    {
        $user = $this->authenticatedUser();
        Driver::factory()->create([
            'cnh' => 'ABC123456',
            'company_id' => $user->company_id
        ]);

        $response = $this->postJson('/api/v1/drivers', [
            'name' => 'Outro Motorista',
            'cpf' => '98765432101',
            'cnh' => 'ABC123456',
            'cnh_category' => 'D',
            'cnh_expiry' => '2026-12-31',
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['Esta CNH já está cadastrada.']);
    }

    /** @test */
    public function user_can_update_their_driver()
    {
        $user = $this->authenticatedUser();
        $driver = Driver::factory()->create(['company_id' => $user->company_id]);

        $response = $this->putJson("/api/v1/drivers/{$driver->id}", [
            'name' => 'Nome Atualizado',
            'cpf' => $driver->cpf,
            'cnh' => $driver->cnh,
            'cnh_category' => $driver->cnh_category,
            'cnh_expiry' => $driver->cnh_expiry,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'name' => 'Nome Atualizado',
        ]);
    }

    /** @test */
    public function user_can_delete_their_driver()
    {
        $user = $this->authenticatedUser();
        $driver = Driver::factory()->create(['company_id' => $user->company_id]);

        $response = $this->deleteJson("/api/v1/drivers/{$driver->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('drivers', ['id' => $driver->id]);
    }
}
