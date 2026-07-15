<?php

namespace Tests\Feature\Blog;

use App\Blog\Enums\BlogPostStatus;
use App\Blog\Models\BlogCategory;
use App\Blog\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminBlogCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_a_blog_category(): void
    {
        $this->post(route('admin.blog.categories.store'), [
            'name' => 'Simples Nacional',
            'description' => 'Conteúdos sobre o regime tributário.',
            'is_active' => '1',
        ])->assertRedirect(route('admin.blog.categories.index'));

        $category = BlogCategory::query()->firstOrFail();

        $this->assertSame('simples-nacional', $category->slug);
        $this->assertTrue($category->is_active);

        $this->put(route('admin.blog.categories.update', $category), [
            'name' => 'Tributação no Simples Nacional',
            'slug' => 'tributacao-simples-nacional',
            'description' => 'Conteúdos atualizados.',
            'is_active' => '1',
        ])->assertRedirect(route('admin.blog.categories.index'));

        $this->assertSame('Tributação no Simples Nacional', $category->fresh()->name);
    }

    public function test_category_in_use_cannot_be_deleted(): void
    {
        $category = BlogCategory::query()->create([
            'name' => 'Contabilidade',
            'slug' => 'contabilidade',
            'is_active' => true,
        ]);

        BlogPost::query()->create([
            'title' => 'Postagem',
            'slug' => 'postagem',
            'excerpt' => 'Resumo.',
            'content' => '<p>Conteúdo.</p>',
            'category_id' => $category->getKey(),
            'category' => $category->name,
            'status' => BlogPostStatus::Draft,
        ]);

        $this->delete(route('admin.blog.categories.destroy', $category))
            ->assertRedirect(route('admin.blog.categories.index'))
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('blog_categories', ['id' => $category->getKey()]);
    }
}
