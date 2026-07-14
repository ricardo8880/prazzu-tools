<?php

namespace App\Http\Controllers\Blog;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class BlogAnalyticsController extends Controller
{
    public function store(Request $request, PlatformAnalytics $analytics): JsonResponse
    {
        $data = $request->validate([
            'event' => ['required', 'in:blog_tool_click'],
            'post_id' => ['required', 'integer'],
            'post_slug' => ['required', 'string', 'max:255'],
            'tool_slug' => ['required', 'string', 'max:255'],
        ]);

        $analytics->record($data['event'], 'blog', $request, [
            'subject_type' => 'blog_post',
            'subject_id' => $data['post_id'],
            'subject_slug' => $data['post_slug'],
            'tool_slug' => $data['tool_slug'],
        ]);

        return response()->json(['recorded' => true]);
    }
}
