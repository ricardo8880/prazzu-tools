<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Tools\SimplesNacionalCalculator\Application\Access\SimplesNacionalFeatureGate;
use App\Tools\SimplesNacionalCalculator\Application\Features\FeatureCatalog;
use App\Tools\SimplesNacionalCalculator\Application\Features\SimplesNacionalFeature;
use Tests\TestCase;

final class SimplesNacionalFeatureGateTest extends TestCase
{
    public function test_free_features_remain_available_when_plus_is_locked(): void
    {
        config()->set('simples-nacional-access.unlock_plus', false);

        $gate = new SimplesNacionalFeatureGate(new FeatureCatalog());

        self::assertTrue($gate->decide(SimplesNacionalFeature::Calculate)->allowed);
        self::assertFalse($gate->decide(SimplesNacionalFeature::Alerts)->allowed);
    }

    public function test_plus_features_are_temporarily_unlocked_by_configuration(): void
    {
        config()->set('simples-nacional-access.unlock_plus', true);

        $gate = new SimplesNacionalFeatureGate(new FeatureCatalog());

        self::assertTrue($gate->decide(SimplesNacionalFeature::Alerts)->allowed);
        self::assertTrue($gate->decide(SimplesNacionalFeature::MonthlyHistory)->allowed);
    }
}
