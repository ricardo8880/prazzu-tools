<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreSimulator\Tests\Feature;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\DefaultToolAccessGate;
use App\Core\Access\Services\DefaultToolFeatureAccessGate;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Tools\ProLaboreSimulator\Tool;
use Illuminate\Contracts\Auth\Authenticatable;
use PHPUnit\Framework\TestCase;

final class PlusFeaturesTest extends TestCase
{
    #[CoversPlusFeature('simulador-pro-labore-ideal', 'scenarios')]
    public function test_plus_features_block_free_and_allow_plus_in_monetized_mode(): void
    {
        $features = ['scenarios'];
        $manifest = (new Tool)->manifest();
        $user = $this->createStub(Authenticatable::class);
        foreach ($features as $feature) {
            $free = $this->gate(SubscriptionPlan::Free)->decide($manifest, $feature, $user);
            $plus = $this->gate(SubscriptionPlan::Plus)->decide($manifest, $feature, $user);
            self::assertFalse($free->allowed, $feature);
            self::assertSame('feature.plus_required', $free->reason, $feature);
            self::assertTrue($plus->allowed, $feature);
            self::assertSame('feature.plus_plan', $plus->reason, $feature);
        }
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
