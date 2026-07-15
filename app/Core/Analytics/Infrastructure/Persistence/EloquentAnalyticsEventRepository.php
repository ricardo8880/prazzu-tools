<?php

namespace App\Core\Analytics\Infrastructure\Persistence;

use App\Core\Analytics\Contracts\AnalyticsEventRepository;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsContext;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;

final class EloquentAnalyticsEventRepository implements AnalyticsEventRepository
{
    public function __construct(private readonly AnalyticsEventNameResolver $eventNames) {}
    public function store(AnalyticsEvent $event, AnalyticsContext $context): void
    {
        PlatformAnalyticsEvent::query()->create([
            'event_id' => $event->identifier(),
            'event_name' => $this->eventNames->canonical($event->name),
            'schema_version' => 1,
            'channel' => $event->channel,
            'subject_type' => $event->subjectType,
            'subject_id' => is_numeric($event->subjectId) ? (int) $event->subjectId : null,
            'subject_slug' => $event->subjectSlug,
            'visitor_id' => $context->visitorId,
            'analytics_session_id' => $context->analyticsSessionId,
            'user_id' => $context->userId,
            'session_id' => $context->laravelSessionId,
            'url' => $context->url,
            'path' => $context->path,
            'referrer' => $context->referrer,
            'source' => $context->source,
            'medium' => $context->medium,
            'campaign' => $context->campaign,
            'utm_source' => $context->utm['source'] ?? null,
            'utm_medium' => $context->utm['medium'] ?? null,
            'utm_campaign' => $context->utm['campaign'] ?? null,
            'utm_term' => $context->utm['term'] ?? null,
            'utm_content' => $context->utm['content'] ?? null,
            'device_type' => $context->deviceType,
            'browser' => $context->browser,
            'operating_system' => $context->operatingSystem,
            'language' => $context->language,
            'timezone' => $context->timezone,
            'screen_resolution' => $context->screenResolution,
            'country_code' => $context->countryCode,
            'region' => $context->region,
            'city' => $context->city,
            'ip_hash' => $context->ipHash,
            'user_agent' => $context->userAgent,
            'metadata' => $event->properties,
            'occurred_at' => $event->date(),
        ]);
    }
}
