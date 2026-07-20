<?php

namespace Tests\Architecture;

use Tests\TestCase;

class TestingEnvironmentIsolationTest extends TestCase
{
    public function test_test_suite_is_isolated_from_the_local_environment(): void
    {
        $this->assertTrue($this->app->environment('testing'));
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->assertSame('array', config('session.driver'));
        $this->assertSame('array', config('cache.default'));
        $this->assertSame('sync', config('queue.default'));

        $cachedConfigPath = str_replace('\\', '/', $this->app->getCachedConfigPath());

        $this->assertStringEndsWith(
            'bootstrap/cache/config-testing.php',
            $cachedConfigPath,
        );
        $this->assertFalse($this->app->configurationIsCached());
    }
}
