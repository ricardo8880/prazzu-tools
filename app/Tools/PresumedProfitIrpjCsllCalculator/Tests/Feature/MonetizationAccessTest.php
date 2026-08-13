<?php

declare(strict_types=1);

namespace App\Tools\PresumedProfitIrpjCsllCalculator\Tests\Feature;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\DefaultToolAccessGate;
use App\Core\Access\Services\DefaultToolFeatureAccessGate;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Core\Tools\Data\ToolManifest;
use App\Tools\PresumedProfitIrpjCsllCalculator\Tool;
use Illuminate\Contracts\Auth\Authenticatable;
use PHPUnit\Framework\TestCase;

final class MonetizationAccessTest extends TestCase
{
    #[CoversPlusFeature('calculadora-irpj-csll-lucro-presumido', 'history')]
    public function test_plus_features_block_free_and_allow_plus_in_monetized_mode(): void
    {
        $manifest = (new Tool)->manifest();
        foreach (['periodicity', 'multiple_activities', 'scenario_comparison', 'carry_forward_limit', 'credits', 'export', 'history'] as $feature) {
            $this->assertFeatureAccess($manifest, $feature);
        }
    }

    private function assertFeatureAccess(ToolManifest $manifest, string $feature): void
    {
        $user = $this->createStub(Authenticatable::class);
        $free = $this->gate(SubscriptionPlan::Free)->decide($manifest, $feature, $user);
        $plus = $this->gate(SubscriptionPlan::Plus)->decide($manifest, $feature, $user);

        self::assertFalse($free->allowed, $feature.' should block Free.');
        self::assertSame('feature.plus_required', $free->reason);
        self::assertTrue($plus->allowed, $feature.' should allow Plus.');
        self::assertSame('feature.plus_plan', $plus->reason);
    }

    private function gate(SubscriptionPlan $plan): DefaultToolFeatureAccessGate
    {
        $flags = new class implements FeatureFlagRepository
        {
            public function enabled(string $flag, bool $default = false): bool
            {
                return true;
            }
        };
        $policy = new class implements CommercialAccessPolicy
        {
            public function grantsPublicCapabilitiesWithoutAuthentication(): bool
            {
                return false;
            }
        };
        $resolver = new class($plan) implements ToolAccessContextResolver
        {
            public function __construct(private readonly SubscriptionPlan $plan) {}

            public function resolve(?Authenticatable $user): ToolAccessContext
            {
                return new ToolAccessContext(userId: 1, plan: $this->plan);
            }
        };

        return new DefaultToolFeatureAccessGate(new DefaultToolAccessGate($flags, $policy), $resolver, $policy, $flags);
    }
}
