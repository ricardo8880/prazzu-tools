<?php

declare(strict_types=1);

namespace Tests\Architecture;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\DefaultToolAccessGate;
use App\Core\Access\Services\DefaultToolFeatureAccessGate;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Core\Tools\ToolRegistry;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

final class PlusFeatureAccessContractTest extends TestCase
{
    public function test_every_declared_plus_feature_is_blocked_for_free_and_allowed_for_plus_in_monetized_mode(): void
    {
        $manifests = app(ToolRegistry::class)->manifests(false);
        $user = $this->createStub(Authenticatable::class);
        $checked = 0;
        $toolsWithPlus = 0;
        $contractKeys = [];

        foreach ($manifests as $manifest) {
            $toolChecked = 0;

            foreach ($manifest->features as $feature) {
                if ($feature->tier->value !== 'plus') {
                    continue;
                }

                $contractKey = $manifest->slug.':'.$feature->key;
                $this->assertArrayNotHasKey($contractKey, $contractKeys, "A feature Plus [{$contractKey}] deve ser única no catálogo.");
                $contractKeys[$contractKey] = true;

                $freeDecision = $this->gate(new ToolAccessContext(userId: 10, plan: SubscriptionPlan::Free))
                    ->decide($manifest, $feature->key, $user);
                $plusDecision = $this->gate(new ToolAccessContext(userId: 11, plan: SubscriptionPlan::Plus))
                    ->decide($manifest, $feature->key, $user);

                $this->assertFalse($freeDecision->allowed, "{$contractKey} deve bloquear Free.");
                $this->assertSame('feature.plus_required', $freeDecision->reason, $contractKey);
                $this->assertTrue($plusDecision->allowed, "{$contractKey} deve permitir Plus.");
                $this->assertSame('feature.plus_plan', $plusDecision->reason, $contractKey);
                $checked++;
                $toolChecked++;
            }

            if ($toolChecked > 0) {
                $toolsWithPlus++;
            }
        }

        $this->assertCount((int) config('plus_feature_governance.catalog_tool_count'), $manifests, 'A matriz Plus deve percorrer todo o catálogo oficial atual.');
        $this->assertSame((int) config('plus_feature_governance.catalog_tool_count'), $toolsWithPlus, 'Cada ferramenta oficial atual deve participar da matriz Plus.');
        $this->assertSame((int) config('plus_feature_governance.declared_plus_feature_count'), $checked, 'A matriz Plus deve cobrir todas as features declaradas, sem omissões silenciosas.');
    }

    private function gate(ToolAccessContext $context): DefaultToolFeatureAccessGate
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
        $resolver = new class($context) implements ToolAccessContextResolver
        {
            public function __construct(private readonly ToolAccessContext $context) {}

            public function resolve(?Authenticatable $user): ToolAccessContext
            {
                return $this->context;
            }
        };

        return new DefaultToolFeatureAccessGate(
            new DefaultToolAccessGate($flags, $policy),
            $resolver,
            $policy,
            $flags,
        );
    }
}
