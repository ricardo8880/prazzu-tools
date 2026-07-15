<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Tools\AccountingFeesCalculator\Domain\Calculators\AccountingFeesCalculator;
use App\Tools\AccountingFeesCalculator\Domain\Data\AccountingFeesResult;
use App\Tools\AccountingFeesCalculator\Domain\Enums\BusinessSegment;
use App\Tools\AccountingFeesCalculator\Domain\Enums\OperationalComplexity;
use App\Tools\AccountingFeesCalculator\Domain\Enums\TaxRegime;

final readonly class CalculateAccountingFees
{
    public function __construct(private AccountingFeesCalculator $calculator) {}

    /** @param array<string, mixed> $input */
    public function execute(array $input): AccountingFeesResult
    {
        return $this->calculator->calculate(
            monthlyRevenue: Money::fromDecimal((string) $input['monthly_revenue']),
            employees: (int) $input['employees'],
            partners: (int) $input['partners'],
            monthlyInvoices: (int) $input['monthly_invoices'],
            monthlyBankTransactions: (int) $input['monthly_bank_transactions'],
            taxRegime: TaxRegime::from((string) $input['tax_regime']),
            segment: BusinessSegment::from((string) $input['business_segment']),
            complexity: OperationalComplexity::from((string) $input['complexity']),
        );
    }
}
