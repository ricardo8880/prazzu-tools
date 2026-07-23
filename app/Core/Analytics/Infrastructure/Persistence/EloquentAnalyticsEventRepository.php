<?php

namespace App\Core\Analytics\Infrastructure\Persistence;

use App\Core\Analytics\Contracts\AnalyticsEventRepository;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsContext;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;
use Illuminate\Database\QueryException;

final class EloquentAnalyticsEventRepository implements AnalyticsEventRepository
{
    public function __construct(
        private readonly AnalyticsEventNameResolver $eventNames,
        private readonly AnalyticsSchema $schema,
    ) {}

    public function store(AnalyticsEvent $event, AnalyticsContext $context): void
    {
        if (! $this->schema->isReady()) {
            return;
        }

        $canonicalName = $this->eventNames->canonical($event->name);
        $eventId = $event->identifier();

        if ($this->isDuplicate($eventId, $canonicalName, $event, $context)) {
            return;
        }

        try {
            PlatformAnalyticsEvent::query()->create([
                'event_id' => $eventId,
                'event_name' => $canonicalName,
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
                'acquisition_context_id' => $context->acquisition?->contextId,
                'acquisition_keyword' => $context->acquisition?->keyword,
                'acquisition_campaign_identifier' => $context->acquisition?->campaignIdentifier,
                'acquisition_primary_tool_slug' => $context->acquisition?->primaryToolSlug,
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
        } catch (QueryException $exception) {
            // A unique event_id makes retries idempotent, including concurrent requests.
            if (! str_contains(strtolower($exception->getMessage()), 'event_id')) {
                throw $exception;
            }
        }
    }

    private function isDuplicate(string $eventId, string $eventName, AnalyticsEvent $event, AnalyticsContext $context): bool
    {
        if (PlatformAnalyticsEvent::query()->where('event_id', $eventId)->exists()) {
            return true;
        }

        if (! config('analytics.deduplication.enabled', true)) {
            return false;
        }

        $windows = (array) config('analytics.deduplication.event_windows', []);
        $window = (int) ($windows[$eventName] ?? config('analytics.deduplication.default_window_seconds', 5));
        if ($window <= 0) {
            return false;
        }

        $query = PlatformAnalyticsEvent::query()
            ->where('event_name', $eventName)
            ->where('channel', $event->channel)
            ->where('occurred_at', '>=', $event->date()->subSeconds($window));

        $identities = array_filter([
            'analytics_session_id' => $context->analyticsSessionId,
            'visitor_id' => $context->visitorId,
            'user_id' => $context->userId,
            'session_id' => $context->laravelSessionId,
            // The analytics cookie is attached to the response. A second request made
            // before the client persists that cookie can receive fresh visitor/session
            // UUIDs. Keep the network fingerprint as an alternative identity, while
            // the event, subject, metadata and short time window still scope the match.
            'ip_hash' => $context->ipHash,
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        if ($identities !== []) {
            $query->where(function ($identityQuery) use ($identities): void {
                foreach ($identities as $column => $value) {
                    $identityQuery->orWhere($column, $value);
                }
            });
        } else {
            // Without any stable identity, two independent anonymous events
            // must not be merged merely because their payload is similar.
            return false;
        }

        $query->where('subject_type', $event->subjectType)
            ->where('subject_id', is_numeric($event->subjectId) ? (int) $event->subjectId : null)
            ->where('subject_slug', $event->subjectSlug);

        if ($event->subjectType === null) {
            $query->where('path', $context->path);
        }

        foreach (['percentage', 'tool_slug', 'placement', 'position', 'destination', 'method', 'file'] as $key) {
            if (array_key_exists($key, $event->properties)) {
                $query->where("metadata->$key", $event->properties[$key]);
            }
        }

        return $query->exists();
    }
}
