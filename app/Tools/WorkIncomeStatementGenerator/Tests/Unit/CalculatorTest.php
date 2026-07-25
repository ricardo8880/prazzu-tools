<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator\Tests\Unit;

use App\Core\Money\Money;
use App\Tools\WorkIncomeStatementGenerator\Application\Data\CalculationInput;
use App\Tools\WorkIncomeStatementGenerator\Domain\Services\Calculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CalculatorTest extends TestCase
{
    public function test_it_generates_personalized_text(): void
    {
        $result = (new Calculator)->calculate(new CalculationInput('Ana', '000', 'Empresa', 'Analista', '2020-01-01', Money::fromDecimal('5000'), 'São Paulo', '2026-07-25'));

        self::assertStringContainsString('Ana', $result->details['document']['text']);
        self::assertSame('R$ 5.000,00', $result->summary[0]->value);
        self::assertStringContainsString('declara, sob sua responsabilidade', $result->details['document']['text']);
        self::assertFalse($result->details['document']['notice']['authenticity_validated']);
        self::assertNotNull($result->calculationMemory);
        self::assertCount(3, $result->calculationMemory->steps);
        self::assertCount(1, $result->warnings);
    }

    public function test_it_rejects_start_after_issue_date(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Calculator)->calculate(new CalculationInput('Ana', '000', 'Empresa', 'Analista', '2026-08-01', Money::fromDecimal('5000'), 'São Paulo', '2026-07-25'));
    }
}
