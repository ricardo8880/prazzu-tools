<?php

declare(strict_types=1);

namespace App\Http\Controllers\Analytics;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Analytics\Domain\Services\ToolAnalyticsMetadata;
use App\Core\Tools\ToolCatalog;
use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\TrackToolEventRequest;
use Illuminate\Http\Response;

final class TrackToolEventController extends Controller
{
    public function __invoke(
        TrackToolEventRequest $request,
        PlatformAnalytics $analytics,
        ToolCatalog $catalog,
        ToolAnalyticsMetadata $metadata,
    ): Response {
        $data = $request->validated();
        abort_if($catalog->find($data['tool']) === null, 404);

        $properties = $metadata->sanitize((array) ($data['metadata'] ?? []));

        if (isset($data['seconds'])) {
            $legacySecondsKey = $data['event'] === AnalyticsEventName::ToolTimeSpent->value
                ? 'time_spent_seconds'
                : 'abandoned_after_seconds';

            if (! isset($properties[$legacySecondsKey])) {
                $properties[$legacySecondsKey] = (int) $data['seconds'];
            }
        }

        $analytics->track(new AnalyticsEvent(
            name: $data['event'],
            channel: 'tool',
            properties: $properties,
            subjectType: 'tool',
            subjectSlug: $data['tool'],
            schemaVersion: (int) ($data['schema_version'] ?? 1),
        ), $request);

        return response()->noContent();
    }
}
