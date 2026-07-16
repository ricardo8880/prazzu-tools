<?php

namespace Tests\Feature\Auth;

use App\Core\Identity\Notifications\PasswordChanged;
use App\Core\Identity\Notifications\PrazzuResetPassword;
use App\Core\Identity\Notifications\PrazzuVerifyEmail;
use App\Core\Identity\Notifications\WelcomeToPrazzuTools;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class TransactionalEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_sends_welcome_and_verification_notifications(): void
    {
        Notification::fake();

        $this->post(route('register.store'), [
            'name' => 'Pessoa Teste',
            'email' => 'pessoa@example.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ])->assertRedirect(route('account.show'));

        $user = User::query()->where('email', 'pessoa@example.com')->firstOrFail();
        Notification::assertSentTo($user, WelcomeToPrazzuTools::class);
        Notification::assertSentTo($user, PrazzuVerifyEmail::class);
    }

    public function test_password_reset_request_uses_neutral_response_and_sends_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, PrazzuResetPassword::class);
    }

    public function test_authenticated_user_can_change_password_and_receives_security_notice(): void
    {
        Notification::fake();
        $user = User::factory()->create(['password' => 'senha1234']);

        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'senha1234',
            'password' => 'novaSenha123',
            'password_confirmation' => 'novaSenha123',
        ])->assertSessionHas('status');

        $this->assertTrue(Hash::check('novaSenha123', $user->fresh()->password));
        Notification::assertSentTo($user, PasswordChanged::class);
    }
}
