<?php

namespace Tests\Feature\Analytics;

use App\Blog\Enums\BlogPostStatus;
use App\Blog\Models\BlogPost;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BlogAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_records_blog_engagement_events(): void
    {
        $post = BlogPost::query()->create([
            'title' => 'Artigo de teste', 'slug' => 'artigo-de-teste', 'excerpt' => 'Resumo',
            'content' => '<p>Conteúdo</p>', 'category' => 'Fiscal',
            'status' => BlogPostStatus::Published->value, 'published_at' => now(),
            'should_index' => true,
        ]);

        $this->postJson(route('blog.analytics'), [
            'event' => 'blog_scroll', 'post_id' => $post->getKey(),
            'post_slug' => $post->slug, 'percentage' => 75,
        ])->assertOk();

        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => 'blog_scroll', 'channel' => 'blog',
            'subject_id' => (string) $post->getKey(),
        ]);
    }

    public function test_blog_dashboard_has_overview_and_individual_post_panel(): void
    {
        $post = BlogPost::query()->create([
            'title' => 'Artigo analisado', 'slug' => 'artigo-analisado', 'excerpt' => 'Resumo',
            'content' => '<p>Conteúdo</p>', 'category' => 'Contábil',
            'status' => BlogPostStatus::Published->value, 'published_at' => now(),
            'should_index' => true,
        ]);

        PlatformAnalyticsEvent::query()->create([
            'event_name' => 'blog_post_view', 'channel' => 'blog',
            'subject_type' => 'blog_post', 'subject_id' => (string) $post->getKey(),
            'subject_slug' => $post->slug, 'metadata' => [], 'occurred_at' => now(),
        ]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('admin.blog.analytics'))->assertOk()->assertSee('Artigo analisado');
        $this->get(route('admin.blog.analytics.posts.show', $post))->assertOk()->assertSee('Artigo analisado');
    }
}
