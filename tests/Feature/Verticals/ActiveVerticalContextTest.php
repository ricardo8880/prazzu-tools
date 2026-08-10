<?php

namespace Tests\Feature\Verticals;

use App\Core\Verticals\Application\VerticalContext;
use App\Core\Verticals\Domain\Data\Vertical;
use Tests\TestCase;

final class ActiveVerticalContextTest extends TestCase
{
    public function test_current_platform_resolves_contabilidade_as_explicit_default_vertical(): void
    {
        $response = $this->get(route('tools.index', ['vertical' => 'contabil']));

        $response->assertOk();
        $response->assertViewHas('activeVertical', fn ($vertical): bool => $vertical instanceof Vertical
            && $vertical->slug === 'contabilidade');

        self::assertSame('contabilidade', app(VerticalContext::class)->slug());
    }

    public function test_session_context_has_priority_over_default_vertical(): void
    {
        config()->set('verticals.registered.rh', ['name' => 'Recursos Humanos', 'public_slug' => 'rh']);

        $response = $this->withSession([
            'vertical' => [
                'context' => [
                    'slug' => 'rh',
                    'activated_at' => now()->toIso8601String(),
                ],
            ],
        ])->get(route('tools.index', ['vertical' => 'rh']));

        $response->assertOk();
        $response->assertViewHas('activeVertical', fn ($vertical): bool => $vertical instanceof Vertical
            && $vertical->slug === 'rh');
    }

    public function test_invalid_session_vertical_is_removed_and_falls_back_to_default(): void
    {
        $response = $this->withSession([
            'vertical' => [
                'context' => [
                    'slug' => 'nao-cadastrada',
                ],
            ],
        ])->get(route('tools.index', ['vertical' => 'contabil']));

        $response->assertOk();
        $response->assertSessionMissing('vertical.context');
        $response->assertViewHas('activeVertical', fn ($vertical): bool => $vertical instanceof Vertical
            && $vertical->slug === 'contabilidade');
    }

    public function test_route_context_has_priority_over_session_and_default_vertical(): void
    {
        $response = $this->withSession([
            'vertical' => [
                'context' => [
                    'slug' => 'contabilidade',
                ],
            ],
        ])->get(route('tools.index', ['vertical' => 'rh']));

        $response->assertOk();
        $response->assertViewHas('activeVertical', fn ($vertical): bool => $vertical instanceof Vertical
            && $vertical->slug === 'rh');
        self::assertSame('rh', app(VerticalContext::class)->slug());
    }
}
