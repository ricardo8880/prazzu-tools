<?php

namespace Tests\Feature\Api;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

final class ToolApiExecutionFoundationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('tools-api.clients', [[
            'id' => 'prazzu-core',
            'name' => 'Prazzu Core',
            'token' => 'test-private-token',
            'abilities' => ['tools:execute'],
        ]]);
    }

    public function test_execution_route_is_versioned_and_protected_by_ability(): void
    {
        $route = Route::getRoutes()->getByName('api.v1.tools.execute');

        $this->assertNotNull($route);
        $this->assertSame('api/v1/tools/{tool}/{action}', $route->uri());
        $this->assertContains('api.ability:tools:execute', $route->gatherMiddleware());
    }

    public function test_unknown_action_uses_the_standard_not_found_response(): void
    {
        $this->withToken('test-private-token')
            ->postJson('/api/v1/tools/unknown-tool/calculate', ['amount' => 100])
            ->assertNotFound()
            ->assertExactJson([
                'success' => false,
                'error' => [
                    'code' => 'tool_action_not_found',
                    'message' => 'A ferramenta ou ação solicitada não está disponível na API.',
                ],
            ]);
    }
}
