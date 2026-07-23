<?php

namespace Tests\Feature\Api;

use App\Core\Tools\Api\Support\ApiResponse;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class ApiFoundationTest extends TestCase
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

    private function api(): static
    {
        return $this->withToken('test-private-token');
    }
    public function test_versioned_api_status_uses_the_standard_success_envelope(): void
    {
        $this->api()->getJson('/api/v1')
            ->assertOk()
            ->assertExactJson([
                'success' => true,
                'data' => [
                    'name' => 'Prazzu Tools API',
                    'version' => 'v1',
                    'status' => 'available',
                    'client' => [
                        'id' => 'prazzu-core',
                        'name' => 'Prazzu Core',
                        'abilities' => ['tools:read', 'tools:execute'],
                    ],
                ],
            ]);
    }

    public function test_api_routes_have_a_versioned_name(): void
    {
        $route = Route::getRoutes()->getByName('api.v1.status');

        $this->assertNotNull($route);
        $this->assertSame('api/v1', $route->uri());
    }

    public function test_unknown_api_routes_use_the_standard_error_envelope(): void
    {
        $this->api()->getJson('/api/v1/not-found')
            ->assertNotFound()
            ->assertExactJson([
                'success' => false,
                'error' => [
                    'code' => 'resource_not_found',
                    'message' => 'O recurso solicitado não foi encontrado.',
                ],
            ]);
    }

    public function test_error_factory_includes_details_when_provided(): void
    {
        $response = ApiResponse::error(
            code: 'validation_failed',
            message: 'Os dados enviados são inválidos.',
            status: 422,
            details: ['fields' => ['amount' => ['O campo é obrigatório.']]],
        );

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame([
            'success' => false,
            'error' => [
                'code' => 'validation_failed',
                'message' => 'Os dados enviados são inválidos.',
                'details' => [
                    'fields' => ['amount' => ['O campo é obrigatório.']],
                ],
            ],
        ], $response->getData(true));
    }
}
