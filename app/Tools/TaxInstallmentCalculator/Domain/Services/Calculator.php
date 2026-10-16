<?php

declare(strict_types=1);

namespace App\Tools\TaxInstallmentCalculator\Domain\Services;

use App\Core\Math\IntegerRounding;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Calculation\Data\ToolCalculationWarning;
use App\Core\Tools\Calculation\Enums\ToolCalculationWarningLevel;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\TaxInstallmentCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Incompatible input for the IRS payment-plan estimator.');
        }
        if ($input->scenarios === []) {
            throw new InvalidArgumentException('Enter at least one scenario.');
        }

        $scenarios = [];
        foreach ($input->scenarios as $index => $scenario) {
            $scenarios[] = $this->calculateScenario($scenario, $index);
        }

        $main = $scenarios[0];

        return new ToolCalculationResult(
            toolSlug: 'irs-payment-plan-calculator',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('debt', 'Tax balance', Money::fromMinor($main['debt_minor'])->formatEnUs()),
                new ToolCalculationSummaryItem('average_installment', 'Average estimated monthly payment', Money::fromMinor($main['average_installment_minor'])->formatEnUs()),
                new ToolCalculationSummaryItem('charges', 'Estimated interest/penalty charges', Money::fromMinor($main['total_charges_minor'])->formatEnUs()),
                new ToolCalculationSummaryItem('final_cost', 'Estimated total paid', Money::fromMinor($main['final_cost_minor'])->formatEnUs()),
            ],
            details: [
                'scenarios' => $scenarios,
                'comparison' => array_map(static fn (array $scenario): array => [
                    'name' => $scenario['name'],
                    'entry_minor' => $scenario['entry_minor'],
                    'installments' => $scenario['installments'],
                    'monthly_charge' => $scenario['monthly_charge'],
                    'first_installment_minor' => $scenario['first_installment_minor'],
                    'average_installment_minor' => $scenario['average_installment_minor'],
                    'final_cost_minor' => $scenario['final_cost_minor'],
                    'total_charges_minor' => $scenario['total_charges_minor'],
                ], $scenarios),
            ],
            warnings: [
                new ToolCalculationWarning(
                    'approximation',
                    'Planning estimate using equal principal reduction plus the user-supplied monthly combined interest/penalty rate on the outstanding balance. Actual IRS interest and penalties can change and do not follow this simplified model exactly.',
                    ToolCalculationWarningLevel::Info,
                ),
                new ToolCalculationWarning(
                    'normative_scope',
                    'For individuals, IRS online-plan eligibility generally differs by balance and term; short-term plans may cover balances under $100,000 for up to 180 days, while long-term online plans generally require $50,000 or less in combined tax, penalties, and interest. Confirm current IRS eligibility, setup fees, required filings, and payment terms before applying.',
                    ToolCalculationWarningLevel::Info,
                ),
            ],
            calculationMemory: new CalculationMemory('1.0.0', [
                new CalculationMemoryStep(
                    'financed_balance',
                    'Balance to pay over time',
                    'tax balance - upfront payment',
                    ['debt_minor' => $main['debt_minor'], 'entry_minor' => $main['entry_minor']],
                    $main['financed_minor'],
                    'Monetary values are calculated in cents.',
                ),
                new CalculationMemoryStep(
                    'principal_amortization',
                    'Monthly principal reduction',
                    'balance ÷ number of monthly payments',
                    ['financed_minor' => $main['financed_minor'], 'installments' => $main['installments']],
                    $main['base_amortization_minor'],
                    'Rounding differences are absorbed so the modeled principal reaches zero.',
                ),
                new CalculationMemoryStep(
                    'monthly_charge',
                    'Estimated monthly interest/penalty charge',
                    'opening balance × user-supplied monthly rate',
                    ['monthly_charge' => $main['monthly_charge']],
                    $main['first_charge_minor'],
                    'Because the modeled balance declines, estimated charges decline over time.',
                ),
                new CalculationMemoryStep(
                    'final_cost',
                    'Estimated total paid',
                    'upfront payment + sum of monthly payments',
                    ['entry_minor' => $main['entry_minor'], 'installments_total_minor' => $main['installments_total_minor']],
                    $main['final_cost_minor'],
                ),
            ]),
        );
    }

    /** @param array{name:string,debt:string,entry:string,installments:int,monthly_charge:string} $scenario */
    private function calculateScenario(array $scenario, int $index): array
    {
        $debt = Money::fromDecimal($scenario['debt']);
        $entry = Money::fromDecimal($scenario['entry']);
        $installments = $scenario['installments'];
        $rate = Percentage::fromString($scenario['monthly_charge']);

        if ($debt->minorAmount() <= 0 || $entry->minorAmount() < 0 || $entry->minorAmount() >= $debt->minorAmount()) {
            throw new InvalidArgumentException('Invalid tax balance or upfront payment in scenario '.($index + 1).'.');
        }
        if ($installments < 1 || $installments > 72 || $rate->millionthsOfPercent() < 0) {
            throw new InvalidArgumentException('Invalid term or monthly rate in scenario '.($index + 1).'.');
        }

        $financed = $debt->minorAmount() - $entry->minorAmount();
        $baseAmortization = IntegerRounding::divide($financed, $installments);
        $schedule = [];
        $balance = $financed;
        $paidPrincipal = 0;
        $totalCharges = 0;
        $installmentsTotal = 0;

        for ($month = 1; $month <= $installments; $month++) {
            $remainingPrincipal = $financed - $paidPrincipal;
            $amortization = $month === $installments
                ? $remainingPrincipal
                : min($remainingPrincipal, $baseAmortization);
            $charge = Money::fromMinor($balance)->percentage($rate)->minorAmount();
            $payment = $amortization + $charge;
            $closing = max(0, $balance - $amortization);

            $totalCharges += $charge;
            $installmentsTotal += $payment;
            $paidPrincipal += $amortization;
            $schedule[] = [
                'month' => $month,
                'opening_balance_minor' => $balance,
                'amortization_minor' => $amortization,
                'charge_minor' => $charge,
                'payment_minor' => $payment,
                'closing_balance_minor' => $closing,
            ];
            $balance = $closing;
        }

        $finalCost = $entry->minorAmount() + $installmentsTotal;

        return [
            'name' => trim($scenario['name']) !== '' ? trim($scenario['name']) : 'Scenario '.($index + 1),
            'debt_minor' => $debt->minorAmount(),
            'entry_minor' => $entry->minorAmount(),
            'financed_minor' => $financed,
            'installments' => $installments,
            'monthly_charge' => $rate->toDecimalString(),
            'base_amortization_minor' => $baseAmortization,
            'first_charge_minor' => $schedule[0]['charge_minor'],
            'first_installment_minor' => $schedule[0]['payment_minor'],
            'last_installment_minor' => $schedule[array_key_last($schedule)]['payment_minor'],
            'average_installment_minor' => IntegerRounding::divide($installmentsTotal, $installments),
            'total_charges_minor' => $totalCharges,
            'installments_total_minor' => $installmentsTotal,
            'final_cost_minor' => $finalCost,
            'schedule' => $schedule,
        ];
    }
}
