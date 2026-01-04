<?php

namespace Tests\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_cannot_access_protected_routes()
    {
        $routes = [
            '/api/v1/vehicles',
            '/api/v1/drivers',
            '/api/v1/maintenances',
            '/api/v1/deliveries',
        ];

        foreach ($routes as $route) {
            $response = $this->getJson($route);
            $response->assertStatus(401);
            $response->assertJson(['message' => 'Não autenticado.']);
        }
    }

    /** @test */
    public function login_requires_valid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
        $response->assertJsonFragment(['error' => 'Credenciais inválidas']);
    }

    /** @test */
    public function login_returns_token_on_success()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['token', 'user']);
        $this->assertNotNull($response->json('token'));
    }

    /** @test */
    public function logout_invalidates_token()
    {
        $user = $this->authenticatedUser();

        $response = $this->postJson('/api/auth/logout');
        $response->assertStatus(200);

        // Tentar usar rotas protegidas após logout
        $response = $this->getJson('/api/v1/vehicles');
        $response->assertStatus(401);
    }

    /** @test */
    public function me_endpoint_returns_authenticated_user()
    {
        $user = $this->authenticatedUser();

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200);
        $response->assertJsonPath('email', $user->email);
        $response->assertJsonPath('company_id', $user->company_id);
    }

    /** @test */
    public function invalid_token_is_rejected()
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid-token-here')
            ->getJson('/api/v1/vehicles');

        $response->assertStatus(401);
    }

    /** @test */
    public function expired_token_is_rejected()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token', expiresAt: now()->subHour());

        $response = $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
            ->getJson('/api/v1/vehicles');

        $response->assertStatus(401);
    }
}
