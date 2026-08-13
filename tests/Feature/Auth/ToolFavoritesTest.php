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
            ->post(route('account.tools.favorite', ['tool' => 'calculadora-salario-liquido']))
            ->assertRedirect();

        $this->assertDatabaseHas('user_tool_favorites', [
            'user_id' => $user->id,
            'tool_slug' => 'calculadora-salario-liquido',
        ]);

        $this->actingAs($user)
            ->get(route('tools.calculadora-salario-liquido.index'))
            ->assertOk()
            ->assertSee('Desfavoritar');

        $this->actingAs($user)
            ->get(route('account.show'))
            ->assertOk()
            ->assertSee('Ferramentas favoritas')
            ->assertSee('Calculadora de Salário Líquido');

        $this->actingAs($user)
            ->post(route('account.tools.favorite', ['tool' => 'calculadora-salario-liquido']))
            ->assertRedirect();

        self::assertFalse(UserToolFavorite::query()
            ->where('user_id', $user->id)
            ->where('tool_slug', 'calculadora-salario-liquido')
            ->exists());
    }

    public function test_guest_cannot_toggle_tool_favorite(): void
    {
        $this->post(route('account.tools.favorite', ['tool' => 'calculadora-salario-liquido']))
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
