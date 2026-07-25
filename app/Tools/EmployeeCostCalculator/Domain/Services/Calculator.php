<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Labor\Normative\EmployerChargeRule;
use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\EmployeeCostCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function __construct(private readonly EmployerChargeRule $chargeRule = new EmployerChargeRule()) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) throw new InvalidArgumentException('Entrada incompatível.');
        if ($input->monthlyHours < 1 || $input->monthlyHours > 744) throw new InvalidArgumentException('A jornada mensal deve estar entre 1 e 744 horas.');

        $rates = $this->chargeRule->ratesFor($input->regime, $input->rat, $input->thirdParties);
        $remuneration = $input->salary->add($input->variablePay);
        $thirteenth = $remuneration->divide(12);
        $vacation = $remuneration->divide(12);
        $vacationThird = $remuneration->divide(36);
        $provisions = $thirteenth->add($vacation)->add($vacationThird);
        $incidenceBase = $remuneration->add($provisions);
        $fgts = $incidenceBase->percentage($rates->fgts);
        $cpp = $incidenceBase->percentage($rates->cpp);
        $rat = $incidenceBase->percentage($rates->rat);
        $thirdParties = $incidenceBase->percentage($rates->thirdParties);
        $charges = $fgts->add($cpp)->add($rat)->add($thirdParties);
        $monthly = $remuneration->add($input->benefits)->add($provisions)->add($charges);
        $annual = $monthly->multiply(12);
        $hourly = $monthly->divide($input->monthlyHours);
        $additional = $monthly->subtract($remuneration);
        $additionalPercentage = Percentage::fromBasisPoints(intdiv(($additional->minorAmount() * 10_000) + intdiv($remuneration->minorAmount(), 2), $remuneration->minorAmount()));
        $snapshot = $this->chargeRule->snapshot(ReferenceDate::fromString('2026-07-25'));

        return new ToolCalculationResult(
            toolSlug: 'custo-funcionario-clt', schemaVersion: '1.2.0',
            summary: [
                new ToolCalculationSummaryItem('monthly_cost', 'Custo mensal provisionado', $monthly->formatPtBr()),
                new ToolCalculationSummaryItem('annual_cost', 'Custo anual projetado', $annual->formatPtBr()),
                new ToolCalculationSummaryItem('hourly_cost', 'Custo por hora', $hourly->formatPtBr()),
                new ToolCalculationSummaryItem('additional_percentage', 'Custo adicional sobre a remuneração', $additionalPercentage->toDecimalString().'%'),
                new ToolCalculationSummaryItem('provisions', '13º, férias e terço', $provisions->formatPtBr()),
                new ToolCalculationSummaryItem('charges', 'FGTS e encargos patronais', $charges->formatPtBr()),
            ],
            details: ['input' => $input->toArray(), 'rates' => $rates->toArray(), 'amounts' => [
                'remuneration_minor' => $remuneration->minorAmount(), 'provisions_minor' => $provisions->minorAmount(),
                'fgts_minor' => $fgts->minorAmount(), 'cpp_minor' => $cpp->minorAmount(), 'rat_minor' => $rat->minorAmount(),
                'third_parties_minor' => $thirdParties->minorAmount(), 'charges_minor' => $charges->minorAmount(),
                'monthly_cost_minor' => $monthly->minorAmount(), 'annual_cost_minor' => $annual->minorAmount(), 'hourly_cost_minor' => $hourly->minorAmount(),
            ]],
            calculationMemory: new CalculationMemory('1.0.0', [
                new CalculationMemoryStep('remuneration', 'Remuneração mensal', 'salário + remuneração variável', ['salary_minor' => $input->salary->minorAmount(), 'variable_pay_minor' => $input->variablePay->minorAmount()], $remuneration->minorAmount(), 'Money::add'),
                new CalculationMemoryStep('provisions', 'Provisões mensais', '13º (1/12) + férias (1/12) + terço constitucional (1/36)', ['remuneration_minor' => $remuneration->minorAmount()], $provisions->minorAmount(), 'Money::divide'),
                new CalculationMemoryStep('charges', 'Encargos sobre base provisionada', 'base × (FGTS + CPP + RAT + terceiros)', ['incidence_base_minor' => $incidenceBase->minorAmount(), ...$rates->toArray()], $charges->minorAmount(), 'Money::percentage'),
                new CalculationMemoryStep('monthly_cost', 'Custo mensal', 'remuneração + benefícios + provisões + encargos', ['remuneration_minor' => $remuneration->minorAmount(), 'benefits_minor' => $input->benefits->minorAmount(), 'provisions_minor' => $provisions->minorAmount(), 'charges_minor' => $charges->minorAmount()], $monthly->minorAmount(), 'Money::add'),
            ], [$snapshot], ['RAT e terceiros são informados pelo usuário conforme atividade, FPAS e enquadramento.'], true),
        );
    }
}
