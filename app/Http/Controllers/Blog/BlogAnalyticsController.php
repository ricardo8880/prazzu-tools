<?php

namespace App\Http\Controllers\Blog;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class BlogAnalyticsController extends Controller
{
    public function store(Request $request, PlatformAnalytics $analytics): JsonResponse
    {
        $data = $request->validate([
            'event' => ['required', Rule::in([
                'blog_tool_click', 'blog_reading_started', 'blog_reading_completed',
                'blog_scroll', 'blog_time_spent', 'blog_abandoned', 'blog_share',
                'blog_download', 'blog_comment',
            ])],
            'post_id' => ['required', 'integer', 'exists:blog_posts,id'],
            'post_slug' => ['required', 'string', 'max:255'],
            'tool_slug' => ['nullable', 'required_if:event,blog_tool_click', 'string', 'max:255'],
            'percentage' => ['nullable', 'required_if:event,blog_scroll', 'integer', 'min:0', 'max:100'],
            'seconds' => ['nullable', 'required_if:event,blog_time_spent', 'integer', 'min:0', 'max:86400'],
            'method' => ['nullable', 'string', 'max:50'],
            'file' => ['nullable', 'string', 'max:255'],
        ]);

        $analytics->record($data['event'], 'blog', $request, array_filter([
            'subject_type' => 'blog_post',
            'subject_id' => $data['post_id'],
            'subject_slug' => $data['post_slug'],
            'tool_slug' => $data['tool_slug'] ?? null,
            'percentage' => $data['percentage'] ?? null,
            'seconds' => $data['seconds'] ?? null,
            'method' => $data['method'] ?? null,
            'file' => $data['file'] ?? null,
        ], static fn (mixed $value): bool => $value !== null));

        return response()->json(['recorded' => true]);
    }
}
