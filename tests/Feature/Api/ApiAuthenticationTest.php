<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class ApiAuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('tools-api.clients', [[
            'id' => 'prazzu-core',
            'name' => 'Prazzu Core',
            'token' => 'test-private-token',
            'abilities' => ['tools:read', 'tools:execute'],
        ]]);
    }

    public function test_api_rejects_requests_without_credentials(): void
    {
        $this->getJson('/api/v1')
            ->assertUnauthorized()
            ->assertExactJson([
                'success' => false,
                'error' => [
                    'code' => 'invalid_api_credentials',
                    'message' => 'As credenciais da API são inválidas ou não foram informadas.',
                ],
            ]);
    }

    public function test_api_rejects_an_invalid_token(): void
    {
        $this->withToken('invalid-token')
            ->getJson('/api/v1')
            ->assertUnauthorized();
    }

    public function test_api_accepts_a_registered_private_client(): void
    {
        $this->withToken('test-private-token')
            ->getJson('/api/v1')
            ->assertOk()
            ->assertJsonPath('data.client.id', 'prazzu-core')
            ->assertJsonPath('data.client.name', 'Prazzu Core')
            ->assertJsonPath('data.client.abilities.0', 'tools:read');
    }

    public function test_ability_middleware_denies_an_ungranted_operation(): void
    {
        Route::middleware(['api.client', 'api.ability:admin:manage'])
            ->get('/api/testing/private-ability', static fn () => response()->json(['ok' => true]));

        $this->withToken('test-private-token')
            ->getJson('/api/testing/private-ability')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'api_ability_denied');
    }

    public function test_ability_middleware_accepts_a_granted_operation(): void
    {
        Route::middleware(['api.client', 'api.ability:tools:execute'])
            ->get('/api/testing/private-ability', static fn () => response()->json(['ok' => true]));

        $this->withToken('test-private-token')
            ->getJson('/api/testing/private-ability')
            ->assertOk()
            ->assertExactJson(['ok' => true]);
    }
}
