<?php

declare(strict_types=1);

namespace App\Tools\ReceiptIssuer\Domain\Services;

use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\ReceiptIssuer\Application\Data\CalculationInput;
use InvalidArgumentException;

final readonly class Calculator implements ToolCalculator
{
    public function __construct(private ReceiptIssuer $issuer = new ReceiptIssuer(new BrazilianMoneyInWords())) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a ferramenta emissor-de-recibos.');
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
            toolSlug: 'emissor-de-recibos',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('number', 'Número do recibo', $receipt->number->value),
                new ToolCalculationSummaryItem('amount', 'Valor recebido', $receipt->amount->formatPtBr()),
                new ToolCalculationSummaryItem('payer', 'Pagador', trim($receipt->payer->name)),
                new ToolCalculationSummaryItem('payee', 'Recebedor', trim($receipt->payee->name)),
            ],
            details: ['receipt' => $receipt->toArray()],
        );
    }
}
