<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Tests\Unit;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxItemEstimate;
use PHPUnit\Framework\TestCase;

final class TaxItemEstimateTest extends TestCase
{
    public function test_it_accepts_a_non_negative_tax_item(): void
    {
        $item = new TaxItemEstimate(
            code: 'IRPJ',
            label: 'Imposto de Renda da Pessoa Jurídica',
            monthlyAmount: Money::fromDecimal('1200.00'),
            annualAmount: Money::fromDecimal('14400.00'),
            effectiveRate: Percentage::fromString('2.4'),
        );

        self::assertSame('IRPJ', $item->code);
        self::assertSame('2.4', $item->effectiveRate?->toDecimalString());
    }

    public function test_it_rejects_negative_tax_amounts(): void
    {
        $this->expectException(InvalidValue::class);
        $this->expectExceptionMessage('Os valores estimados do tributo não podem ser negativos.');

        new TaxItemEstimate(
            code: 'IRPJ',
            label: 'IRPJ',
            monthlyAmount: Money::fromDecimal('-0.01'),
            annualAmount: Money::zero(),
        );
    }
}
