<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Access;

use App\Core\Access\Contracts\CommercialAccessPolicy;
use App\Core\Access\Contracts\ToolAccessContextResolver;
use App\Core\FeatureFlags\Contracts\FeatureFlagRepository;
use App\Tools\SimplesNacionalCalculator\Application\Features\FeatureCatalog;
use App\Tools\SimplesNacionalCalculator\Application\Features\FeatureTier;
use App\Tools\SimplesNacionalCalculator\Application\Features\SimplesNacionalFeature;
use Illuminate\Contracts\Auth\Authenticatable;

final readonly class SimplesNacionalFeatureGate
{
    public function __construct(
        private FeatureCatalog $catalog,
        private ToolAccessContextResolver $accessContextResolver,
        private CommercialAccessPolicy $commercialPolicy,
        private FeatureFlagRepository $featureFlags,
    ) {}

    public function decide(SimplesNacionalFeature $feature, ?Authenticatable $user = null): FeatureAccessDecision
    {
        if (! $this->featureFlags->enabled('tools.calculadora-simples-nacional.enabled', true)
            || ! $this->featureFlags->enabled("tools.calculadora-simples-nacional.features.{$feature->value}.enabled", true)) {
            return FeatureAccessDecision::deny('feature.disabled');
        }

        if ($this->catalog->tierFor($feature) === FeatureTier::Free) {
            return FeatureAccessDecision::allow('feature.free');
        }

        if ($this->commercialPolicy->grantsPublicCapabilitiesWithoutAuthentication()) {
            return FeatureAccessDecision::allow('feature.launch_free');
        }

        $context = $this->accessContextResolver->resolve($user);

        if (! $context->authenticated()) {
            return FeatureAccessDecision::deny('feature.authentication_required');
        }

        return $context->plan->grantsPremiumTools()
            ? FeatureAccessDecision::allow('feature.plus_plan')
            : FeatureAccessDecision::deny('feature.plus_required');
    }
}
