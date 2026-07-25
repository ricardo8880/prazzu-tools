<?php

declare(strict_types=1);

namespace App\Tools\EmployeeCostCalculator\Domain\Services;

use App\Core\Money\Percentage;
use App\Core\Tools\Calculation\Data\ToolCalculationResult;
use App\Core\Tools\Calculation\Data\ToolCalculationSummaryItem;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Core\Tools\Contracts\ToolCalculator;
use App\Tools\EmployeeCostCalculator\Application\Data\CalculationInput;
use InvalidArgumentException;

final class Calculator implements ToolCalculator
{
    public function calculate(ToolCalculationInput $input): ToolCalculationResult
    {
        if (! $input instanceof CalculationInput) {
            throw new InvalidArgumentException('Entrada incompatível.');
        }
        if (! in_array($input->regime, ['general', 'simples_annex_iv', 'simples_other'], true)) {
            throw new InvalidArgumentException('Regime inválido.');
        }
        if ($input->monthlyHours < 1 || $input->monthlyHours > 744) {
            throw new InvalidArgumentException('A jornada mensal deve estar entre 1 e 744 horas.');
        }

        $remuneration = $input->salary->add($input->variablePay);
        $thirteenth = $remuneration->divide(12);
        $vacation = $remuneration->divide(12);
        $vacationThird = $remuneration->divide(36);
        $provisions = $thirteenth->add($vacation)->add($vacationThird);
        $incidenceBase = $remuneration->add($provisions);
        $fgts = $incidenceBase->percentage(Percentage::fromString('8'));
        $cppRate = $input->regime === 'simples_other' ? Percentage::zero() : Percentage::fromString('20');
        $ratRate = $input->regime === 'simples_other' ? Percentage::zero() : $input->rat;
        $thirdPartiesRate = str_starts_with($input->regime, 'simples_')
            ? Percentage::zero()
            : $input->thirdParties;
        $cpp = $incidenceBase->percentage($cppRate);
        $rat = $incidenceBase->percentage($ratRate);
        $thirdParties = $incidenceBase->percentage($thirdPartiesRate);
        $charges = $fgts->add($cpp)->add($rat)->add($thirdParties);
        $monthly = $remuneration->add($input->benefits)->add($provisions)->add($charges);
        $annual = $monthly->multiply(12);
        $hourly = $monthly->divide($input->monthlyHours);
        $additional = $monthly->subtract($remuneration);
        $additionalBasisPoints = intdiv(
            ($additional->minorAmount() * 10_000) + intdiv($remuneration->minorAmount(), 2),
            $remuneration->minorAmount(),
        );
        $additionalPercentage = Percentage::fromBasisPoints($additionalBasisPoints);

        return new ToolCalculationResult(
            toolSlug: 'custo-funcionario-clt',
            schemaVersion: '1.1.0',
            summary: [
                new ToolCalculationSummaryItem('monthly_cost', 'Custo mensal provisionado', $monthly->formatPtBr()),
                new ToolCalculationSummaryItem('annual_cost', 'Custo anual projetado', $annual->formatPtBr()),
                new ToolCalculationSummaryItem('hourly_cost', 'Custo por hora', $hourly->formatPtBr()),
                new ToolCalculationSummaryItem('additional_percentage', 'Custo adicional sobre a remuneração', $additionalPercentage->toDecimalString().'%'),
                new ToolCalculationSummaryItem('provisions', '13º, férias e terço', $provisions->formatPtBr()),
                new ToolCalculationSummaryItem('charges', 'FGTS e encargos patronais', $charges->formatPtBr()),
            ],
            details: [
                'input' => $input->toArray(),
                'amounts' => [
                    'remuneration_minor' => $remuneration->minorAmount(),
                    'thirteenth_minor' => $thirteenth->minorAmount(),
                    'vacation_minor' => $vacation->minorAmount(),
                    'vacation_third_minor' => $vacationThird->minorAmount(),
                    'fgts_minor' => $fgts->minorAmount(),
                    'cpp_minor' => $cpp->minorAmount(),
                    'rat_minor' => $rat->minorAmount(),
                    'third_parties_minor' => $thirdParties->minorAmount(),
                    'benefits_minor' => $input->benefits->minorAmount(),
                    'provisions_minor' => $provisions->minorAmount(),
                    'charges_minor' => $charges->minorAmount(),
                    'monthly_cost_minor' => $monthly->minorAmount(),
                    'annual_cost_minor' => $annual->minorAmount(),
                    'hourly_cost_minor' => $hourly->minorAmount(),
                    'additional_percentage' => $additionalPercentage->toDecimalString(),
                ],
                'memory' => [
                    'Remuneração mensal' => $remuneration->formatPtBr(),
                    '13º mensal' => $thirteenth->formatPtBr(),
                    'Férias mensais' => $vacation->formatPtBr(),
                    'Terço de férias mensal' => $vacationThird->formatPtBr(),
                    'FGTS 8%' => $fgts->formatPtBr(),
                    'CPP '.$cppRate->toDecimalString().'%' => $cpp->formatPtBr(),
                    'RAT '.$ratRate->toDecimalString().'%' => $rat->formatPtBr(),
                    'Terceiros '.$thirdPartiesRate->toDecimalString().'%' => $thirdParties->formatPtBr(),
                    'Benefícios' => $input->benefits->formatPtBr(),
                    'Custo mensal' => $monthly->formatPtBr(),
                    'Custo anual = mensal × 12' => $annual->formatPtBr(),
                    'Custo por hora = mensal ÷ '.$input->monthlyHours => $hourly->formatPtBr(),
                    'Custo adicional sobre a remuneração' => $additionalPercentage->toDecimalString().'%',
                ],
            ],
        );
    }
}
