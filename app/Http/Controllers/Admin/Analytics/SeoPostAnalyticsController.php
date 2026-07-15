<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Blog\Models\BlogPost;
use App\Core\Analytics\Application\Queries\SeoAnalyticsQuery;
use App\Core\Analytics\Models\SeoMetricSnapshot;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsDashboardRequest;
use App\Http\Requests\Admin\Analytics\SeoMetricSnapshotRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class SeoPostAnalyticsController extends Controller
{
    public function show(BlogPost $post, AnalyticsDashboardRequest $request, SeoAnalyticsQuery $query): View
    {
        return view('admin.analytics.seo-post', $query->forPost($post, $request->period()) + ['selected_period' => $request->validated('period', '30')]);
    }

    public function store(BlogPost $post, SeoMetricSnapshotRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['country_code'] = $data['country_code'] ? strtoupper($data['country_code']) : null;
        SeoMetricSnapshot::query()->updateOrCreate([
            'blog_post_id' => $post->getKey(), 'metric_date' => $data['metric_date'],
            'source' => $data['source'], 'search_type' => $data['search_type'],
            'device' => $data['device'], 'country_code' => $data['country_code'],
        ], $data);

        return back()->with('status', 'Métricas de SEO registradas com sucesso.');
    }
}
