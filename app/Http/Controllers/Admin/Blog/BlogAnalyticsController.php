<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

final class BlogAnalyticsController extends Controller
{
    public function __invoke(): View
    {
        $totals = PlatformAnalyticsEvent::query()->where('channel', 'blog')
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_post_view' THEN 1 ELSE 0 END) as views")
            ->selectRaw("SUM(CASE WHEN event_name = 'blog_tool_click' THEN 1 ELSE 0 END) as tool_clicks")
            ->first();

        $posts = PlatformAnalyticsEvent::query()
            ->where('channel', 'blog')->where('event_name', 'blog_post_view')
            ->select('subject_slug', DB::raw('COUNT(*) as total'))
            ->groupBy('subject_slug')->orderByDesc('total')->limit(20)->get();

        $tools = PlatformAnalyticsEvent::query()
            ->where('channel', 'blog')->where('event_name', 'blog_tool_click')
            ->get(['metadata'])
            ->groupBy(static fn (PlatformAnalyticsEvent $event): string => (string) ($event->metadata['tool_slug'] ?? 'desconhecida'))
            ->map(static fn ($events, string $toolSlug): object => (object) ['tool_slug' => $toolSlug, 'total' => $events->count()])
            ->sortByDesc('total')->take(20)->values();

        return view('admin.blog.analytics', compact('totals', 'posts', 'tools'));
    }
}
