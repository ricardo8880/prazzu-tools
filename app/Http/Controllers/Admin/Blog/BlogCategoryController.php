<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Blog\Models\BlogCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\SaveBlogCategoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class BlogCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $categories = BlogCategory::query()
            ->withCount('posts')
            ->when($search !== '', static function ($query) use ($search): void {
                $query->where(static function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.blog.categories.index', compact('categories', 'search'));
    }

    public function create(): View
    {
        return view('admin.blog.categories.create', [
            'category' => new BlogCategory,
        ]);
    }

    public function store(SaveBlogCategoryRequest $request): RedirectResponse
    {
        $category = new BlogCategory;
        $this->persist($category, $request);

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('status', 'Categoria criada com sucesso.');
    }

    public function edit(BlogCategory $category): View
    {
        return view('admin.blog.categories.edit', compact('category'));
    }

    public function update(SaveBlogCategoryRequest $request, BlogCategory $category): RedirectResponse
    {
        $this->persist($category, $request);

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('status', 'Categoria atualizada com sucesso.');
    }

    public function destroy(BlogCategory $category): RedirectResponse
    {
        if ($category->posts()->exists()) {
            return redirect()
                ->route('admin.blog.categories.index')
                ->withErrors(['category' => 'A categoria não pode ser excluída enquanto possuir postagens relacionadas.']);
        }

        $category->delete();

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('status', 'Categoria excluída com sucesso.');
    }

    private function persist(BlogCategory $category, SaveBlogCategoryRequest $request): void
    {
        $data = $request->validated();
        $data['name'] = trim($data['name']);
        $data['slug'] = $this->resolveSlug($category, $data['slug'] ?? null, $data['name']);
        $data['is_active'] = $request->boolean('is_active');

        $category->fill($data)->save();
    }

    private function resolveSlug(BlogCategory $category, ?string $slug, string $name): string
    {
        $base = Str::slug($slug ?: $name) ?: 'categoria';
        $candidate = $base;
        $suffix = 2;

        while (BlogCategory::query()
            ->where('slug', $candidate)
            ->when($category->exists, static fn ($query) => $query->whereKeyNot($category->getKey()))
            ->exists()) {
            $candidate = "{$base}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
