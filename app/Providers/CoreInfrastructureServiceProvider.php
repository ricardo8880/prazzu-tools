<?php

namespace App\Providers;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\Access\Contracts\ToolAccessGate;
use App\Core\Access\Contracts\ToolFeatureAccessGate;
use App\Core\Access\Services\ConfigCommercialAccessPolicy;
use App\Core\Access\Services\DefaultToolAccessContextResolver;
use App\Core\Access\Services\DefaultToolAccessGate;
use App\Core\Access\Services\DefaultToolFeatureAccessGate;
use App\Core\Analytics\Contracts\AnalyticsContextResolver;
use App\Core\Analytics\Contracts\AnalyticsEventRepository;
use App\Core\Analytics\Contracts\PlatformAnalytics;
use App\Core\Analytics\Infrastructure\Http\RequestAnalyticsContextResolver;
use App\Core\Analytics\Infrastructure\Persistence\AnalyticsSchema;
use App\Core\Analytics\Infrastructure\Persistence\EloquentAnalyticsEventRepository;
use App\Core\Analytics\Services\DatabasePlatformAnalytics;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Core\FeatureFlags\Services\ConfigFeatureFlagRepository;
use App\Core\Identity\Contracts\PrazzuIdentityLinker;
use App\Core\Identity\Services\ImmutablePrazzuIdentityLinker;
use App\Core\Integrations\Contracts\ExternalServiceClient;
use App\Core\Integrations\Services\LaravelHttpServiceClient;
use App\Core\Usage\Contracts\UsageMetrics;
use App\Core\Usage\Services\DatabaseUsageMetrics;
use Illuminate\Support\ServiceProvider;

final class CoreInfrastructureServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FeatureFlagRepository::class, ConfigFeatureFlagRepository::class);
        $this->app->singleton(CommercialAccessPolicy::class, ConfigCommercialAccessPolicy::class);
        $this->app->singleton(ToolAccessGate::class, DefaultToolAccessGate::class);
        $this->app->singleton(ToolFeatureAccessGate::class, DefaultToolFeatureAccessGate::class);
        $this->app->singleton(ToolAccessContextResolver::class, DefaultToolAccessContextResolver::class);
        $this->app->singleton(AnalyticsContextResolver::class, RequestAnalyticsContextResolver::class);
        $this->app->singleton(AnalyticsEventRepository::class, EloquentAnalyticsEventRepository::class);
        $this->app->singleton(AnalyticsSchema::class);
        $this->app->singleton(PlatformAnalytics::class, DatabasePlatformAnalytics::class);

        $this->app->singleton(UsageMetrics::class, DatabaseUsageMetrics::class);
        $this->app->bind(PrazzuIdentityLinker::class, ImmutablePrazzuIdentityLinker::class);
        $this->app->bind(ExternalServiceClient::class, LaravelHttpServiceClient::class);
    }
}
