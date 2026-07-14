<?php

namespace Tests\Feature\Blog;

use App\Blog\Enums\BlogPostStatus;
use App\Blog\Models\BlogPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class AdminBlogPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_draft_post(): void
    {
        $response = $this->post(route('admin.blog.posts.store'), [
            'title' => 'Guia do Simples Nacional',
            'excerpt' => 'Resumo do guia.',
            'content' => '<h2>Conteúdo</h2><p>Texto completo.</p>',
            'category' => 'Simples Nacional',
            'status' => BlogPostStatus::Draft->value,
            'should_index' => '1',
            'related_keywords' => 'das, anexo, fator r',
        ]);

        $post = BlogPost::query()->firstOrFail();

        $response->assertRedirect(route('admin.blog.posts.edit', $post));
        $this->assertSame('guia-do-simples-nacional', $post->slug);
        $this->assertSame(['das', 'anexo', 'fator r'], $post->related_keywords);
        $this->assertNull($post->published_at);
    }

    public function test_admin_can_publish_and_schedule_posts(): void
    {
        $published = $this->createDraft('publicar-agora');
        $scheduled = $this->createDraft('publicar-depois');

        $this->put(route('admin.blog.posts.update', $published), $this->payload([
            'title' => $published->title,
            'slug' => $published->slug,
            'status' => BlogPostStatus::Published->value,
        ]))->assertRedirect();

        $future = now()->addDay()->format('Y-m-d H:i:s');
        $this->put(route('admin.blog.posts.update', $scheduled), $this->payload([
            'title' => $scheduled->title,
            'slug' => $scheduled->slug,
            'status' => BlogPostStatus::Scheduled->value,
            'published_at' => $future,
        ]))->assertRedirect();

        $this->assertNotNull($published->fresh()->published_at);
        $this->assertSame(BlogPostStatus::Scheduled, $scheduled->fresh()->status);
    }

    public function test_admin_can_upload_and_delete_post_images(): void
    {
        Storage::fake('public');

        $this->post(route('admin.blog.posts.store'), $this->payload([
            'cover_image' => UploadedFile::fake()->image('capa.jpg'),
            'social_image' => UploadedFile::fake()->image('social.jpg'),
        ]));

        $post = BlogPost::query()->firstOrFail();
        Storage::disk('public')->assertExists($post->cover_image_path);
        Storage::disk('public')->assertExists($post->social_image_path);

        $this->delete(route('admin.blog.posts.destroy', $post))->assertRedirect(route('admin.blog.posts.index'));
        Storage::disk('public')->assertMissing($post->cover_image_path);
        Storage::disk('public')->assertMissing($post->social_image_path);
    }

    private function createDraft(string $slug): BlogPost
    {
        return BlogPost::query()->create([
            'title' => ucfirst(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'excerpt' => 'Resumo.',
            'content' => '<p>Conteúdo.</p>',
            'category' => 'Contabilidade',
            'status' => BlogPostStatus::Draft,
        ]);
    }

    /** @param array<string, mixed> $overrides */
    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Postagem de teste',
            'excerpt' => 'Resumo da postagem.',
            'content' => '<p>Conteúdo completo.</p>',
            'category' => 'Contabilidade',
            'status' => BlogPostStatus::Draft->value,
            'should_index' => '1',
        ], $overrides);
    }
}
