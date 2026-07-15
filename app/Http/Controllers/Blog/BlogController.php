<?php

namespace App\Http\Controllers\Blog;

use App\Blog\Models\BlogPost;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Tools\ToolCatalog;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

final class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $category = trim((string) $request->string('categoria'));

        $query = BlogPost::query()->publiclyAvailable();

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query
                    ->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('primary_keyword', 'like', "%{$search}%");
            });
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        /** @var LengthAwarePaginator<int, BlogPost> $posts */
        $posts = $query->paginate(9)->withQueryString();

        $categories = BlogPost::query()
            ->publiclyAvailable()
            ->selectRaw('category, COUNT(*) as posts_count')
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        $featured = BlogPost::query()
            ->publiclyAvailable()
            ->where('is_featured', true)
            ->first();

        return view('blog.index', compact('posts', 'categories', 'featured', 'search', 'category'));
    }

    public function show(string $slug, Request $request, ToolCatalog $toolCatalog, PlatformAnalytics $analytics): View
    {
        $post = BlogPost::query()
            ->publiclyAvailable()
            ->where('slug', $slug)
            ->firstOrFail();

        $analytics->record(AnalyticsEventName::BlogPostViewed->value, 'blog', $request, [
            'subject_type' => 'blog_post',
            'subject_id' => $post->getKey(),
            'subject_slug' => $post->slug,
        ]);

        $relatedPosts = BlogPost::query()
            ->publiclyAvailable()
            ->whereKeyNot($post->getKey())
            ->where('category', $post->category)
            ->limit(3)
            ->get();

        $relatedTools = $post->relatedToolSlugs()
            ->map(fn (string $toolSlug): ?array => $toolCatalog->find($toolSlug))
            ->filter()
            ->values();

        return view('blog.show', compact('post', 'relatedPosts', 'relatedTools'));
    }
}
