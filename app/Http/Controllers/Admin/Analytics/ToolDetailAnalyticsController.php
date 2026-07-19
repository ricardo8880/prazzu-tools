<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Core\Analytics\Application\Queries\ToolAnalyticsQuery;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsDashboardRequest;
use Illuminate\View\View;

final class ToolDetailAnalyticsController extends Controller
{
    public function __invoke(string $tool, AnalyticsDashboardRequest $request, ToolAnalyticsQuery $query): View
    {
        return view('admin.analytics.tool', $query->tool($tool, $request->period()) + ['selected_period' => $request->validated('period', '7')]);
    }
}
