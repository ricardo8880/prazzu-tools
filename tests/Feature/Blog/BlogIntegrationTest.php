<?php

namespace Tests\Feature\Blog;

use App\Blog\Enums\BlogPostStatus;
use App\Blog\Models\BlogPost;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BlogIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_article_view_is_recorded(): void
    {
        $post = BlogPost::query()->create([
            'title' => 'Guia contábil', 'slug' => 'guia-contabil', 'excerpt' => 'Resumo',
            'content' => '<p>Conteúdo completo.</p>', 'category' => 'Contabilidade',
            'status' => BlogPostStatus::Published, 'published_at' => now(), 'should_index' => true,
        ]);

        $this->get(route('blog.show', $post->slug))->assertOk();

        $this->assertDatabaseHas('platform_analytics_events', [
            'event_name' => 'blog.post.viewed', 'channel' => 'blog', 'subject_id' => $post->getKey(),
        ]);
    }

    public function test_tool_click_event_is_recorded(): void
    {
        $this->postJson(route('blog.analytics'), [
            'event' => 'blog_tool_click', 'post_id' => 10,
            'post_slug' => 'artigo', 'tool_slug' => 'calculadora-simples-nacional',
        ])->assertOk()->assertJson(['recorded' => true]);

        $event = PlatformAnalyticsEvent::query()->firstOrFail();
        self::assertSame('calculadora-simples-nacional', $event->metadata['tool_slug']);
    }

    public function test_admin_routes_are_available_locally_and_preview_drafts(): void
    {
        $post = BlogPost::query()->create([
            'title' => 'Rascunho', 'slug' => 'rascunho', 'excerpt' => 'Resumo',
            'content' => '<p>Conteúdo.</p>', 'category' => 'Fiscal',
            'status' => BlogPostStatus::Draft, 'should_index' => false,
        ]);

        $this->get(route('admin.blog.posts.preview', $post))
            ->assertOk()->assertSee('Pré-visualização administrativa');
    }
}
