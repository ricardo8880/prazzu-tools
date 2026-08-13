<?php

declare(strict_types=1);

namespace App\Tools\ContractGenerator\Tests\Feature;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\DefaultToolAccessGate;
use App\Core\Access\Services\DefaultToolFeatureAccessGate;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Core\Quality\Attributes\CoversPlusFeature;
use App\Tools\ContractGenerator\Tool;
use Illuminate\Contracts\Auth\Authenticatable;
use PHPUnit\Framework\TestCase;

final class PlusFeaturesTest extends TestCase
{
    #[CoversPlusFeature('gerador-de-contratos', 'company_autofill')]
    #[CoversPlusFeature('gerador-de-contratos', 'contract_library')]
    #[CoversPlusFeature('gerador-de-contratos', 'favorites')]
    #[CoversPlusFeature('gerador-de-contratos', 'history')]
    #[CoversPlusFeature('gerador-de-contratos', 'smart_clauses')]
    #[CoversPlusFeature('gerador-de-contratos', 'version_comparison')]
    public function test_contract_plus_features_block_free_and_allow_plus_in_monetized_mode(): void
    {
        $features = ['contract_library', 'smart_clauses', 'favorites', 'company_autofill', 'history', 'version_comparison'];
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
