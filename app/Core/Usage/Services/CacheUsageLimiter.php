<?php

namespace App\Core\Usage\Services;

use App\Core\Usage\Contracts\UsageLimiter;
use App\Core\Usage\Data\UsageDecision;
use App\Core\Usage\Data\UsageLimit;
use Illuminate\Contracts\Cache\Repository;

final readonly class CacheUsageLimiter implements UsageLimiter
{
    public function __construct(private Repository $cache) {}

    public function consume(string $toolSlug, string $subjectKey, UsageLimit $limit): UsageDecision
    {
        $bucket = intdiv(time(), $limit->windowSeconds);
        $key = sprintf('tool-usage:%s:%s:%d', $toolSlug, hash('sha256', $subjectKey), $bucket);
        $expiresAt = (($bucket + 1) * $limit->windowSeconds);

        $count = (int) $this->cache->get($key, 0);

        if ($count >= $limit->maxExecutions) {
            return new UsageDecision(false, 0, max(1, $expiresAt - time()));
        }

        $count++;
        $this->cache->put($key, $count, max(1, $expiresAt - time()));

        return new UsageDecision(true, max(0, $limit->maxExecutions - $count));
    }
}
