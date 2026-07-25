<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\WorkIncomeStatementGenerator\Application\Data\CalculationInput;
use App\Tools\WorkIncomeStatementGenerator\Domain\Services\Calculator;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_generates_personalized_text(): void
    {
        $r = (new Calculator)->calculate(new CalculationInput('Ana', '000', 'Empresa', 'Analista', '2020-01-01', Money::fromDecimal('5000'), 'São Paulo', '2026-07-25'));
        self::assertStringContainsString('Ana', $r->details['document']['text']);
        self::assertSame('R$ 5.000,00', $r->summary[0]->value);
    }
}
