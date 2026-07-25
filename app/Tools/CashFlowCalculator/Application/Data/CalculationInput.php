<?php

declare(strict_types=1);

namespace App\Tools\CashFlowCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;

final readonly class CalculationInput implements ToolCalculationInput
{
    public function __construct(
        public Money $openingBalance,
        public Money $salesReceipts,
        public Money $otherInflows,
        public Money $operatingPayments,
        public Money $taxPayments,
        public Money $investments,
        public Money $financingPayments,
        public Money $otherOutflows,
    ) {}

    public function toArray(): array
    {
        return [
            'opening_balance' => $this->openingBalance->minorAmount(),
            'sales_receipts' => $this->salesReceipts->minorAmount(),
            'other_inflows' => $this->otherInflows->minorAmount(),
            'operating_payments' => $this->operatingPayments->minorAmount(),
            'tax_payments' => $this->taxPayments->minorAmount(),
            'investments' => $this->investments->minorAmount(),
            'financing_payments' => $this->financingPayments->minorAmount(),
            'other_outflows' => $this->otherOutflows->minorAmount(),
        ];
    }
}
