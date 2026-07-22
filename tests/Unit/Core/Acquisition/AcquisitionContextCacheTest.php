<?php

namespace Tests\Unit\Core\Acquisition;

use App\Core\Acquisition\Infrastructure\Cache\AcquisitionContextCache;
use Illuminate\Contracts\Cache\Repository;
use Mockery;
use Tests\TestCase;

final class AcquisitionContextCacheTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_uses_configured_ttl_in_seconds(): void
    {
        config(['acquisition.cache_ttl' => 75]);

        $repository = Mockery::mock(Repository::class);
        $repository->shouldReceive('get')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturn(null);
        $repository->shouldReceive('put')
            ->once()
            ->with(Mockery::type('string'), ['value' => null], 75);

        $cache = new AcquisitionContextCache($repository);

        self::assertNull($cache->remember('campanha', static fn () => null));
    }
}
