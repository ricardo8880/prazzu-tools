<?php

declare(strict_types=1);

namespace App\Tools\ProfitDistributionCalculator\Tests\Unit;

use App\Tools\ProfitDistributionCalculator\Application\Data\CalculationInput;
use App\Tools\ProfitDistributionCalculator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_calculates_distribution_without_pro_labore_dependency(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('Sócio', '100', '50000.00', intendedDistribution: '30000.00'));

        self::assertSame('distribuicao-de-lucros', $result->toolSlug);
        self::assertSame(3000000, $result->details['distributed_amount_minor']);
        self::assertSame(2000000, $result->details['undistributed_balance_minor']);
    }
}
