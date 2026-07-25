<?php

declare(strict_types=1);

namespace App\Tools\WorkIncomeStatementGenerator\Domain\Services;

use App\Core\Documents\Data\GeneratedDocumentNotice;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\WorkIncomeStatementGenerator\Application\Data\CalculationInput;
use DateTimeImmutable;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }

        $startDate = new DateTimeImmutable($input->startDate);
        $issueDate = new DateTimeImmutable($input->issueDate);
        if ($startDate > $issueDate) {
            throw new InvalidArgumentException('O início informado não pode ser posterior à emissão.');
        }

        $text = "{$input->employer} declara, sob sua responsabilidade, que {$input->name}, documento {$input->document}, exerce a atividade de {$input->occupation} desde {$startDate->format('d/m/Y')}, com renda mensal declarada de {$input->monthlyIncome->formatPtBr()}.";
        $notice = new GeneratedDocumentNotice(
            purpose: 'Registrar uma declaração particular de atividade e renda com dados fornecidos pelo declarante.',
            limitations: [
                'Não cria, reconhece nem comprova vínculo empregatício ou relação jurídica.',
                'Não valida identidade, renda, atividade, poderes do declarante, assinatura ou autenticidade.',
                'A aceitação depende da instituição destinatária e dos documentos comprobatórios exigidos.',
            ],
        );

        return new ToolCalculationResult(
            toolSlug: 'declaracao-trabalho-renda',
            schemaVersion: '1.1.0',
            summary: [
                new ToolCalculationSummaryItem('monthly_income', 'Renda mensal declarada', $input->monthlyIncome->formatPtBr()),
                new ToolCalculationSummaryItem('worker', 'Pessoa declarada', $input->name),
                new ToolCalculationSummaryItem('occupation', 'Atividade declarada', $input->occupation),
            ],
            details: [
                'input' => $input->toArray(),
                'document' => [
                    'title' => 'Declaração de Trabalho e Renda',
                    'text' => $text,
                    'location' => "{$input->city}, {$issueDate->format('d/m/Y')}",
                    'signer' => $input->employer,
                    'notice' => $notice->toArray(),
                ],
            ],
            warnings: [
                new ToolCalculationWarning('document.does_not_prove_employment', 'Este documento reproduz uma declaração particular e não comprova vínculo laboral, renda ou autenticidade sem evidências e assinatura do declarante.'),
            ],
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.0.0',
                steps: [
                    new CalculationMemoryStep('period', 'Período declarado', 'data de início ≤ data de emissão', ['start_date' => $input->startDate, 'issue_date' => $input->issueDate], 'valid'),
                    new CalculationMemoryStep('income', 'Renda mensal declarada', 'valor monetário informado pelo declarante', ['monthly_income_minor' => $input->monthlyIncome->minorAmount()], $input->monthlyIncome->minorAmount(), 'Money em centavos, com aritmética inteira'),
                    new CalculationMemoryStep('document', 'Texto documental', 'interpolação literal dos dados revisáveis', ['worker' => $input->name, 'declarant' => $input->employer, 'occupation' => $input->occupation], 'generated'),
                ],
                assumptions: [
                    'A atividade e a renda são declarações do utilizador, sem verificação externa.',
                    'O texto não qualifica juridicamente a relação nem presume vínculo empregatício.',
                    'O documento requer revisão e assinatura do declarante antes do uso.',
                ],
                isEstimate: false,
            ),
        );
    }
}
