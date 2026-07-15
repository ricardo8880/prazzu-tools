<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Core\Analytics\Application\Queries\SeoAnalyticsQuery;
use App\Core\Analytics\Application\Services\AnalyticsQueryCache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsDashboardRequest;
use Illuminate\View\View;

final class SeoAnalyticsController extends Controller
{
    public function __invoke(AnalyticsDashboardRequest $request, SeoAnalyticsQuery $query, AnalyticsQueryCache $cache): View
    {
        $period = $request->period();
        $data = $cache->remember('seo', $period, [], fn (): array => $query->execute($period));

        return view('admin.analytics.seo', $data + [
            'selected_period' => $request->validated('period', '30'),
        ]);
    }
}
