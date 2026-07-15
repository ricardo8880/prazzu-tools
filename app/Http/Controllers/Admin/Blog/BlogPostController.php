<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Blog\Enums\BlogPostStatus;
use App\Blog\Models\BlogCategory;
use App\Blog\Models\BlogPost;
use App\Blog\Seo\BlogSeoAnalyzer;
use App\Core\Tools\ToolCatalog;
use App\Core\Tools\ToolRegistry;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\SaveBlogPostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));

        $posts = BlogPost::query()
            ->with(['author', 'blogCategory'])
            ->when($search !== '', static function ($query) use ($search): void {
                $query->where(static function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('blogCategory', static fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($status !== '', static fn ($query) => $query->where('status', $status))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.blog.index', [
            'posts' => $posts,
            'search' => $search,
            'selectedStatus' => $status,
            'statuses' => BlogPostStatus::cases(),
        ]);
    }

    public function create(ToolRegistry $registry): View
    {
        return view('admin.blog.create', $this->formData(new BlogPost(), $registry));
    }

    public function store(SaveBlogPostRequest $request): RedirectResponse
    {
        $post = new BlogPost();
        $this->persist($post, $request);

        return redirect()
            ->route('admin.blog.posts.edit', $post)
            ->with('status', 'Postagem criada com sucesso.');
    }

    public function edit(BlogPost $post, ToolRegistry $registry): View
    {
        return view('admin.blog.edit', $this->formData($post, $registry));
    }

    public function update(SaveBlogPostRequest $request, BlogPost $post): RedirectResponse
    {
        $this->persist($post, $request);

        return redirect()
            ->route('admin.blog.posts.edit', $post)
            ->with('status', 'Postagem atualizada com sucesso.');
    }


    public function preview(BlogPost $post, ToolCatalog $toolCatalog): View
    {
        $relatedPosts = BlogPost::query()
            ->whereKeyNot($post->getKey())
            ->where('category', $post->category)
            ->latest('published_at')
            ->limit(3)
            ->get();

        $relatedTools = $post->relatedToolSlugs()
            ->map(static fn (string $slug): ?array => $toolCatalog->find($slug))
            ->filter()
            ->values();

        return view('blog.show', compact('post', 'relatedPosts', 'relatedTools'))
            ->with('isPreview', true);
    }

    public function destroy(BlogPost $post): RedirectResponse
    {
        $this->deleteStoredFile($post->cover_image_path);
        $this->deleteStoredFile($post->social_image_path);
        $post->delete();

        return redirect()
            ->route('admin.blog.posts.index')
            ->with('status', 'Postagem excluída com sucesso.');
    }

    /** @return array<string, mixed> */
    private function formData(BlogPost $post, ToolRegistry $registry, ?BlogSeoAnalyzer $seoAnalyzer = null): array
    {
        return [
            'post' => $post,
            'statuses' => BlogPostStatus::cases(),
            'tools' => $registry->manifests(),
            'selectedTools' => $post->relatedToolSlugs()->all(),
            'seoIssues' => ($seoAnalyzer ?? app(BlogSeoAnalyzer::class))->analyze($post),
            'categories' => BlogCategory::query()
                ->where(static function ($query) use ($post): void {
                    $query->where('is_active', true)
                        ->when($post->category_id, static fn ($query) => $query->orWhereKey($post->category_id));
                })
                ->orderBy('name')
                ->get(),
        ];
    }

    private function persist(BlogPost $post, SaveBlogPostRequest $request): void
    {
        $data = $request->validated();
        $data['slug'] = $this->resolveSlug($post, $data['slug'] ?? null, $data['title']);
        $data['author_id'] = $request->user()?->getKey() ?? $post->author_id;
        $data['is_featured'] = $request->boolean('is_featured');
        $data['should_index'] = $request->boolean('should_index');
        $category = BlogCategory::query()->findOrFail($data['category_id']);
        $data['category'] = $category->name;
        $data['related_keywords'] = collect(explode(',', (string) ($data['related_keywords'] ?? '')))
            ->map(static fn (string $keyword): string => trim($keyword))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $this->normalizePublication($data);

        if ($request->hasFile('cover_image')) {
            $this->deleteStoredFile($post->cover_image_path);
            $data['cover_image_path'] = $request->file('cover_image')->store('blog/covers', 'public');
        }

        if ($request->hasFile('social_image')) {
            $this->deleteStoredFile($post->social_image_path);
            $data['social_image_path'] = $request->file('social_image')->store('blog/social', 'public');
        }

        $relatedTools = Arr::pull($data, 'related_tools', []);
        unset($data['cover_image'], $data['social_image']);

        $post->fill($data)->save();
        $post->syncRelatedToolSlugs($relatedTools);
    }

    /** @param array<string, mixed> $data */
    private function normalizePublication(array &$data): void
    {
        $status = BlogPostStatus::from($data['status']);

        if ($status === BlogPostStatus::Draft) {
            $data['published_at'] = null;
            return;
        }

        if (empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if ($status === BlogPostStatus::Scheduled && now()->greaterThanOrEqualTo($data['published_at'])) {
            $data['status'] = BlogPostStatus::Published->value;
        }
    }

    private function resolveSlug(BlogPost $post, ?string $slug, string $title): string
    {
        $base = Str::slug($slug ?: $title);
        $candidate = $base;
        $suffix = 2;

        while (BlogPost::query()
            ->where('slug', $candidate)
            ->when($post->exists, static fn ($query) => $query->whereKeyNot($post->getKey()))
            ->exists()) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }

    private function deleteStoredFile(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
