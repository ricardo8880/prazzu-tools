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
            throw new InvalidArgumentException('Entrada incompatível com a calculadora de parcelamento tributário.');
        }
        if ($input->scenarios === []) {
            throw new InvalidArgumentException('Informe ao menos um cenário.');
        }

        $scenarios = [];
        foreach ($input->scenarios as $index => $scenario) {
            $scenarios[] = $this->calculateScenario($scenario, $index);
        }

        $main = $scenarios[0];

        return new ToolCalculationResult(
            toolSlug: 'calculadora-parcelamento-tributario',
            schemaVersion: '1.0.0',
            summary: [
                new ToolCalculationSummaryItem('debt', 'Dívida informada', Money::fromMinor($main['debt_minor'])->formatPtBr()),
                new ToolCalculationSummaryItem('average_installment', 'Parcela média aproximada', Money::fromMinor($main['average_installment_minor'])->formatPtBr()),
                new ToolCalculationSummaryItem('charges', 'Encargos estimados', Money::fromMinor($main['total_charges_minor'])->formatPtBr()),
                new ToolCalculationSummaryItem('final_cost', 'Custo final estimado', Money::fromMinor($main['final_cost_minor'])->formatPtBr()),
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
                    'Simulação aproximada pelo sistema de amortização constante (SAC): principal uniforme e encargos mensais sobre o saldo devedor. O valor real depende das regras do programa de parcelamento.',
                    ToolCalculationWarningLevel::Info,
                ),
                new ToolCalculationWarning(
                    'normative_scope',
                    'A ferramenta não consulta nem presume regras de Receita Federal, PGFN, Simples Nacional, estados ou municípios. Informe a taxa de encargos aplicável ao seu caso e confirme entrada mínima, parcela mínima, descontos e atualização monetária no órgão responsável.',
                    ToolCalculationWarningLevel::Info,
                ),
            ],
            calculationMemory: new CalculationMemory('1.0.0', [
                new CalculationMemoryStep(
                    'financed_balance',
                    'Saldo parcelado',
                    'dívida − entrada',
                    ['debt_minor' => $main['debt_minor'], 'entry_minor' => $main['entry_minor']],
                    $main['financed_minor'],
                    'Valores monetários são calculados em centavos, sem ponto flutuante.',
                ),
                new CalculationMemoryStep(
                    'principal_amortization',
                    'Amortização do principal',
                    'saldo parcelado ÷ quantidade de parcelas',
                    ['financed_minor' => $main['financed_minor'], 'installments' => $main['installments']],
                    $main['base_amortization_minor'],
                    'Diferenças de centavos são absorvidas nas parcelas para zerar o saldo ao final.',
                ),
                new CalculationMemoryStep(
                    'monthly_charge',
                    'Encargo de cada mês',
                    'saldo devedor inicial do mês × taxa mensal informada',
                    ['monthly_charge' => $main['monthly_charge']],
                    $main['first_charge_minor'],
                    'Como o saldo cai ao longo do tempo, os encargos e as parcelas também diminuem no modelo SAC.',
                ),
                new CalculationMemoryStep(
                    'final_cost',
                    'Custo final estimado',
                    'entrada + soma das parcelas',
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
            throw new InvalidArgumentException('Dívida ou entrada inválida no cenário '.($index + 1).'.');
        }
        if ($installments < 1 || $installments > 240 || $rate->millionthsOfPercent() < 0) {
            throw new InvalidArgumentException('Prazo ou encargos inválidos no cenário '.($index + 1).'.');
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
            'name' => trim($scenario['name']) !== '' ? trim($scenario['name']) : 'Cenário '.($index + 1),
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
