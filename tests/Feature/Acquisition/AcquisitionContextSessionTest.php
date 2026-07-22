<?php

namespace Tests\Feature\Acquisition;

use App\Core\Acquisition\Infrastructure\Persistence\AcquisitionContextRecord;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AcquisitionContextSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_link_activates_context_and_redirects_to_clean_url(): void
    {
        $context = $this->activeContext();

        $response = $this->get(route('home', ['context' => $context->keyword]));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('acquisition.context.keyword', $context->keyword);
        $response->assertSessionHas('acquisition.context.mode', 'contextual');

        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => AnalyticsEventName::AcquisitionContextEntered->value,
            'subject_slug' => $context->keyword,
        ]);
    }

    public function test_context_survives_refresh_without_query_string(): void
    {
        $context = $this->activeContext();

        $this->withSession([
            'acquisition' => [
                'context' => [
                    'keyword' => $context->keyword,
                    'activated_at' => now()->toIso8601String(),
                ],
            ],
        ])->get(route('home'))
            ->assertOk()
            ->assertViewHas('acquisitionContext', fn ($active): bool => $active?->keyword === $context->keyword)
            ->assertSee('Experiência de contexto');
    }

    public function test_active_context_is_shared_with_other_platform_pages(): void
    {
        $context = $this->activeContext();

        $this->withSession([
            'acquisition' => [
                'context' => [
                    'keyword' => $context->keyword,
                    'activated_at' => now()->toIso8601String(),
                ],
            ],
        ])->get(route('tools.index'))
            ->assertOk()
            ->assertViewHas('activeAcquisitionContext', fn ($active): bool => $active?->keyword === $context->keyword);
    }

    public function test_contextual_bar_is_shown_with_both_actions(): void
    {
        $context = $this->activeContext();

        $this->withSession([
            'acquisition' => [
                'context' => [
                    'keyword' => $context->keyword,
                    'activated_at' => now()->toIso8601String(),
                ],
            ],
        ])->get(route('tools.index'))
            ->assertOk()
            ->assertSee('Experiência personalizada')
            ->assertSee('Explorar livremente')
            ->assertSee('Continuar neste tema')
            ->assertSee(route('acquisition.context.clear'), false)
            ->assertSee(route('acquisition.context.continue'), false);
    }

    public function test_visitor_can_continue_context_and_keep_it_active(): void
    {
        $context = $this->activeContext();

        $response = $this->withSession([
            'acquisition' => [
                'context' => [
                    'keyword' => $context->keyword,
                    'activated_at' => now()->toIso8601String(),
                ],
            ],
        ])->post(route('acquisition.context.continue'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('acquisition.context.keyword', $context->keyword);
        $response->assertSessionHas('acquisition.context.mode', 'contextual');

        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => AnalyticsEventName::AcquisitionContextContinued->value,
            'subject_slug' => $context->keyword,
        ]);
    }

    public function test_unknown_context_is_removed_from_url_without_being_persisted(): void
    {
        $this->get(route('home', ['context' => 'nao-existe']))
            ->assertRedirect(route('home'))
            ->assertSessionMissing('acquisition.context');
    }

    public function test_visitor_can_explore_freely_without_losing_active_context(): void
    {
        $context = $this->activeContext();

        $response = $this->withSession([
            'acquisition' => [
                'context' => [
                    'keyword' => $context->keyword,
                    'activated_at' => now()->toIso8601String(),
                ],
            ],
        ])->post(route('acquisition.context.clear'));

        $response->assertRedirect(route('tools.index'));
        $response->assertSessionHas('acquisition.context.keyword', $context->keyword);
        $response->assertSessionHas('acquisition.context.mode', 'free');

        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => AnalyticsEventName::AcquisitionContextExited->value,
            'subject_slug' => $context->keyword,
        ]);
    }

    private function activeContext(): AcquisitionContextRecord
    {
        return AcquisitionContextRecord::query()->create([
            'name' => 'Experiência de contexto',
            'keyword' => 'contexto-persistente',
            'campaign_identifier' => 'campanha-contexto',
            'status' => 'active',
            'hero_title_line' => 'Experiência de contexto',
            'contextual_message' => 'Você está seguindo uma experiência especial.',
            'contextual_continue_label' => 'Continuar neste tema',
        ]);
    }
}
