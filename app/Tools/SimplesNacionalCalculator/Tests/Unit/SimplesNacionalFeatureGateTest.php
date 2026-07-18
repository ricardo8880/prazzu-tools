<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Tools\SimplesNacionalCalculator\Application\Access\SimplesNacionalFeatureGate;
use App\Tools\SimplesNacionalCalculator\Application\Features\FeatureCatalog;
use App\Tools\SimplesNacionalCalculator\Application\Features\SimplesNacionalFeature;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

final class SimplesNacionalFeatureGateTest extends TestCase
{
    public function test_free_features_remain_available_when_plus_is_locked(): void
    {
        $gate = $this->gate(new ToolAccessContext);

        self::assertTrue($gate->decide(SimplesNacionalFeature::Calculate)->allowed);
        self::assertFalse($gate->decide(SimplesNacionalFeature::Alerts)->allowed);
    }

    public function test_plus_features_follow_the_central_launch_policy(): void
    {
        $gate = $this->gate(new ToolAccessContext, launchFree: true);

        self::assertTrue($gate->decide(SimplesNacionalFeature::Alerts)->allowed);
        self::assertTrue($gate->decide(SimplesNacionalFeature::MonthlyHistory)->allowed);
    }

    public function test_locked_plus_features_use_the_effective_plan_resolved_by_core(): void
    {
        $gate = $this->gate(new ToolAccessContext(userId: 10, plan: SubscriptionPlan::Premium));
        $user = $this->createStub(Authenticatable::class);

        self::assertTrue($gate->decide(SimplesNacionalFeature::Alerts, $user)->allowed);
    }

    private function gate(ToolAccessContext $context, bool $launchFree = false): SimplesNacionalFeatureGate
    {
        $resolver = new class($context) implements ToolAccessContextResolver
        {
            public function __construct(private readonly ToolAccessContext $context) {}

            public function resolve(?Authenticatable $user): ToolAccessContext
            {
                return $this->context;
            }
        };

        $commercialPolicy = new class($launchFree) implements CommercialAccessPolicy
        {
            public function __construct(private readonly bool $launchFree) {}

            public function grantsPublicCapabilitiesWithoutAuthentication(): bool
            {
                return $this->launchFree;
            }

            public function enforcesUsageLimits(): bool
            {
                return ! $this->launchFree;
            }
        };

        $featureFlags = new class implements FeatureFlagRepository
        {
            public function enabled(string $flag, bool $default = false): bool
            {
                return $default;
            }
        };

        return new SimplesNacionalFeatureGate(new FeatureCatalog, $resolver, $commercialPolicy, $featureFlags);
    }
}
