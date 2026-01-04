<?php

namespace Tests\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('login:127.0.0.1');
    }

    /** @test */
    public function login_is_rate_limited_to_5_attempts_per_minute()
    {
        $user = User::factory()->create();

        // 5 tentativas com sucesso
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/auth/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);

            $this->assertNotEquals(429, $response->status());
        }

        // 6ª tentativa deve ser bloqueada
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    /** @test */
    public function authenticated_api_is_rate_limited()
    {
        $user = $this->authenticatedUser();

        // Simular muitas requisições (limite agora é 100/min para autenticados)
        for ($i = 0; $i < 101; $i++) {
            $response = $this->getJson('/api/v1/vehicles');

            if ($i < 100) {
                $this->assertNotEquals(429, $response->status());
            }
        }

        // 101ª requisição deve ser bloqueada
        $response = $this->getJson('/api/v1/vehicles');
        $response->assertStatus(429);
    }
}
