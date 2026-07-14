<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Tools\SimplesNacionalCalculator\Application\Features\FeatureCatalog;
use App\Tools\SimplesNacionalCalculator\Application\Features\FeatureTier;
use App\Tools\SimplesNacionalCalculator\Application\Features\SimplesNacionalFeature;
use PHPUnit\Framework\TestCase;

final class FeatureCatalogTest extends TestCase
{
    public function test_free_and_plus_features_are_separated(): void
    {
        $catalog = new FeatureCatalog();

        self::assertSame(FeatureTier::Free, $catalog->tierFor(SimplesNacionalFeature::Calculate));
        self::assertSame(FeatureTier::Plus, $catalog->tierFor(SimplesNacionalFeature::MonthlyHistory));
        self::assertCount(4, $catalog->featuresFor(FeatureTier::Free));
        self::assertCount(5, $catalog->featuresFor(FeatureTier::Plus));
    }
}
