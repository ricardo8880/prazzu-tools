<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\WorkingCapitalCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível com a calculadora de capital de giro.');
        }

        $operatingAssets = $input->receivables->add($input->inventory)->add($input->otherCurrentAssets);
        $operatingLiabilities = $input->suppliers->add($input->otherOperatingLiabilities);
        $need = $operatingAssets->subtract($operatingLiabilities);
        $currentAssets = $input->cash->add($operatingAssets);
        $currentLiabilities = $operatingLiabilities->add($input->loans)->add($input->otherCurrentLiabilities);
        $netWorkingCapital = $currentAssets->subtract($currentLiabilities);
        $requiredCapital = Money::fromMinor(max(0, $need->minorAmount()));
        $fundingGap = Money::fromMinor(max(0, $requiredCapital->minorAmount() - $netWorkingCapital->minorAmount()));

        return new ToolCalculationResult(
            toolSlug: 'capital-de-giro',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('required_capital', 'Capital de giro necessário', $requiredCapital->formatPtBr(), 'Necessidade operacional positiva que precisa ser financiada.'),
                new ToolCalculationSummaryItem('operating_need', 'Necessidade de capital de giro (NCG)', $need->formatPtBr(), 'Ativos operacionais menos passivos operacionais.'),
                new ToolCalculationSummaryItem('net_working_capital', 'Capital circulante líquido (CCL)', $netWorkingCapital->formatPtBr(), 'Ativo circulante menos passivo circulante.'),
                new ToolCalculationSummaryItem('funding_gap', 'Necessidade adicional de recursos', $fundingGap->formatPtBr(), 'Necessidade não coberta pelo capital circulante líquido.'),
            ],
            details: [
                'input' => $input->toArray(),
                'memory' => [
                    'Ativos operacionais = contas a receber + estoques + outros ativos' => $operatingAssets->formatPtBr(),
                    'Passivos operacionais = fornecedores + outras obrigações operacionais' => $operatingLiabilities->formatPtBr(),
                    'NCG = ativos operacionais - passivos operacionais' => $need->formatPtBr(),
                    'Ativo circulante = caixa + ativos operacionais' => $currentAssets->formatPtBr(),
                    'Passivo circulante = passivos operacionais + empréstimos + outras obrigações' => $currentLiabilities->formatPtBr(),
                    'CCL = ativo circulante - passivo circulante' => $netWorkingCapital->formatPtBr(),
                ],
            ],
        );
    }
}
