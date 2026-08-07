<?php

namespace Tests\Feature\Verticals;

use App\Core\Verticals\Application\VerticalContext;
use App\Core\Verticals\Domain\Data\Vertical;
use Tests\TestCase;

final class ActiveVerticalContextTest extends TestCase
{
    public function test_current_platform_resolves_contabilidade_as_explicit_default_vertical(): void
    {
        $response = $this->get(route('tools.index'));

        $response->assertOk();
        $response->assertViewHas('activeVertical', fn ($vertical): bool => $vertical instanceof Vertical
            && $vertical->slug === 'contabilidade');

        self::assertSame('contabilidade', app(VerticalContext::class)->slug());
    }

    public function test_session_context_has_priority_over_default_vertical(): void
    {
        config()->set('verticals.registered.rh', ['name' => 'Recursos Humanos']);

        $response = $this->withSession([
            'vertical' => [
                'context' => [
                    'slug' => 'rh',
                    'activated_at' => now()->toIso8601String(),
                ],
            ],
        ])->get(route('tools.index'));

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
        ])->get(route('tools.index'));

        $response->assertOk();
        $response->assertSessionMissing('vertical.context');
        $response->assertViewHas('activeVertical', fn ($vertical): bool => $vertical instanceof Vertical
            && $vertical->slug === 'contabilidade');
    }

    public function test_global_fallback_remains_valid_when_no_default_vertical_is_configured(): void
    {
        config()->set('verticals.default', null);

        $response = $this->get(route('tools.index'));

        $response->assertOk();
        $response->assertViewHas('activeVertical', null);
        self::assertNull(app(VerticalContext::class)->active());
    }
}
