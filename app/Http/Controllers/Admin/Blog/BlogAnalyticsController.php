<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Core\Analytics\Application\Queries\BlogAnalyticsQuery;
use App\Core\Analytics\Application\Services\AnalyticsQueryCache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsDashboardRequest;
use Illuminate\View\View;

final class BlogAnalyticsController extends Controller
{
    public function __invoke(AnalyticsDashboardRequest $request, BlogAnalyticsQuery $query, AnalyticsQueryCache $cache): View
    {
        $period = $request->period();
        $data = $cache->remember('blog', $period, [], fn (): array => $query->overview($period));

        return view('admin.blog.analytics', $data + [
            'selected_period' => $request->selectedPeriod(),
        ]);
    }
}
