<?php

declare(strict_types=1);

namespace App\Tools\IncomeStatementGenerator\Domain\Services;

use App\Core\Documents\Data\GeneratedDocumentNotice;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\IncomeStatementGenerator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }

        $deductions = $input->inss->add($input->irrf)->add($input->otherDeductions);
        if ($deductions->minorAmount() > $input->gross->minorAmount()) {
            throw new InvalidArgumentException('Deduções superiores aos rendimentos.');
        }

        $net = $input->gross->subtract($deductions);
        $notice = new GeneratedDocumentNotice(
            purpose: 'Consolidar valores previamente apurados e declarados pela fonte pagadora.',
            limitations: [
                'Não apura tributos nem substitui informe oficial, escrituração ou obrigação acessória.',
                'Não valida CPF, identidade, poderes da fonte pagadora, assinatura ou autenticidade.',
                'Os valores e o ano-calendário devem ser conferidos pelo declarante antes da assinatura.',
            ],
        );

        return new ToolCalculationResult(
            toolSlug: 'declaracao-rendimentos',
            schemaVersion: '1.1.0',
            summary: [
                new ToolCalculationSummaryItem('gross', 'Rendimentos brutos', $input->gross->formatPtBr()),
                new ToolCalculationSummaryItem('deductions', 'Deduções informadas', $deductions->formatPtBr()),
                new ToolCalculationSummaryItem('net', 'Rendimento líquido', $net->formatPtBr()),
            ],
            details: [
                'input' => $input->toArray(),
                'document' => [
                    'title' => 'Declaração de Rendimentos',
                    'payer' => $input->payer,
                    'beneficiary' => $input->name,
                    'document' => $input->document,
                    'year' => $input->year,
                    'gross' => $input->gross->formatPtBr(),
                    'inss' => $input->inss->formatPtBr(),
                    'irrf' => $input->irrf->formatPtBr(),
                    'other' => $input->otherDeductions->formatPtBr(),
                    'net' => $net->formatPtBr(),
                    'declaration' => "A fonte pagadora {$input->payer} declara, sob sua responsabilidade, que {$input->name}, documento {$input->document}, recebeu no ano-calendário de {$input->year} os valores discriminados neste documento.",
                    'notice' => $notice->toArray(),
                ],
            ],
            warnings: [
                new ToolCalculationWarning('document.requires_review_and_signature', 'Revise todos os dados e obtenha a assinatura do declarante. A plataforma não valida autenticidade nem substitui documentos fiscais oficiais.'),
            ],
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.0.0',
                steps: [
                    new CalculationMemoryStep('deductions', 'Total de deduções informadas', 'INSS + IRRF + outras deduções', ['inss_minor' => $input->inss->minorAmount(), 'irrf_minor' => $input->irrf->minorAmount(), 'other_minor' => $input->otherDeductions->minorAmount()], $deductions->minorAmount(), 'Money em centavos, com aritmética inteira'),
                    new CalculationMemoryStep('net', 'Rendimento líquido informado', 'rendimentos brutos - deduções informadas', ['gross_minor' => $input->gross->minorAmount(), 'deductions_minor' => $deductions->minorAmount()], $net->minorAmount(), 'Money em centavos, com aritmética inteira'),
                ],
                assumptions: [
                    'Todos os valores foram previamente apurados e informados pelo utilizador.',
                    'A ferramenta apenas organiza os dados em documento; não calcula obrigação tributária.',
                    'Não há validação externa de identidade, vínculo, assinatura ou autenticidade.',
                ],
                isEstimate: false,
            ),
        );
    }
}
