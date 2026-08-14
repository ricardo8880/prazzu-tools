<?php

declare(strict_types=1);

namespace App\Tools\ActualProfitCalculator\Tests\Unit;
use App\Core\Tax\Normative\ActualProfitIncomeTaxRule; use App\Tools\ActualProfitCalculator\Application\Data\CalculationInput; use App\Tools\ActualProfitCalculator\Domain\Services\Calculator; use PHPUnit\Framework\TestCase;
final class CalculatorTest extends TestCase { public function test_calculation():void{$r=(new Calculator(new ActualProfitIncomeTaxRule))->calculate(new CalculationInput('100000','0','0','0','0',3));self::assertSame('R$ 28.000,00',$r->summary[3]->value);} }
