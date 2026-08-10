<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Blog\Enums\BlogPostStatus;
use App\Blog\Models\BlogCategory;
use App\Blog\Models\BlogPost;
use App\Blog\Seo\BlogSeoAnalyzer;
use App\Core\Tools\ToolCatalog;
use App\Core\Tools\ToolRegistry;
use App\Core\Verticals\Application\VerticalContext;
use App\Core\Verticals\Contracts\VerticalRegistry;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\SaveBlogPostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class BlogPostController extends Controller
{
    public function index(Request $request, VerticalRegistry $verticalRegistry): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $selectedVertical = trim((string) $request->query('vertical', ''));

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
            ->when($selectedVertical !== '', static fn ($query) => $query->where('vertical_slug', $selectedVertical))
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.blog.index', [
            'posts' => $posts,
            'search' => $search,
            'selectedStatus' => $status,
            'statuses' => BlogPostStatus::cases(),
            'verticals' => $verticalRegistry->all(),
            'selectedVertical' => $selectedVertical,
        ]);
    }

    public function create(ToolRegistry $registry, VerticalRegistry $verticalRegistry): View
    {
        return view('admin.blog.create', $this->formData(new BlogPost, $registry, verticalRegistry: $verticalRegistry));
    }

    public function store(SaveBlogPostRequest $request, VerticalContext $verticalContext, ToolRegistry $registry): RedirectResponse
    {
        $post = new BlogPost;
        $this->persist($post, $request, $verticalContext, $registry);

        return redirect()
            ->route('admin.blog.posts.edit', $post)
            ->with('status', 'Postagem criada com sucesso.');
    }

    public function edit(BlogPost $post, ToolRegistry $registry, VerticalRegistry $verticalRegistry): View
    {
        return view('admin.blog.edit', $this->formData($post, $registry, verticalRegistry: $verticalRegistry));
    }

    public function update(SaveBlogPostRequest $request, BlogPost $post, VerticalContext $verticalContext, ToolRegistry $registry): RedirectResponse
    {
        $this->persist($post, $request, $verticalContext, $registry);

        return redirect()
            ->route('admin.blog.posts.edit', $post)
            ->with('status', 'Postagem atualizada com sucesso.');
    }

    public function preview(BlogPost $post, ToolCatalog $toolCatalog): View
    {
        $relatedPosts = BlogPost::query()
            ->forVertical($post->vertical_slug)
            ->whereKeyNot($post->getKey())
            ->where('category', $post->category)
            ->latest('published_at')
            ->limit(3)
            ->get();

        $toolsForPostVertical = $toolCatalog->forVertical($post->vertical_slug)->keyBy('slug');
        $relatedTools = $post->relatedToolSlugs()
            ->map(static fn (string $slug): ?array => $toolsForPostVertical->get($slug))
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
    private function formData(BlogPost $post, ToolRegistry $registry, ?BlogSeoAnalyzer $seoAnalyzer = null, ?VerticalRegistry $verticalRegistry = null): array
    {
        $verticalRegistry ??= app(VerticalRegistry::class);
        $selectedVertical = (string) old('vertical_slug', $post->vertical_slug ?: config('verticals.default'));

        return [
            'post' => $post,
            'verticals' => $verticalRegistry->all(),
            'statuses' => BlogPostStatus::cases(),
            'tools' => collect($registry->manifests())
                ->filter(static fn ($tool): bool => $tool->vertical === $selectedVertical)
                ->values(),
            'selectedTools' => $post->relatedToolSlugs()->all(),
            'seoIssues' => ($seoAnalyzer ?? app(BlogSeoAnalyzer::class))->analyze($post),
            'categories' => BlogCategory::query()
                ->where('vertical_slug', $selectedVertical)
                ->where(static function ($query) use ($post): void {
                    $query->where('is_active', true)
                        ->when($post->category_id, static fn ($query) => $query->orWhereKey($post->category_id));
                })
                ->orderBy('name')
                ->get(),
        ];
    }

    private function persist(BlogPost $post, SaveBlogPostRequest $request, VerticalContext $verticalContext, ToolRegistry $registry): void
    {
        $data = $request->validated();
        $data['vertical_slug'] = (string) ($data['vertical_slug'] ?? $post->vertical_slug ?: $verticalContext->slug() ?: config('verticals.default'));
        $data['slug'] = $this->resolveSlug($post, $data['slug'] ?? null, $data['title']);
        $data['author_id'] = $request->user()?->getKey() ?? $post->author_id;
        $data['is_featured'] = $request->boolean('is_featured');
        $data['should_index'] = $request->boolean('should_index');
        $category = BlogCategory::query()->where('vertical_slug', $data['vertical_slug'])->findOrFail($data['category_id']);
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

        $relatedTools = array_values(Arr::pull($data, 'related_tools', []));
        $allowedToolSlugs = collect($registry->manifests())
            ->filter(static fn ($tool): bool => $tool->vertical === $data['vertical_slug'])
            ->pluck('slug')
            ->all();

        if (array_diff($relatedTools, $allowedToolSlugs) !== []) {
            throw ValidationException::withMessages([
                'related_tools' => 'Selecione apenas ferramentas pertencentes à mesma vertical da postagem.',
            ]);
        }
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
