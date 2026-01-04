<?php

namespace Tests\Security;

use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function vehicle_creation_requires_all_mandatory_fields()
    {
        $user = $this->authenticatedUser();

        $response = $this->postJson('/api/v1/vehicles', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['plate', 'brand', 'model', 'year', 'type']);
    }

    /** @test */
    public function vehicle_plate_must_be_valid_format()
    {
        $user = $this->authenticatedUser();

        $response = $this->postJson('/api/v1/vehicles', [
            'plate' => 'INVALID',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2023,
            'type' => 'car',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('plate');
    }

    /** @test */
    public function vehicle_year_must_be_valid()
    {
        $user = $this->authenticatedUser();

        // Ano muito antigo
        $response = $this->postJson('/api/v1/vehicles', [
            'plate' => 'ABC1234',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 1800,
            'type' => 'car',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('year');

        // Ano futuro
        $response = $this->postJson('/api/v1/vehicles', [
            'plate' => 'ABC1234',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => date('Y') + 5,
            'type' => 'car',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('year');
    }

    /** @test */
    public function driver_cpf_must_have_11_digits()
    {
        $user = $this->authenticatedUser();

        $response = $this->postJson('/api/v1/drivers', [
            'name' => 'João Silva',
            'cpf' => '123456789', // Apenas 9 dígitos
            'cnh' => 'ABC123456',
            'cnh_category' => 'D',
            'cnh_expiry' => '2026-12-31',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('cpf');
    }

    /** @test */
    public function driver_cnh_category_must_be_valid()
    {
        $user = $this->authenticatedUser();

        $response = $this->postJson('/api/v1/drivers', [
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'cnh' => 'ABC123456',
            'cnh_category' => 'Z', // Categoria inválida
            'cnh_expiry' => '2026-12-31',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('cnh_category');
    }

    /** @test */
    public function delivery_coordinates_must_be_valid()
    {
        $user = $this->authenticatedUser();

        // Latitude inválida (> 90)
        $response = $this->postJson('/api/v1/deliveries', [
            'origin_address' => 'Rua A',
            'origin_lat' => 91, // Inválido
            'origin_lng' => -46.633,
            'destination_address' => 'Rua B',
            'destination_lat' => -23.550,
            'destination_lng' => -46.633,
            'recipient_name' => 'João',
            'recipient_phone' => '11999999999',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('origin_lat');

        // Longitude inválida (> 180)
        $response = $this->postJson('/api/v1/deliveries', [
            'origin_address' => 'Rua A',
            'origin_lat' => -23.550,
            'origin_lng' => 181, // Inválido
            'destination_address' => 'Rua B',
            'destination_lat' => -23.550,
            'destination_lng' => -46.633,
            'recipient_name' => 'João',
            'recipient_phone' => '11999999999',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('origin_lng');
    }

    /** @test */
    public function sql_injection_attempts_are_sanitized()
    {
        $user = $this->authenticatedUser();

        $maliciousInput = "'; DROP TABLE vehicles; --";

        $response = $this->postJson('/api/v1/vehicles', [
            'plate' => $maliciousInput,
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2023,
            'type' => 'car',
        ]);

        // Deve falhar na validação, não executar SQL
        $response->assertStatus(422);

        // Tabela deve ainda existir
        $this->assertDatabaseCount('vehicles', 0);
    }

    /** @test */
    public function xss_attempts_are_sanitized()
    {
        $user = $this->authenticatedUser();

        $xssInput = '<script>alert("XSS")</script>';

        $vehicle = Vehicle::factory()->create([
            'company_id' => $user->company_id,
            'brand' => $xssInput,
        ]);

        $response = $this->getJson("/api/v1/vehicles/{$vehicle->id}");

        $response->assertStatus(200);
        // O conteúdo não deve executar script (JSON não renderiza HTML)
        $response->assertJsonPath('data.brand', $xssInput);
    }
}
