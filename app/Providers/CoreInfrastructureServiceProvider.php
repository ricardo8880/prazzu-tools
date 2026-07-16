<?php

namespace App\Providers;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessGate;
use App\Core\Analytics\Contracts\AnalyticsContextResolver;
use App\Core\Analytics\Contracts\AnalyticsEventRepository;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Infrastructure\Http\RequestAnalyticsContextResolver;
use App\Core\Analytics\Infrastructure\Persistence\EloquentAnalyticsEventRepository;
use App\Core\Analytics\Services\DatabasePlatformAnalytics;
use App\Core\Access\Services\ConfigCommercialAccessPolicy;
use App\Core\Access\Services\DefaultToolAccessGate;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Core\FeatureFlags\Services\ConfigFeatureFlagRepository;
use App\Core\Identity\Contracts\PrazzuIdentityLinker;
use App\Core\Identity\Services\ImmutablePrazzuIdentityLinker;
use App\Core\Integrations\Contracts\ExternalServiceClient;
use App\Core\Integrations\Services\LaravelHttpServiceClient;
use App\Core\Usage\Contracts\UsageLimiter;
use App\Core\Usage\Contracts\UsageMetrics;
use App\Core\Usage\Services\CacheUsageLimiter;
use App\Core\Usage\Services\DatabaseUsageMetrics;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\ServiceProvider;

final class CoreInfrastructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FeatureFlagRepository::class, ConfigFeatureFlagRepository::class);
        $this->app->singleton(CommercialAccessPolicy::class, ConfigCommercialAccessPolicy::class);
        $this->app->singleton(ToolAccessGate::class, DefaultToolAccessGate::class);
        $this->app->singleton(AnalyticsContextResolver::class, RequestAnalyticsContextResolver::class);
        $this->app->singleton(AnalyticsEventRepository::class, EloquentAnalyticsEventRepository::class);
        $this->app->singleton(PlatformAnalytics::class, DatabasePlatformAnalytics::class);

        $this->app->singleton(UsageLimiter::class, function ($app): UsageLimiter {
            return new CacheUsageLimiter($app->make('cache.store'));
        });

        $this->app->singleton(UsageMetrics::class, DatabaseUsageMetrics::class);
        $this->app->bind(PrazzuIdentityLinker::class, ImmutablePrazzuIdentityLinker::class);
        $this->app->bind(ExternalServiceClient::class, LaravelHttpServiceClient::class);
        $this->app->alias('cache.store', CacheRepository::class);
    }
}
