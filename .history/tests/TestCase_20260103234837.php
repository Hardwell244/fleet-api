<?php

namespace Tests;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Desabilitar logs durante testes
        \Log::shouldReceive('channel')->andReturnSelf();
        \Log::shouldReceive('info')->andReturnSelf();
        \Log::shouldReceive('warning')->andReturnSelf();
    }

    /**
     * Criar usuário autenticado para testes
     */
    protected function authenticatedUser(?Company $company = null): User
    {
        if (!$company) {
            $company = Company::factory()->create();
        }

        $user = User::factory()->create([
            'company_id' => $company->id,
        ]);

        Sanctum::actingAs($user);

        return $user;
    }

    /**
     * Criar empresa para testes
     */
    protected function createCompany(): Company
    {
        return Company::factory()->create();
    }

    /**
     * Autenticar usuário e retornar token
     */
    protected function getAuthToken(?User $user = null): string
    {
        if (!$user) {
            $company = $this->createCompany();
            $user = User::factory()->create(['company_id' => $company->id]);
        }

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password', // Factory usa essa senha padrão
        ]);

        return $response->json('token');
    }
}
