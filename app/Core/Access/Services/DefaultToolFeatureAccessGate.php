<?php

declare(strict_types=1);

namespace App\Core\Access\Services;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\Access\Contracts\ToolAccessGate;
use App\Core\Access\Contracts\ToolFeatureAccessGate;
use App\Core\Access\Data\AccessDecision;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Core\Tools\Data\ToolManifest;
use App\Core\Tools\Enums\ToolFeatureTier;
use Illuminate\Contracts\Auth\Authenticatable;

final readonly class DefaultToolFeatureAccessGate implements ToolFeatureAccessGate
{
    public function __construct(
        private ToolAccessGate $toolAccessGate,
        private ToolAccessContextResolver $accessContextResolver,
        private CommercialAccessPolicy $commercialPolicy,
        private FeatureFlagRepository $featureFlags,
    ) {}

    public function decide(ToolManifest $manifest, string $featureKey, ?Authenticatable $user = null): AccessDecision
    {
        $feature = $manifest->feature($featureKey);

        if ($feature === null) {
            return AccessDecision::deny('feature.not_declared');
        }

        $context = $this->accessContextResolver->resolve($user);
        $toolDecision = $this->toolAccessGate->decide($manifest, $context);

        if (! $toolDecision->allowed) {
            return $toolDecision;
        }

        if (! $this->featureFlags->enabled("tools.{$manifest->slug}.features.{$feature->key}.enabled", true)) {
            return AccessDecision::deny('feature.disabled');
        }

        if ($feature->tier === ToolFeatureTier::Essential) {
            return AccessDecision::allow('feature.essential');
        }

        if ($this->commercialPolicy->grantsPublicCapabilitiesWithoutAuthentication()) {
            return AccessDecision::allow('feature.launch_free');
        }

        if (! $context->authenticated()) {
            return AccessDecision::deny('feature.authentication_required');
        }

        return $context->plan->grantsPlusFeatures()
            ? AccessDecision::allow('feature.plus_plan')
            : AccessDecision::deny('feature.plus_required');
    }
}
