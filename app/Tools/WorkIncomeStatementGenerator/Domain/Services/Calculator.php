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
            throw new InvalidArgumentException('Incompatible input.');
        }

        $startDate = new DateTimeImmutable($input->startDate);
        $issueDate = new DateTimeImmutable($input->issueDate);
        if ($startDate > $issueDate) {
            throw new InvalidArgumentException('The employment start date cannot be later than the issue date.');
        }

        $text = "{$input->employer} states that {$input->name}, employee/reference ID {$input->document}, has worked as {$input->occupation} since {$startDate->format('F j, Y')}, with reported monthly income of {$input->monthlyIncome->formatEnUs()}.";
        $notice = new GeneratedDocumentNotice(
            purpose: 'Create a general employment and income verification letter using employer-supplied information.',
            limitations: [
                'This generated letter does not independently verify employment, income, identity, or authority to sign.',
                'Acceptance depends on the recipient and any supporting documents they require.',
                'Use the employer’s official verification process when a lender, landlord, government agency, or other recipient requires one.',
            ],
        );

        return new ToolCalculationResult(
            toolSlug: 'employment-income-letter-generator',
            schemaVersion: '1.1.0',
            summary: [
                new ToolCalculationSummaryItem('monthly_income', 'Reported monthly income', $input->monthlyIncome->formatEnUs()),
                new ToolCalculationSummaryItem('worker', 'Employee / worker', $input->name),
                new ToolCalculationSummaryItem('occupation', 'Job title / occupation', $input->occupation),
            ],
            details: [
                'input' => $input->toArray(),
                'document' => [
                    'title' => 'Employment and Income Verification Letter',
                    'text' => $text,
                    'location' => "{$input->city}, {$issueDate->format('F j, Y')}",
                    'signer' => $input->employer,
                    'notice' => $notice->toArray(),
                ],
            ],
            warnings: [
                new ToolCalculationWarning('document.does_not_prove_employment', 'This generated letter is informational and should be reviewed, signed, and supported by the employer’s records before use.'),
            ],
            calculationMemory: new CalculationMemory(
                schemaVersion: '1.0.0',
                steps: [
                    new CalculationMemoryStep('period', 'Employment period', 'start date ≤ issue date', ['start_date' => $input->startDate, 'issue_date' => $input->issueDate], 'valid'),
                    new CalculationMemoryStep('income', 'Reported monthly income', 'monthly income supplied by the employer', ['monthly_income_minor' => $input->monthlyIncome->minorAmount()], $input->monthlyIncome->minorAmount(), 'Money in cents using integer arithmetic'),
                    new CalculationMemoryStep('document', 'Generated letter', 'formatted employer-supplied data', ['worker' => $input->name, 'declarant' => $input->employer, 'occupation' => $input->occupation], 'generated'),
                ],
                assumptions: [
                    'Employment and income data are supplied by the user and are not independently verified.',
                    'The tool does not determine legal worker classification or employment status.',
                    'The letter should be reviewed and signed by an authorized employer representative before use.',
                ],
                isEstimate: false,
            ),
        );
    }
}
