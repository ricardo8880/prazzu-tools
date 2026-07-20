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
use App\Core\ToolIntegration\Contracts\ToolIntegrationCatalog;
use App\Core\ToolIntegration\Contracts\ToolResultPublisher;
use App\Core\ToolIntegration\Contracts\ToolResultResolver;
use App\Core\ToolIntegration\Contracts\ToolResultStore;
use App\Core\ToolIntegration\Services\DefaultToolResultExchange;
use App\Core\ToolIntegration\Services\InMemoryToolIntegrationCatalog;
use App\Core\ToolIntegration\Services\RequestToolResultStore;
use App\Core\ToolIntegration\Services\StandardIntegrationContracts;
use App\Core\Taxation\Contracts\TaxEstimateProviderRegistry;
use App\Core\Taxation\Services\InMemoryTaxEstimateProviderRegistry;
use App\Core\Tools\Infrastructure\Contracts\SensitiveToolPayloadProtector;
use App\Core\Tools\Infrastructure\Contracts\ToolResultCompatibility;
use App\Core\Tools\Infrastructure\Contracts\ToolResultExporter;
use App\Core\Tools\Infrastructure\Contracts\ToolResultSharingGuard;
use App\Core\Tools\Infrastructure\Services\ManifestSensitiveToolPayloadProtector;
use App\Core\Tools\Infrastructure\Services\ManifestToolResultCompatibility;
use App\Core\Tools\Infrastructure\Services\ManifestToolResultExporter;
use App\Core\Tools\Infrastructure\Services\ManifestToolResultSharingGuard;
use App\Core\Usage\Contracts\UsageMetrics;
use App\Core\Usage\Services\DatabaseUsageMetrics;
use Illuminate\Support\ServiceProvider;

final class CoreInfrastructureServiceProvider extends ServiceProvider
{
    public function boot(StandardIntegrationContracts $contracts): void
    {
        $contracts->register();
    }

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
        $this->app->singleton(ToolResultCompatibility::class, ManifestToolResultCompatibility::class);
        $this->app->singleton(ToolResultExporter::class, ManifestToolResultExporter::class);
        $this->app->singleton(ToolResultSharingGuard::class, ManifestToolResultSharingGuard::class);
        $this->app->singleton(SensitiveToolPayloadProtector::class, ManifestSensitiveToolPayloadProtector::class);
        $this->app->bind(PrazzuIdentityLinker::class, ImmutablePrazzuIdentityLinker::class);
        $this->app->bind(ExternalServiceClient::class, LaravelHttpServiceClient::class);
        $this->app->singleton(ToolIntegrationCatalog::class, InMemoryToolIntegrationCatalog::class);
        $this->app->singleton(TaxEstimateProviderRegistry::class, InMemoryTaxEstimateProviderRegistry::class);
        $this->app->scoped(ToolResultStore::class, RequestToolResultStore::class);
        $this->app->scoped(DefaultToolResultExchange::class);
        $this->app->scoped(ToolResultPublisher::class, static fn ($app): ToolResultPublisher => $app->make(DefaultToolResultExchange::class));
        $this->app->scoped(ToolResultResolver::class, static fn ($app): ToolResultResolver => $app->make(DefaultToolResultExchange::class));
    }
}
