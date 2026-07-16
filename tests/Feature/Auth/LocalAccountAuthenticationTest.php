<?php

namespace Tests\Feature\Auth;

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
