<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;

final class PersistenceAuthenticationTest extends TestCase
{
    public function test_guest_is_redirected_only_when_accessing_persistent_history(): void
    {
        $this->get(route('tools.calculadora-de-rescisao.history.index'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_can_open_tools_without_authentication(): void
    {
        $this->get(route('tools.calculadora-de-rescisao.index'))->assertOk();
        $this->get(route('tools.calculadora-simples-nacional.index'))->assertOk();
    }
    public function test_persistence_auth_is_not_applied_to_immediate_tool_usage_routes(): void
    {
        foreach (app('router')->getRoutes() as $route) {
            $name = (string) $route->getName();
            if (! str_starts_with($name, 'tools.') || ! in_array('persistence.auth', $route->gatherMiddleware(), true)) {
                continue;
            }

            $persistentRoute = Str::contains($name, [
                '.history.',
                '.profiles.',
                '.adjustments.delete',
            ]);

            self::assertTrue(
                $persistentRoute,
                "A rota [{$name}] exige login fora de um fluxo de persistência/continuidade.",
            );
        }
    }

}
