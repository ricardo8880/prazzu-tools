<?php

namespace Tests\Feature\Blog;

use App\Blog\Enums\BlogPostStatus;
use App\Blog\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class PublicBlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_blog_lists_only_public_posts(): void
    {
        $published = $this->createPost('artigo-publicado', BlogPostStatus::Published, now()->subMinute());
        $this->createPost('rascunho', BlogPostStatus::Draft, null);
        $this->createPost('publicacao-futura', BlogPostStatus::Published, now()->addDay());

        $this->get(route('blog.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee('Rascunho')
            ->assertDontSee('Publicacao futura');
    }

    public function test_it_filters_posts_by_search_and_category(): void
    {
        $this->createPost('simples-nacional', BlogPostStatus::Published, now()->subMinute(), 'Fiscal', 'Como calcular o DAS');
        $this->createPost('documentos', BlogPostStatus::Published, now()->subMinute(), 'Organização', 'Organize documentos');

        $this->get(route('blog.index', ['q' => 'DAS', 'categoria' => 'Fiscal']))
            ->assertOk()
            ->assertSee('Como calcular o DAS')
            ->assertDontSee('Organize documentos');
    }

    public function test_a_public_post_has_its_article_page_and_drafts_return_not_found(): void
    {
        $published = $this->createPost('guia-publico', BlogPostStatus::Published, now()->subMinute());
        $draft = $this->createPost('guia-privado', BlogPostStatus::Draft, null);

        $this->get(route('blog.show', $published->slug))
            ->assertOk()
            ->assertSee($published->title)
            ->assertSee('Conteúdo completo', false);

        $this->get(route('blog.show', $draft->slug))->assertNotFound();
    }

    public function test_article_page_resolves_related_tools_from_the_central_catalog(): void
    {
        $post = $this->createPost('artigo-com-ferramenta', BlogPostStatus::Published, now()->subMinute());
        $post->syncRelatedToolSlugs(['calculadora-margem-markup']);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('Ferramentas relacionadas');
    }

    private function createPost(
        string $slug,
        BlogPostStatus $status,
        mixed $publishedAt,
        string $category = 'Contabilidade',
        ?string $title = null,
    ): BlogPost {
        return BlogPost::query()->create([
            'title' => $title ?? ucfirst(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'excerpt' => 'Resumo útil para o leitor.',
            'content' => '<h2>Conteúdo completo</h2><p>Explicação do artigo.</p>',
            'category' => $category,
            'status' => $status,
            'published_at' => $publishedAt,
        ]);
    }
}
