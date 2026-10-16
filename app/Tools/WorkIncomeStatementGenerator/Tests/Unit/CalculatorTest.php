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
        $result = (new Calculator)->calculate(new CalculationInput('Alex', '000', 'Company', 'Analyst', '2020-01-01', Money::fromDecimal('5000'), 'New York', '2026-07-25'));

        self::assertStringContainsString('Alex', $result->details['document']['text']);
        self::assertSame('$5,000.00', $result->summary[0]->value);
        self::assertStringContainsString('declara, sob sua responsabilidade', $result->details['document']['text']);
        self::assertFalse($result->details['document']['notice']['authenticity_validated']);
        self::assertNotNull($result->calculationMemory);
        self::assertCount(3, $result->calculationMemory->steps);
        self::assertCount(1, $result->warnings);
    }

    public function test_it_rejects_start_after_issue_date(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Calculator)->calculate(new CalculationInput('Alex', '000', 'Company', 'Analyst', '2026-08-01', Money::fromDecimal('5000'), 'New York', '2026-07-25'));
    }
}
