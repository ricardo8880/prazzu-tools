<?php

namespace App\Core\Analytics\Application\Services;

use App\Core\Analytics\Domain\Enums\AnalyticsEventName;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsContext;
use App\Core\Analytics\Models\PlatformAnalyticsEvent;

final readonly class BlogConversionAttribution
{
    public function __construct(private AnalyticsEventNameResolver $eventNames) {}

    public function enrich(AnalyticsEvent $event, AnalyticsContext $context): AnalyticsEvent
    {
        if (! in_array($this->eventNames->canonical($event->name), [
            AnalyticsEventName::AccountCreated->value,
            AnalyticsEventName::SubscriptionCreated->value,
        ], true)) {
            return $event;
        }

        if (array_key_exists('attributed_blog_post_id', $event->properties)) {
            return $event;
        }

        $query = PlatformAnalyticsEvent::query()
            ->whereIn('event_name', $this->eventNames->expand([AnalyticsEventName::BlogPostViewed]))
            ->where('subject_type', 'blog_post')
            ->whereNotNull('subject_id');

        if ($context->analyticsSessionId !== null) {
            $query->where('analytics_session_id', $context->analyticsSessionId);
        } elseif ($context->visitorId !== null) {
            $query->where('visitor_id', $context->visitorId);
        } else {
            return $event;
        }

        $touchpoint = $query->latest('occurred_at')->latest('id')->first(['subject_id', 'subject_slug']);
        if ($touchpoint === null) {
            return $event;
        }

        return new AnalyticsEvent(
            name: $event->name,
            channel: $event->channel,
            properties: [
                ...$event->properties,
                'attributed_blog_post_id' => (int) $touchpoint->subject_id,
                'attributed_blog_post_slug' => $touchpoint->subject_slug,
            ],
            subjectType: $event->subjectType,
            subjectId: $event->subjectId,
            subjectSlug: $event->subjectSlug,
            occurredAt: $event->occurredAt,
            eventId: $event->eventId,
            schemaVersion: $event->schemaVersion,
        );
    }
}
