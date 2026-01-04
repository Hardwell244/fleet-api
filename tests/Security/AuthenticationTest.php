<?php

namespace Tests\Security;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/v1/vehicles');
        $response->assertStatus(401);
    }

    /** @test */
    public function login_requires_valid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@logitech.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function login_returns_token_on_success()
    {
        // CRIAR COMPANY E USER COM CREDENCIAIS CORRETAS
        $company = Company::factory()->create();

        $user = User::factory()->create([
            'company_id' => $company->id,
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // Hash da senha
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
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $token = $user->createToken('test-token')->plainTextToken;

        // Verificar que o token existe antes do logout
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/logout');

        $response->assertStatus(200);

        // Verificar que o token foi deletado após o logout
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    /** @test */
    public function me_endpoint_returns_authenticated_user()
    {
        $user = $this->authenticatedUser();

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200);
        $response->assertJson([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ]
        ]);
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
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $token = $user->createToken('test-token')->plainTextToken;

        // Simulate token expiration by deleting it
        $user->tokens()->delete();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/vehicles');

        $response->assertStatus(401);
    }
}
