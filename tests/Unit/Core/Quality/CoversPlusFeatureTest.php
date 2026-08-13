<?php

declare(strict_types=1);

namespace Tests\Unit\Core\Quality;

use App\Core\Quality\Attributes\CoversPlusFeature;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CoversPlusFeatureTest extends TestCase
{
    public function test_it_builds_a_stable_contract_key(): void
    {
        $attribute = new CoversPlusFeature('calculadora-turnover', 'segmented_analysis');

        self::assertSame('calculadora-turnover:segmented_analysis', $attribute->contractKey());
    }

    public function test_it_rejects_an_invalid_tool_slug(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CoversPlusFeature('Calculadora Turnover', 'segmented_analysis');
    }

    public function test_it_rejects_an_invalid_feature_key(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new CoversPlusFeature('calculadora-turnover', 'segmented-analysis');
    }
}
