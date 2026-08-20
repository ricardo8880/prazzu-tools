<?php

declare(strict_types=1);

namespace App\Tools\WorkingCapitalCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
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
        $fundingSurplus = Money::fromMinor(max(0, $netWorkingCapital->minorAmount() - $requiredCapital->minorAmount()));

        $warnings = [];
        if ($fundingGap->minorAmount() > 0) {
            $warnings[] = new ToolCalculationWarning(
                code: 'funding_gap',
                message: 'O capital circulante líquido informado não cobre integralmente a necessidade operacional estimada. A diferença exibida representa o déficit de financiamento nesta fotografia.',
                level: ToolCalculationWarningLevel::Warning,
                title: 'Há necessidade adicional de recursos',
            );
        } elseif ($fundingSurplus->minorAmount() > 0) {
            $warnings[] = new ToolCalculationWarning(
                code: 'funding_surplus',
                message: 'O capital circulante líquido supera a necessidade operacional estimada. A folga não substitui análise de liquidez, sazonalidade ou prazos médios.',
                level: ToolCalculationWarningLevel::Info,
                title: 'Há folga de capital circulante',
            );
        }

        return new ToolCalculationResult(
            toolSlug: 'capital-de-giro',
            schemaVersion: '1.2.0',
            summary: [
                new ToolCalculationSummaryItem('required_capital', 'Capital de giro necessário', $requiredCapital->formatPtBr(), 'Necessidade operacional positiva que precisa ser financiada.'),
                new ToolCalculationSummaryItem('operating_need', 'Necessidade de capital de giro (NCG)', $need->formatPtBr(), 'Ativos operacionais menos passivos operacionais.'),
                new ToolCalculationSummaryItem('net_working_capital', 'Capital circulante líquido (CCL)', $netWorkingCapital->formatPtBr(), 'Ativo circulante menos passivo circulante.'),
                new ToolCalculationSummaryItem('funding_gap', 'Necessidade adicional de recursos', $fundingGap->formatPtBr(), 'Necessidade operacional ainda não coberta pelo CCL.'),
                new ToolCalculationSummaryItem('funding_surplus', 'Folga de capital circulante', $fundingSurplus->formatPtBr(), 'Excesso de CCL sobre a necessidade operacional estimada.'),
            ],
            details: ['input' => $input->toArray()],
            warnings: $warnings,
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.2.0',
                steps: [
                    new CalculationMemoryStep('operating_assets', 'Ativos operacionais', 'contas a receber + estoques + outros ativos circulantes operacionais', ['receivables' => $input->receivables->minorAmount(), 'inventory' => $input->inventory->minorAmount(), 'other_current_assets' => $input->otherCurrentAssets->minorAmount()], $operatingAssets->minorAmount(), 'Soma em centavos, sem ponto flutuante.'),
                    new CalculationMemoryStep('operating_liabilities', 'Passivos operacionais', 'fornecedores + outras obrigações operacionais', ['suppliers' => $input->suppliers->minorAmount(), 'other_operating_liabilities' => $input->otherOperatingLiabilities->minorAmount()], $operatingLiabilities->minorAmount(), 'Soma em centavos, sem ponto flutuante.'),
                    new CalculationMemoryStep('operating_need', 'Necessidade de capital de giro (NCG)', 'ativos operacionais − passivos operacionais', ['operating_assets' => $operatingAssets->minorAmount(), 'operating_liabilities' => $operatingLiabilities->minorAmount()], $need->minorAmount()),
                    new CalculationMemoryStep('current_assets', 'Ativo circulante', 'caixa + ativos operacionais', ['cash' => $input->cash->minorAmount(), 'operating_assets' => $operatingAssets->minorAmount()], $currentAssets->minorAmount()),
                    new CalculationMemoryStep('current_liabilities', 'Passivo circulante', 'passivos operacionais + empréstimos + outras obrigações circulantes', ['operating_liabilities' => $operatingLiabilities->minorAmount(), 'loans' => $input->loans->minorAmount(), 'other_current_liabilities' => $input->otherCurrentLiabilities->minorAmount()], $currentLiabilities->minorAmount()),
                    new CalculationMemoryStep('net_working_capital', 'Capital circulante líquido (CCL)', 'ativo circulante − passivo circulante', ['current_assets' => $currentAssets->minorAmount(), 'current_liabilities' => $currentLiabilities->minorAmount()], $netWorkingCapital->minorAmount()),
                    new CalculationMemoryStep('required_capital', 'Capital de giro necessário', 'máximo(0, NCG)', ['operating_need' => $need->minorAmount()], $requiredCapital->minorAmount()),
                    new CalculationMemoryStep('funding_gap', 'Necessidade adicional de recursos', 'máximo(0, capital necessário − CCL)', ['required_capital' => $requiredCapital->minorAmount(), 'net_working_capital' => $netWorkingCapital->minorAmount()], $fundingGap->minorAmount()),
                    new CalculationMemoryStep('funding_surplus', 'Folga de capital circulante', 'máximo(0, CCL − capital necessário)', ['required_capital' => $requiredCapital->minorAmount(), 'net_working_capital' => $netWorkingCapital->minorAmount()], $fundingSurplus->minorAmount()),
                ],
                assumptions: [
                    'Todos os saldos devem representar a mesma data-base e o mesmo perímetro contabilístico.',
                    'A classificação entre itens operacionais e financeiros foi definida pelo utilizador e não é reclassificada automaticamente.',
                    'O resultado é uma fotografia financeira; sazonalidade, prazos médios e eventos posteriores exigem cenários adicionais.',
                ],
                isEstimate: true,
            ),
        );
    }
}
