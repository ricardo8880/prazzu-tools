<?php

namespace App\Http\Controllers\Analytics;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Tools\ToolCatalog;
use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\TrackToolEventRequest;
use Illuminate\Http\Response;

final class TrackToolEventController extends Controller
{
    public function __invoke(TrackToolEventRequest $request, PlatformAnalytics $analytics, ToolCatalog $catalog): Response
    {
        $data = $request->validated();
        abort_if($catalog->find($data['tool']) === null, 404);
        $analytics->track(new AnalyticsEvent(name: $data['event'], channel: 'tool', properties: array_filter(['seconds' => $data['seconds'] ?? null], fn ($v) => $v !== null), subjectType: 'tool', subjectSlug: $data['tool']), $request);

        return response()->noContent();
    }
}
