<?php

declare(strict_types=1);

namespace Tests\Feature\Platform;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Newsletter\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RetentionEntryPointsTest extends TestCase
{
    use RefreshDatabase;

    public function test_newsletter_subscription_is_persisted_normalized_and_idempotent(): void
    {
        $this->from('/')
            ->post(route('newsletter.store'), ['email' => '  CONTADOR@Example.COM  '])
            ->assertRedirect('/')
            ->assertSessionHas('status');

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'contador@example.com',
            'source_path' => '/',
        ]);
        $this->assertSame(1, NewsletterSubscriber::query()->count());

        $this->from('/')
            ->post(route('newsletter.store'), ['email' => 'contador@example.com'])
            ->assertRedirect('/');

        $this->assertSame(1, NewsletterSubscriber::query()->count());
        $this->assertSame(
            1,
            \App\Core\Analytics\Models\PlatformAnalyticsEvent::query()
                ->where('event_name', AnalyticsEventName::NewsletterSubscribed->value)
                ->count(),
        );
        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => AnalyticsEventName::NewsletterSubscribed->value,
            'channel' => 'engagement',
        ]);
    }

    public function test_newsletter_links_subscription_to_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/')
            ->post(route('newsletter.store'), ['email' => $user->email])
            ->assertRedirect('/');

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => strtolower($user->email),
            'user_id' => $user->getKey(),
        ]);
    }

    public function test_logged_user_cannot_link_an_unrelated_email_to_their_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/')
            ->post(route('newsletter.store'), ['email' => 'outro@example.com'])
            ->assertRedirect('/');

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'outro@example.com',
            'user_id' => null,
        ]);
    }

    public function test_newsletter_is_available_on_desktop_sidebar_and_smaller_screens(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('id="newsletter-sidebar-email"', false)
            ->assertSee('id="newsletter-mobile-email"', false)
            ->assertSee('Fique por dentro do que realmente muda');
    }

    public function test_post_result_cta_prioritizes_optional_continuity_for_guests(): void
    {
        $this->get(route('tools.calculadora-salario-liquido.index'))
            ->assertOk()
            ->assertSee('data-result-continuity-cta', false)
            ->assertSee('Criar conta grátis')
            ->assertSee('Os cálculos continuam disponíveis sem cadastro.')
            ->assertDontSee('Conhecer o Plus');
    }

    public function test_post_result_cta_points_authenticated_users_to_real_history_when_available(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('tools.calculadora-salario-liquido.index'))
            ->assertOk()
            ->assertSee('Abrir histórico')
            ->assertSee(route('tools.calculadora-salario-liquido.history.index'), false);
    }
}
