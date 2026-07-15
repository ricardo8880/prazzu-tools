<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Core\Analytics\Application\Queries\AudienceAnalyticsQuery;
use App\Core\Analytics\Application\Services\AnalyticsQueryCache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsDashboardRequest;
use Illuminate\View\View;

final class AudienceAnalyticsController extends Controller
{
    public function __invoke(AnalyticsDashboardRequest $request, AudienceAnalyticsQuery $query, AnalyticsQueryCache $cache): View
    {
        $period = $request->period();
        $data = $cache->remember('audience', $period, [], fn (): array => $query->execute($period));

        return view('admin.analytics.audience', $data + [
            'selected_period' => $request->validated('period', '30'),
        ]);
    }
}
