<?php

namespace App\Core\Analytics\Services;

use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Http\Request;

final class DatabasePlatformAnalytics implements PlatformAnalytics
{
    public function record(string $eventName, string $channel, Request $request, array $metadata = []): void
    {
        PlatformAnalyticsEvent::query()->create([
            'event_name' => $eventName,
            'channel' => $channel,
            'subject_type' => $metadata['subject_type'] ?? null,
            'subject_id' => $metadata['subject_id'] ?? null,
            'subject_slug' => $metadata['subject_slug'] ?? null,
            'user_id' => $request->user()?->getKey(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
            'path' => '/'.$request->path(),
            'referrer' => $request->headers->get('referer'),
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }
}
