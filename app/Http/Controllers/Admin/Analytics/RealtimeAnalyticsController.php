<?php

namespace App\Http\Controllers\Admin\Analytics;

use App\Core\Analytics\Application\Queries\RealtimeAnalyticsQuery;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class RealtimeAnalyticsController extends Controller
{
    public function index(RealtimeAnalyticsQuery $query): View
    {
        return view('admin.analytics.realtime', $query->execute());
    }

    public function data(Request $request, RealtimeAnalyticsQuery $query): JsonResponse
    {
        $payload = $query->execute();

        return response()->json([
            'generated_at' => $payload['generated_at']->toIso8601String(),
            'online_window_minutes' => $payload['online_window_minutes'],
            'activity_window_minutes' => $payload['activity_window_minutes'],
            'summary' => $payload['summary'],
            'pages' => $payload['pages']->values(),
            'tools' => $payload['tools']->values(),
            'sources' => $payload['sources']->values(),
            'locations' => $payload['locations']->values(),
            'events' => $payload['events']->map(static fn ($event): array => [
                'event_name' => $event->event_name,
                'channel' => $event->channel,
                'subject_slug' => $event->subject_slug,
                'path' => $event->path,
                'source' => $event->source,
                'device_type' => $event->device_type,
                'region' => $event->region,
                'city' => $event->city,
                'occurred_at' => $event->occurred_at?->toIso8601String(),
                'occurred_at_label' => $event->occurred_at?->format('H:i:s'),
            ])->values(),
        ])->header('Cache-Control', 'no-store, private');
    }
}
