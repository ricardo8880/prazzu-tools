<?php

namespace Tests\Feature\Blog;

use App\Blog\Enums\BlogPostStatus;
use App\Blog\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BlogSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_post_exposes_canonical_open_graph_and_structured_data(): void
    {
        $post = BlogPost::query()->create([
            'title' => 'Como calcular o Simples Nacional',
            'slug' => 'como-calcular-simples-nacional',
            'excerpt' => 'Aprenda a calcular o Simples Nacional com segurança.',
            'content' => '<h2>Passo a passo</h2><p>Conteúdo completo.</p>',
            'category' => 'Simples Nacional',
            'status' => BlogPostStatus::Published,
            'published_at' => now()->subDay(),
            'meta_title' => 'Como calcular o Simples Nacional corretamente',
            'meta_description' => 'Entenda o cálculo do Simples Nacional, suas faixas, alíquota efetiva e os principais cuidados para calcular o DAS corretamente.',
            'should_index' => true,
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('rel="canonical"', false)
            ->assertSee('property="og:type" content="article"', false)
            ->assertSee('application/ld+json', false);
    }

    public function test_sitemap_only_lists_indexable_public_posts(): void
    {
        $visible = BlogPost::query()->create([
            'title' => 'Post indexável', 'slug' => 'post-indexavel', 'excerpt' => 'Resumo',
            'content' => '<p>Conteúdo</p>', 'category' => 'Fiscal',
            'status' => BlogPostStatus::Published, 'published_at' => now()->subDay(), 'should_index' => true,
        ]);
        $hidden = BlogPost::query()->create([
            'title' => 'Post oculto', 'slug' => 'post-oculto', 'excerpt' => 'Resumo',
            'content' => '<p>Conteúdo</p>', 'category' => 'Fiscal',
            'status' => BlogPostStatus::Published, 'published_at' => now()->subDay(), 'should_index' => false,
        ]);

        $this->get(route('blog.sitemap'))
            ->assertOk()
            ->assertSee(route('blog.show', $visible->slug), false)
            ->assertDontSee(route('blog.show', $hidden->slug), false);
    }
}
