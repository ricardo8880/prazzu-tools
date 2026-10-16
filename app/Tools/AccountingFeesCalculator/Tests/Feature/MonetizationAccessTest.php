<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Tests\Feature;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\DefaultToolAccessGate;
use App\Core\Access\Services\DefaultToolFeatureAccessGate;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Tools\AccountingFeesCalculator\Tool;
use Illuminate\Contracts\Auth\Authenticatable;
use PHPUnit\Framework\TestCase;

final class MonetizationAccessTest extends TestCase
{
    #[CoversPlusFeature('accounting-fee-calculator', 'history')]
    #[CoversPlusFeature('accounting-fee-calculator', 'history_export')]
    public function test_history_features_require_plus_in_monetized_mode(): void
    {
        $manifest = (new Tool)->manifest();
        foreach (['history', 'history_export'] as $feature) {
            $user = $this->createStub(Authenticatable::class);
            self::assertFalse($this->gate(SubscriptionPlan::Free)->decide($manifest, $feature, $user)->allowed);
            self::assertTrue($this->gate(SubscriptionPlan::Plus)->decide($manifest, $feature, $user)->allowed);
        }
    }

    private function gate(SubscriptionPlan $plan): DefaultToolFeatureAccessGate
    {
        $flags = new class implements FeatureFlagRepository { public function enabled(string $flag, bool $default = false): bool { return true; } };
        $policy = new class implements CommercialAccessPolicy { public function grantsPublicCapabilitiesWithoutAuthentication(): bool { return false; } };
        $resolver = new class($plan) implements ToolAccessContextResolver {
            public function __construct(private readonly SubscriptionPlan $plan) {}
            public function resolve(?Authenticatable $user): ToolAccessContext { return new ToolAccessContext(userId: 1, plan: $this->plan); }
        };
        return new DefaultToolFeatureAccessGate(new DefaultToolAccessGate($flags, $policy), $resolver, $policy, $flags);
    }
}
