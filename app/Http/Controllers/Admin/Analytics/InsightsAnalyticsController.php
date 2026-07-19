<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Core\Analytics\Application\Queries\InsightsAnalyticsQuery;
use App\Core\Analytics\Application\Services\AnalyticsInsightGenerator;
use App\Core\Analytics\Models\AnalyticsInsight;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsDashboardRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class InsightsAnalyticsController extends Controller
{
    public function index(AnalyticsDashboardRequest $request, InsightsAnalyticsQuery $query, AnalyticsInsightGenerator $generator): View
    {
        $period = $request->period();
        if (! AnalyticsInsight::query()->whereDate('period_start', $period->start->toDateString())->whereDate('period_end', $period->end->toDateString())->exists()) {
            $generator->generate($period);
        }

        return view('admin.analytics.insights', $query->execute($period, $request->string('type')->toString() ?: null, $request->string('severity')->toString() ?: null) + ['selected_period' => $request->validated('period', '7')]);
    }

    public function generate(AnalyticsDashboardRequest $request, AnalyticsInsightGenerator $generator): RedirectResponse
    {
        $count = $generator->generate($request->period());

        return back()->with('status', "$count insight(s) analisado(s) e atualizado(s).");
    }

    public function status(Request $request, AnalyticsInsight $insight): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|in:open,acknowledged,resolved']);
        $insight->update(['status' => $data['status'], 'resolved_at' => $data['status'] === 'resolved' ? now() : null]);

        return back()->with('status', 'Status do insight atualizado.');
    }
}
