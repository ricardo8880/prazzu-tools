<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Core\Tools\Favorites\Models\UserToolFavorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ToolFavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_favorite_and_unfavorite_a_tool(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('account.tools.favorite', ['tool' => 'take-home-pay-calculator']))
            ->assertRedirect();

        $this->assertDatabaseHas('user_tool_favorites', [
            'user_id' => $user->id,
            'tool_slug' => 'take-home-pay-calculator',
        ]);

        $this->actingAs($user)
            ->get(route('tools.take-home-pay-calculator.index'))
            ->assertOk()
            ->assertSee('Desfavoritar');

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('Ferramentas favoritas')
            ->assertSee('Calculadora de Salário Líquido');

        $this->actingAs($user)
            ->post(route('account.tools.favorite', ['tool' => 'take-home-pay-calculator']))
            ->assertRedirect();

        self::assertFalse(UserToolFavorite::query()
            ->where('user_id', $user->id)
            ->where('tool_slug', 'take-home-pay-calculator')
            ->exists());
    }

    public function test_guest_sees_favorite_intent_without_losing_access_to_the_tool(): void
    {
        $favoriteIntentUrl = route('register', [
            'source' => 'tool_favorite',
            'tool' => 'take-home-pay-calculator',
        ]);

        $this->get(route('tools.take-home-pay-calculator.index'))
            ->assertOk()
            ->assertSee('Favoritar')
            ->assertSee($favoriteIntentUrl, false)
            ->assertSee('Calcular salário líquido');

        $this->get($favoriteIntentUrl)
            ->assertOk()
            ->assertSee('Salve esta ferramenta para encontrar depois')
            ->assertSee('O cadastro é opcional e não libera cálculos')
            ->assertSee(route('login', [
                'source' => 'tool_favorite',
                'tool' => 'take-home-pay-calculator',
            ]), false);

        $this->assertDatabaseCount('user_tool_favorites', 0);
    }

    public function test_guest_cannot_toggle_tool_favorite(): void
    {
        $this->post(route('account.tools.favorite', ['tool' => 'take-home-pay-calculator']))
            ->assertRedirect(route('login'));
    }

    public function test_unknown_tool_cannot_be_favorited(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('account.tools.favorite', ['tool' => 'ferramenta-inexistente']))
            ->assertNotFound();
    }
}
