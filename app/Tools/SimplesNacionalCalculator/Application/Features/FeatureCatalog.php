<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Features;

final class FeatureCatalog
{
    public function tierFor(SimplesNacionalFeature $feature): FeatureTier
    {
        return match ($feature) {
            SimplesNacionalFeature::Calculate,
            SimplesNacionalFeature::FactorR,
            SimplesNacionalFeature::EffectiveRate,
            SimplesNacionalFeature::EstimatedDas => FeatureTier::Free,
            SimplesNacionalFeature::CompareScenarios,
            SimplesNacionalFeature::CompareAnnexes,
            SimplesNacionalFeature::MonthlyHistory,
            SimplesNacionalFeature::AnnualProjection,
            SimplesNacionalFeature::Alerts => FeatureTier::Plus,
        };
    }

    /** @return list<SimplesNacionalFeature> */
    public function featuresFor(FeatureTier $tier): array
    {
        return array_values(array_filter(
            SimplesNacionalFeature::cases(),
            fn (SimplesNacionalFeature $feature): bool => $this->tierFor($feature) === $tier,
        ));
    }
}
