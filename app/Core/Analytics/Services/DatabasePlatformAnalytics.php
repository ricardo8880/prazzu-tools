<?php

namespace App\Core\Analytics\Services;

use App\Core\Analytics\Contracts\AnalyticsContextResolver;
use App\Core\Analytics\Contracts\AnalyticsEventRepository;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Domain\Events\AnalyticsEvent;
use App\Core\Analytics\Infrastructure\Http\AnalyticsCollectionPolicy;
use Illuminate\Http\Request;

final readonly class DatabasePlatformAnalytics implements PlatformAnalytics
{
    public function __construct(
        private AnalyticsContextResolver $contextResolver,
        private AnalyticsEventRepository $events,
        private AnalyticsCollectionPolicy $collectionPolicy,
    ) {}

    public function track(AnalyticsEvent $event, ?Request $request = null): void
    {
        if (! $this->collectionPolicy->shouldCollect($request)) {
            return;
        }

        $this->events->store($event, $this->contextResolver->resolve($request));
    }

    public function record(string $eventName, string $channel, Request $request, array $metadata = []): void
    {
        $this->track(AnalyticsEvent::make($eventName, $channel, $metadata), $request);
    }
}
