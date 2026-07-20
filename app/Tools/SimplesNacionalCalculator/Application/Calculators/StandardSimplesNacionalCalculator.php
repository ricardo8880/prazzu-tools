<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Calculators;

use App\Core\ToolIntegration\Data\IntegrationPayload;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Core\Tools\Calculation\Data\ToolCalculationAction;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Tools\SimplesNacionalCalculator\Application\Data\SimplesNacionalCalculationInput;
use App\Tools\SimplesNacionalCalculator\Domain\Calculators\SimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Tool;
use InvalidArgumentException;

final readonly class StandardSimplesNacionalCalculator implements ToolCalculator
{
    public const RESULT_SCHEMA_VERSION = '1.0.0';

    public function __construct(private SimplesNacionalCalculator $calculator) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof SimplesNacionalCalculationInput) {
            throw new InvalidArgumentException('A Calculadora de Simples Nacional recebeu uma entrada incompatível.');
        }

        $result = $this->calculator->calculate(
            annex: $input->annex,
            rbt12: $input->rbt12,
            monthlyRevenue: $input->monthlyRevenue,
        );

        return new ToolCalculationResult(
            toolSlug: Tool::SLUG,
            schemaVersion: self::RESULT_SCHEMA_VERSION,
            summary: [
                new ToolCalculationSummaryItem('annex', 'Anexo', $result->annex->label()),
                new ToolCalculationSummaryItem('effective_rate', 'Alíquota efetiva', $result->effectiveRate->toDecimalString().'%'),
                new ToolCalculationSummaryItem('estimated_das', 'DAS estimado', $result->estimatedDas->formatPtBr()),
            ],
            details: $result->toArray(),
            nextActions: [
                new ToolCalculationAction(
                    key: 'review-tax-scenario',
                    label: 'Revisar cenário tributário',
                    type: 'review',
                    context: ['annex' => $result->annex->value],
                ),
            ],
            integrationPayload: new IntegrationPayload(
                sourceTool: Tool::SLUG,
                contractName: 'company-tax-snapshot',
                contractVersion: 1,
                data: [
                    'rbt12' => $this->moneyToDecimal($result->rbt12->minorAmount()),
                    'monthly_revenue' => $this->moneyToDecimal($result->monthlyRevenue->minorAmount()),
                    'annex' => $result->annex->value,
                    'effective_rate' => $result->effectiveRate->toDecimalString(),
                    'estimated_das' => $this->moneyToDecimal($result->estimatedDas->minorAmount()),
                ],
            ),
        );
    }

    private function moneyToDecimal(int $minorAmount): string
    {
        $sign = $minorAmount < 0 ? '-' : '';
        $absolute = abs($minorAmount);

        return $sign.intdiv($absolute, 100).'.'.str_pad((string) ($absolute % 100), 2, '0', STR_PAD_LEFT);
    }
}
