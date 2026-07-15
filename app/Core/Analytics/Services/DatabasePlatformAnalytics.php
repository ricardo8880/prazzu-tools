<?php

namespace App\Core\Analytics\Services;

use App\Core\Analytics\Contracts\AnalyticsContextResolver;
use App\Core\Analytics\Contracts\AnalyticsEventRepository;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use Illuminate\Http\Request;

final readonly class DatabasePlatformAnalytics implements PlatformAnalytics
{
    public function __construct(
        private AnalyticsContextResolver $contextResolver,
        private AnalyticsEventRepository $events,
    ) {}

    public function track(AnalyticsEvent $event, ?Request $request = null): void
    {
        if (! config('analytics.enabled', true)) {
            return;
        }

        $this->events->store($event, $this->contextResolver->resolve($request));
    }

    public function record(string $eventName, string $channel, Request $request, array $metadata = []): void
    {
        $this->track(AnalyticsEvent::make($eventName, $channel, $metadata), $request);
    }
}
