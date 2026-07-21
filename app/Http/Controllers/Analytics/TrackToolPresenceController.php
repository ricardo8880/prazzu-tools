<?php

namespace App\Http\Controllers\Analytics;

use App\Core\Analytics\Models\AnalyticsSession;
use App\Core\Analytics\Models\AnalyticsToolPresence;
use App\Core\Tools\ToolCatalog;
use App\Http\Controllers\Controller;
use App\Http\Requests\Analytics\TrackToolPresenceRequest;
use Illuminate\Http\Response;

final class TrackToolPresenceController extends Controller
{
    public function __invoke(TrackToolPresenceRequest $request, ToolCatalog $catalog): Response
    {
        $data = $request->validated();
        abort_if($catalog->find($data['tool']) === null, 404);

        if ($data['action'] === 'leave') {
            AnalyticsToolPresence::query()->whereKey($data['presence_id'])->delete();

            return response()->noContent();
        }

        $sessionId = $request->attributes->get('analytics.session_id');
        $visitorId = $request->attributes->get('analytics.visitor_id');
        $session = is_string($sessionId) ? AnalyticsSession::query()->find($sessionId) : null;

        AnalyticsToolPresence::query()->updateOrCreate(
            ['id' => $data['presence_id']],
            [
                'tool_slug' => $data['tool'],
                'visitor_id' => is_string($visitorId) ? $visitorId : null,
                'analytics_session_id' => is_string($sessionId) ? $sessionId : null,
                'user_id' => $request->user()?->getAuthIdentifier(),
                'path' => '/'.$request->path(),
                'source' => $session?->source,
                'country_code' => $session?->country_code,
                'region' => $session?->region,
                'city' => $session?->city,
                'last_seen_at' => now(),
            ],
        );

        return response()->noContent();
    }
}
