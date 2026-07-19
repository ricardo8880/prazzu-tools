<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Core\Analytics\Application\Queries\FunnelAnalyticsQuery;
use App\Core\Analytics\Application\Services\AnalyticsQueryCache;
use App\Core\Analytics\Models\AnalyticsFunnel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AnalyticsDashboardRequest;
use App\Http\Requests\Admin\Analytics\StoreAnalyticsFunnelRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class FunnelAnalyticsController extends Controller
{
    public function index(AnalyticsDashboardRequest $request, FunnelAnalyticsQuery $query, AnalyticsQueryCache $cache): View
    {
        $filters = [
            'funnel' => $request->string('funnel')->toString() ?: null,
            'source' => $request->string('source')->toString() ?: null,
            'device_type' => $request->string('device_type')->toString() ?: null,
            'subject_slug' => $request->string('subject_slug')->toString() ?: null,
        ];

        $period = $request->period();
        $data = $cache->remember('funnels', $period, $filters, fn (): array => $query->execute($period, $filters));

        return view('admin.analytics.funnels', $data + [
            'selected_period' => $request->validated('period', '30'),
            'filters' => $filters,
        ]);
    }

    public function store(StoreAnalyticsFunnelRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $funnel = AnalyticsFunnel::query()->create($request->safe()->only(['name', 'description', 'identity_type']) + ['is_active' => true]);
            foreach ($request->parsedSteps() as $position => $step) {
                $funnel->steps()->create($step + ['position' => $position + 1]);
            }
        });

        return back()->with('status', 'Funil personalizado criado.');
    }

    public function destroy(Request $request, AnalyticsFunnel $funnel): RedirectResponse
    {
        $funnel->delete();

        return redirect()->route('admin.analytics.funnels')->with('status', 'Funil removido.');
    }
}
