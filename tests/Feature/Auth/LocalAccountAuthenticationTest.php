<?php

namespace Tests\Feature\Auth;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class LocalAccountAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_create_a_local_account_and_is_authenticated(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Usuário de Teste',
            'email' => 'usuario@example.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $response->assertRedirect(route('account.show'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'usuario@example.com',
            'prazzu_account_id' => null,
        ]);
    }

    public function test_account_created_from_result_continuity_keeps_only_safe_attribution(): void
    {
        $this->post(route('register.store', [
            'source' => 'result_continuity',
            'tool' => 'calculadora-salario-liquido',
        ]), [
            'name' => 'Pessoa Retorno',
            'email' => 'retorno@example.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ])->assertRedirect(route('account.show'));

        $event = PlatformAnalyticsEvent::query()
            ->where('event_name', AnalyticsEventName::AccountCreated->value)
            ->firstOrFail();

        self::assertSame('result_continuity', $event->metadata['source']);
        self::assertSame('calculadora-salario-liquido', $event->metadata['tool_slug']);
        self::assertArrayNotHasKey('email', $event->metadata);
    }

    public function test_existing_user_can_login_and_logout(): void
    {
        $user = User::factory()->create([
            'password' => 'senha1234',
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'senha1234',
        ])->assertRedirect(route('account.show'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_guest_cannot_open_account_page(): void
    {
        $this->get(route('account.show'))->assertRedirect(route('login'));
    }
}
