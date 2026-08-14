<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Tests\Unit; use App\Tools\TaxReformSimulator\Application\Data\CalculationInput; use App\Tools\TaxReformSimulator\Domain\Rules\ConsumptionTaxTransitionRule; use App\Tools\TaxReformSimulator\Domain\Services\Calculator; use PHPUnit\Framework\TestCase; final class CalculatorTest extends TestCase { public function test_2026_rates():void{$r=(new Calculator(new ConsumptionTaxTransitionRule))->calculate(new CalculationInput('100000','9.25','18','9','18','0',2026));self::assertSame('0.9',$r->details['cbs_rate']);self::assertSame('0.1',$r->details['ibs_rate']);} }
