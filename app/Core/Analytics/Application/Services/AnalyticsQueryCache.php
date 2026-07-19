<?php

namespace App\Core\Analytics\Application\Services;

use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use Closure;
use Illuminate\Contracts\Cache\Repository;

final readonly class AnalyticsQueryCache
{
    public function __construct(private Repository $cache) {}

    /**
     * @template T
     *
     * @param array<string, mixed> $filters
     * @param Closure(): T $callback
     * @return T
     */
    public function remember(string $report, AnalyticsPeriod $period, array $filters, Closure $callback): mixed
    {
        $ttl = max(0, (int) config('analytics.performance.dashboard_cache_seconds', 60));

        if ($ttl === 0) {
            return $callback();
        }

        ksort($filters);
        $key = sprintf(
            'analytics:report:%s:%s',
            preg_replace('/[^a-z0-9._-]/i', '-', $report),
            hash('sha256', json_encode([
                'start' => $period->start->toIso8601String(),
                'end' => $period->end->toIso8601String(),
                'filters' => $filters,
            ], JSON_THROW_ON_ERROR)),
        );

        return $this->cache->remember($key, $ttl, $callback);
    }
}
