<?php

declare(strict_types=1);

namespace App\Tools\LaborChargesCalculator\Domain\Services;

use App\Core\Dates\ReferenceDate;
use App\Core\Labor\Normative\EmployerChargeRule;
use App\Core\Tools\Calculation\Data\CalculationMemory;
use App\Core\Tools\Calculation\Data\CalculationMemoryStep;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\LaborChargesCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function __construct(private readonly EmployerChargeRule $chargeRule = new EmployerChargeRule()) {}

    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) throw new InvalidArgumentException('Entrada incompatível.');
        $rates = $this->chargeRule->ratesFor($input->regime, $input->rat, $input->thirdParties);
        $thirteenth = $input->salary->divide(12); $vacation = $input->salary->divide(12); $vacationThird = $input->salary->divide(36);
        $provisions = $thirteenth->add($vacation)->add($vacationThird); $incidenceBase = $input->salary->add($provisions);
        $fgts = $incidenceBase->percentage($rates->fgts); $cpp = $incidenceBase->percentage($rates->cpp);
        $rat = $incidenceBase->percentage($rates->rat); $third = $incidenceBase->percentage($rates->thirdParties);
        $charges = $fgts->add($cpp)->add($rat)->add($third); $total = $input->salary->add($input->benefits)->add($provisions)->add($charges);

        return new ToolCalculationResult('encargos-trabalhistas', '1.1.0', [
            new ToolCalculationSummaryItem('total_cost', 'Custo mensal total provisionado', $total->formatPtBr()),
            new ToolCalculationSummaryItem('provisions', 'Provisões de 13º e férias', $provisions->formatPtBr()),
            new ToolCalculationSummaryItem('fgts', 'FGTS provisionado', $fgts->formatPtBr(), $rates->fgts->toDecimalString().'% sobre a base com provisões.'),
            new ToolCalculationSummaryItem('patronal', 'CPP, RAT e terceiros', $cpp->add($rat)->add($third)->formatPtBr()),
        ], ['input' => $input->toArray(), 'rates' => $rates->toArray(), 'amounts' => ['incidence_base_minor' => $incidenceBase->minorAmount(), 'charges_minor' => $charges->minorAmount(), 'total_cost_minor' => $total->minorAmount()]],
        calculationMemory: new CalculationMemory('1.0.0', [
            new CalculationMemoryStep('provisions', 'Provisões de 13º e férias', 'salário/12 + salário/12 + salário/36', ['salary_minor' => $input->salary->minorAmount()], $provisions->minorAmount(), 'Money::divide'),
            new CalculationMemoryStep('incidence_base', 'Base provisionada', 'salário + provisões', ['salary_minor' => $input->salary->minorAmount(), 'provisions_minor' => $provisions->minorAmount()], $incidenceBase->minorAmount(), 'Money::add'),
            new CalculationMemoryStep('charges', 'Encargos', 'base × (FGTS + CPP + RAT + terceiros)', ['incidence_base_minor' => $incidenceBase->minorAmount(), ...$rates->toArray()], $charges->minorAmount(), 'Money::percentage'),
            new CalculationMemoryStep('total', 'Custo total', 'salário + benefícios + provisões + encargos', ['salary_minor' => $input->salary->minorAmount(), 'benefits_minor' => $input->benefits->minorAmount(), 'provisions_minor' => $provisions->minorAmount(), 'charges_minor' => $charges->minorAmount()], $total->minorAmount(), 'Money::add'),
        ], [$this->chargeRule->snapshot(ReferenceDate::fromString('2026-07-25'))], ['RAT e terceiros dependem do enquadramento informado.'], true));
    }
}
