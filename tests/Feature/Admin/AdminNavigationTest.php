<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Core\Access\Enums\AccountRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_administrator_can_open_central_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => AccountRole::Administrator]))
            ->get(route('admin.index'))
            ->assertOk()
            ->assertSee('Painel administrativo')
            ->assertSee(route('admin.analytics.index'), false)
            ->assertSee(route('admin.acquisition.contexts.index'), false)
            ->assertSee(route('admin.blog.posts.index'), false);
    }

    public function test_regular_user_cannot_open_central_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => AccountRole::User]))
            ->get(route('admin.index'))
            ->assertForbidden();
    }

    public function test_shared_navigation_is_visible_on_admin_pages_only(): void
    {
        $administrator = User::factory()->create(['role' => AccountRole::Administrator]);

        $this->actingAs($administrator)
            ->get(route('admin.acquisition.contexts.index'))
            ->assertOk()
            ->assertSee('Navegação administrativa')
            ->assertSee('aria-current="page"', false);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Navegação administrativa');
    }
}
