<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Domain\Services;

use App\Core\Money\Money;
use App\Tools\VacationCalculator\Domain\Data\VacationInput;
use App\Tools\VacationCalculator\Domain\Data\VacationResult;
use App\Tools\VacationCalculator\Domain\Rules\VacationEntitlementRule;

final readonly class VacationCalculator
{
    public const RULE_VERSION = '1.0.0';

    public function __construct(private ?VacationEntitlementRule $entitlementRule = null) {}

    public function calculate(VacationInput $input): VacationResult
    {
        $base = $input->remunerationBase();
        $entitledDays = ($this->entitlementRule ?? new VacationEntitlementRule)->entitledDays($input->unjustifiedAbsences);
        $cashAllowanceDays = $input->convertOneThirdToCash ? intdiv($entitledDays, 3) : 0;
        $leaveDays = $entitledDays - $cashAllowanceDays;

        $vacationRemuneration = $this->proportionalAmount($base, $leaveDays);
        $cashAllowance = $this->proportionalAmount($base, $cashAllowanceDays);
        $vacationThird = $vacationRemuneration->divide(3);
        $cashAllowanceThird = $cashAllowance->divide(3);

        $grossTotal = Money::zero($base->currency())
            ->add($vacationRemuneration)
            ->add($vacationThird)
            ->add($cashAllowance)
            ->add($cashAllowanceThird);
        $netTotal = $grossTotal->subtract($input->otherDeductions);

        $acquisitionEndDate = $input->acquisitionStartDate->modify('+1 year -1 day');
        $concessionDeadline = $acquisitionEndDate->modify('+1 year');
        $paymentDeadline = $input->vacationStartDate->modify('-2 days');
        $concessionPeriodOverdue = $input->vacationStartDate > $concessionDeadline;

        $warnings = [];
        if ($entitledDays === 0) {
            $warnings[] = 'Mais de 32 faltas injustificadas eliminam o direito às férias deste período aquisitivo.';
        }
        if ($input->commissionAverage->minorAmount() + $input->overtimeAverage->minorAmount() + $input->recurringAdditions->minorAmount() > 0) {
            $warnings[] = 'A remuneração-base inclui médias e adicionais informados pelo usuário.';
        }
        if ($input->otherDeductions->minorAmount() > 0) {
            $warnings[] = 'Os descontos foram informados manualmente; incidências de INSS e IRRF ainda não são apuradas neste lote.';
        }
        if ($concessionPeriodOverdue) {
            $warnings[] = 'A data informada ultrapassa o período concessivo e exige revisão das consequências trabalhistas aplicáveis.';
        }
        if ($input->vacationStartDate <= $acquisitionEndDate) {
            $warnings[] = 'A data de início está dentro do período aquisitivo; confirme se o caso envolve férias coletivas ou outra hipótese específica.';
        }

        return new VacationResult(
            remunerationBase: $base,
            entitledDays: $entitledDays,
            leaveDays: $leaveDays,
            cashAllowanceDays: $cashAllowanceDays,
            vacationRemuneration: $vacationRemuneration,
            vacationThird: $vacationThird,
            cashAllowance: $cashAllowance,
            cashAllowanceThird: $cashAllowanceThird,
            grossTotal: $grossTotal,
            otherDeductions: $input->otherDeductions,
            netTotal: $netTotal,
            acquisitionEndDate: $acquisitionEndDate,
            concessionDeadline: $concessionDeadline,
            paymentDeadline: $paymentDeadline,
            concessionPeriodOverdue: $concessionPeriodOverdue,
            warnings: $warnings,
        );
    }

    private function proportionalAmount(Money $base, int $days): Money
    {
        return $base->multiply($days)->divide(30);
    }
}
