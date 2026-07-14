<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Access;

use App\Tools\SimplesNacionalCalculator\Application\Features\FeatureCatalog;
use App\Tools\SimplesNacionalCalculator\Application\Features\FeatureTier;
use App\Tools\SimplesNacionalCalculator\Application\Features\SimplesNacionalFeature;
use Illuminate\Contracts\Auth\Authenticatable;

final readonly class SimplesNacionalFeatureGate
{
    public function __construct(private FeatureCatalog $catalog) {}

    public function decide(SimplesNacionalFeature $feature, ?Authenticatable $user = null): FeatureAccessDecision
    {
        if (! config('simples-nacional-access.enabled', true)) {
            return FeatureAccessDecision::deny('feature.disabled');
        }

        if ($this->catalog->tierFor($feature) === FeatureTier::Free) {
            return FeatureAccessDecision::allow('feature.free');
        }

        if (config('simples-nacional-access.unlock_plus', true)) {
            return FeatureAccessDecision::allow('feature.plus_temporarily_unlocked');
        }

        if ($user === null) {
            return FeatureAccessDecision::deny('feature.authentication_required');
        }

        $plan = data_get($user, (string) config('simples-nacional-access.user_plan_attribute', 'plan'));
        $plusPlans = config('simples-nacional-access.plus_plans', ['premium', 'plus']);

        return in_array((string) $plan, $plusPlans, true)
            ? FeatureAccessDecision::allow('feature.plus_plan')
            : FeatureAccessDecision::deny('feature.plus_required');
    }
}
