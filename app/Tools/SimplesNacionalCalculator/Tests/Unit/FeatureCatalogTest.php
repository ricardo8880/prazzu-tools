<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Tests\Unit;

use App\Core\Tools\Enums\ToolFeatureTier;
use App\Tools\SimplesNacionalCalculator\Tool;
use PHPUnit\Framework\TestCase;

final class FeatureCatalogTest extends TestCase
{
    public function test_essential_and_plus_features_are_separated_in_the_manifest(): void
    {
        $manifest = (new Tool)->manifest();

        self::assertSame(ToolFeatureTier::Essential, $manifest->feature('calculate')?->tier);
        self::assertSame(ToolFeatureTier::Plus, $manifest->feature('monthly_history')?->tier);
        self::assertCount(4, $manifest->featuresFor(ToolFeatureTier::Essential));
        self::assertCount(5, $manifest->featuresFor(ToolFeatureTier::Plus));
        self::assertSame('1.2.0', $manifest->version);
        self::assertTrue($manifest->supportsHistory);
        self::assertTrue($manifest->storesSensitiveData);
        self::assertSame(['rbt12', 'monthly_revenue'], (new Tool)->historyPolicy()->sensitiveFields);
    }
}
