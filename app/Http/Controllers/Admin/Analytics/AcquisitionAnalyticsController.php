<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Core\Analytics\Application\Queries\AcquisitionAnalyticsQuery;
use App\Core\Analytics\Application\Services\AnalyticsQueryCache;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Analytics\AcquisitionAnalyticsRequest;
use Illuminate\View\View;

final class AcquisitionAnalyticsController extends Controller
{
    public function __invoke(AcquisitionAnalyticsRequest $request, AcquisitionAnalyticsQuery $query, AnalyticsQueryCache $cache): View
    {
        $period = $request->period();
        $data = $cache->remember('acquisition', $period, [], fn (): array => $query->execute($period));

        return view('admin.analytics.acquisition', $data + [
            'selected_period' => $request->validated('period', '7'),
        ]);
    }
}
