<?php

namespace Tests\Unit\Core\Usage;

use App\Core\Usage\Data\UsageLimit;
use App\Core\Usage\Services\CacheUsageLimiter;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use PHPUnit\Framework\TestCase;

final class CacheUsageLimiterTest extends TestCase
{
    public function test_blocks_subject_after_limit_is_consumed(): void
    {
        $limiter = new CacheUsageLimiter(new Repository(new ArrayStore));
        $limit = new UsageLimit(2, 3600);

        self::assertTrue($limiter->consume('teste', 'usuario:1', $limit)->allowed);
        self::assertTrue($limiter->consume('teste', 'usuario:1', $limit)->allowed);

        $blocked = $limiter->consume('teste', 'usuario:1', $limit);
        self::assertFalse($blocked->allowed);
        self::assertSame(0, $blocked->remaining);
        self::assertGreaterThan(0, $blocked->retryAfterSeconds);
    }

    public function test_limits_are_isolated_by_tool_and_subject(): void
    {
        $limiter = new CacheUsageLimiter(new Repository(new ArrayStore));
        $limit = new UsageLimit(1, 3600);

        self::assertTrue($limiter->consume('a', 'usuario:1', $limit)->allowed);
        self::assertTrue($limiter->consume('b', 'usuario:1', $limit)->allowed);
        self::assertTrue($limiter->consume('a', 'usuario:2', $limit)->allowed);
    }
}
