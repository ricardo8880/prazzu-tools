<?php

namespace Tests\Feature\Blog;

use App\Blog\Enums\BlogPostStatus;
use App\Blog\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

final class BlogPostFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_persists_the_editorial_and_seo_foundation(): void
    {
        $author = User::factory()->create();

        $post = BlogPost::query()->create([
            'author_id' => $author->id,
            'title' => 'Como calcular a alíquota efetiva do Simples Nacional',
            'slug' => 'como-calcular-aliquota-efetiva-simples-nacional',
            'excerpt' => 'Entenda o cálculo da alíquota efetiva.',
            'content' => '<h2>Memória de cálculo</h2><p>Conteúdo completo.</p>',
            'category' => 'Simples Nacional',
            'status' => BlogPostStatus::Draft,
            'primary_keyword' => 'alíquota efetiva simples nacional',
            'related_keywords' => ['cálculo simples nacional', 'DAS'],
            'meta_title' => 'Alíquota efetiva do Simples Nacional: como calcular',
            'meta_description' => 'Aprenda a calcular a alíquota efetiva do Simples Nacional com exemplos claros.',
        ]);

        $this->assertTrue($post->author->is($author));
        $this->assertSame(BlogPostStatus::Draft, $post->status);
        $this->assertSame(['cálculo simples nacional', 'DAS'], $post->related_keywords);
        $this->assertFalse($post->isPubliclyAvailable(Carbon::parse('2026-07-14 12:00:00')));
        $this->assertTrue($author->blogPosts->contains($post));
    }

    public function test_only_published_posts_whose_date_has_arrived_are_public(): void
    {
        $now = Carbon::parse('2026-07-14 12:00:00');

        $published = $this->createPost('publicado', BlogPostStatus::Published, $now->copy()->subMinute());
        $this->createPost('futuro', BlogPostStatus::Published, $now->copy()->addMinute());
        $this->createPost('agendado', BlogPostStatus::Scheduled, $now->copy()->subMinute());
        $this->createPost('rascunho', BlogPostStatus::Draft, null);

        $visible = BlogPost::query()->publiclyAvailable($now)->pluck('id');

        $this->assertSame([$published->id], $visible->all());
        $this->assertTrue($published->isPubliclyAvailable($now));
    }

    public function test_it_relates_posts_to_tool_registry_slugs_without_coupling_to_a_tool_model(): void
    {
        $post = $this->createPost('ferramentas', BlogPostStatus::Draft, null);

        $post->syncRelatedToolSlugs([
            'calculadora-simples-nacional',
            'calculadora-margem-markup',
            'calculadora-simples-nacional',
        ]);

        $this->assertSame([
            'calculadora-margem-markup',
            'calculadora-simples-nacional',
        ], $post->relatedToolSlugs()->all());

        $post->delete();

        $this->assertDatabaseCount('blog_post_tool', 0);
    }

    private function createPost(string $slug, BlogPostStatus $status, ?Carbon $publishedAt): BlogPost
    {
        return BlogPost::query()->create([
            'title' => ucfirst($slug),
            'slug' => $slug,
            'excerpt' => 'Resumo da postagem.',
            'content' => '<p>Conteúdo.</p>',
            'category' => 'Contabilidade',
            'status' => $status,
            'published_at' => $publishedAt,
        ]);
    }
}
