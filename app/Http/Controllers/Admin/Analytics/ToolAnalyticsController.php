<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Core\Analytics\Application\Queries\ToolAnalyticsQuery;
use App\Core\Analytics\Application\Services\AnalyticsQueryCache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsDashboardRequest;
use Illuminate\View\View;

final class ToolAnalyticsController extends Controller
{
    public function __invoke(AnalyticsDashboardRequest $request, ToolAnalyticsQuery $query, AnalyticsQueryCache $cache): View
    {
        $period = $request->period();
        $data = $cache->remember('tools', $period, [], fn (): array => $query->overview($period));

        return view('admin.analytics.tools', $data + [
            'selected_period' => $request->validated('period', '7'),
        ]);
    }
}
