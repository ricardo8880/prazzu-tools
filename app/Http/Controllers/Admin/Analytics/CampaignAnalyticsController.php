<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Core\Analytics\Application\Queries\CampaignAnalyticsQuery;
use App\Core\Analytics\Application\Services\AnalyticsQueryCache;
use App\Core\Analytics\Application\Services\CampaignAnalyticsInsightGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AcquisitionAnalyticsRequest;
use Illuminate\View\View;

final class CampaignAnalyticsController extends Controller
{
    public function __invoke(
        AcquisitionAnalyticsRequest $request,
        CampaignAnalyticsQuery $query,
        AnalyticsQueryCache $cache,
        CampaignAnalyticsInsightGenerator $insightGenerator,
    ): View {
        $period = $request->period();
        $data = $cache->remember('campaigns', $period, [], fn (): array => $query->execute($period));
        $previous = $cache->remember('campaigns', $period->previous(), [], fn (): array => $query->execute($period->previous()));

        return view('admin.analytics.campaigns', $data + [
            'previous' => $previous,
            'comparison' => $this->comparison($data['summary'], $previous['summary']),
            'campaign_insights' => $insightGenerator->generate($data, $previous),
            'selected_period' => $request->validated('period', '7'),
        ]);
    }

    /** @param array<string, int|float> $current @param array<string, int|float> $previous */
    private function comparison(array $current, array $previous): array
    {
        $result = [];
        foreach (['sessions', 'visitors', 'conversions', 'conversion_rate'] as $metric) {
            $before = (float) $previous[$metric];
            $result[$metric] = $before === 0.0
                ? ((float) $current[$metric] === 0.0 ? 0.0 : 100.0)
                : round((((float) $current[$metric] - $before) / $before) * 100, 1);
        }

        return $result;
    }
}
