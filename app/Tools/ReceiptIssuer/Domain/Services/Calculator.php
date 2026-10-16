<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\Services;

use App\Core\Money\AmericanMoneyInWords;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\ReceiptIssuer\Application\Data\CalculationInput;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
    public function __construct(private ReceiptIssuer $issuer = new ReceiptIssuer(new AmericanMoneyInWords)) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Incompatible receipt-generator input.');
        }

        $receipt = $this->issuer->issue(
            identifier: $input->identifier,
            number: $input->number,
            payer: $input->payer,
            payee: $input->payee,
            amount: $input->amount,
            description: $input->description,
            issuedAt: $input->issuedAt,
            city: $input->city,
        );

        return new ToolCalculationResult(
            toolSlug: 'receipt-generator',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('number', 'Receipt number', $receipt->number->value),
                new ToolCalculationSummaryItem('amount', 'Amount received', $receipt->amount->formatEnUs()),
                new ToolCalculationSummaryItem('payer', 'Payer', trim($receipt->payer->name)),
                new ToolCalculationSummaryItem('payee', 'Payee', trim($receipt->payee->name)),
            ],
            details: ['receipt' => $receipt->toArray()],
        );
    }
}
