<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Blog\Models\BlogPost;
use App\Core\Analytics\Application\Queries\BlogAnalyticsQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsDashboardRequest;
use Illuminate\View\View;

final class BlogPostAnalyticsController extends Controller
{
    public function __invoke(BlogPost $post, AnalyticsDashboardRequest $request, BlogAnalyticsQuery $query): View
    {
        return view('admin.blog.analytics-post', $query->post($post, $request->period()) + [
            'selected_period' => $request->selectedPeriod(),
        ]);
    }
}
