<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Domain\Calculators;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Tools\LaborTerminationCalculator\Domain\Data\LaborTerminationResult;
use App\Tools\LaborTerminationCalculator\Domain\Enums\NoticeType;
use App\Tools\LaborTerminationCalculator\Domain\Enums\TerminationType;
use DateTimeImmutable;

final class LaborTerminationCalculator
{
    public const RULE_VERSION = '1.4.0';

    public function calculate(
        Money $monthlySalary,
        DateTimeImmutable $admissionDate,
        DateTimeImmutable $terminationDate,
        TerminationType $terminationType,
        string $contractType,
        NoticeType $noticeType,
        int $daysWorkedInMonth,
        int $overdueVacationPeriods,
        int $doubleVacationPeriods,
        Money $fgtsBalance,
        Money $domesticIndemnityReserveBalance,
        Money $otherDiscounts,
        int $dependents,
        Money $commissionAverage,
        Money $overtimeAverage,
        Money $recurringAdditions,
        ?DateTimeImmutable $contractEndDate,
        string $earlyTerminationInitiative,
        Money $article480Discount,
        Money $extraordinaryIndemnities,
    ): LaborTerminationResult {
        $salaryBase = $monthlySalary->add($commissionAverage)->add($overtimeAverage)->add($recurringAdditions);
        $this->validate($monthlySalary, $salaryBase, $admissionDate, $terminationDate, $terminationType, $contractType, $noticeType, $daysWorkedInMonth, $overdueVacationPeriods, $doubleVacationPeriods, $contractEndDate, $earlyTerminationInitiative, $article480Discount);

        foreach ([$fgtsBalance, $domesticIndemnityReserveBalance, $otherDiscounts, $commissionAverage, $overtimeAverage, $recurringAdditions, $article480Discount, $extraordinaryIndemnities] as $money) {
            if ($money->minorAmount() < 0) {
                throw new InvalidValue('Os valores adicionais não podem ser negativos.');
            }
        }
        if ($dependents < 0 || $dependents > 99) {
            throw new InvalidValue('A quantidade de dependentes deve possuir valor válido.');
        }

        $noticeDays = $this->noticeDays($admissionDate, $terminationDate, $terminationType, $noticeType);
        $noticePay = $this->noticePay($salaryBase, $terminationType, $noticeType, $noticeDays);
        $noticeDiscount = $this->noticeDiscount($salaryBase, $terminationType, $noticeType);
        $projectedTerminationDate = $noticePay->minorAmount() > 0 ? $terminationDate->modify(sprintf('+%d days', $noticeDays)) : $terminationDate;

        $dailySalary = $salaryBase->divide(30);
        $salaryBalance = $salaryBase->multiply($daysWorkedInMonth)->divide(30);
        $vacationMonths = $terminationType->grantsProportionalVacation() ? $this->proportionalVacationMonths($admissionDate, $projectedTerminationDate) : 0;
        $thirteenthMonths = $terminationType->grantsProportionalThirteenth() ? $this->proportionalThirteenthMonths($admissionDate, $projectedTerminationDate) : 0;

        $regularOverduePeriods = max(0, $overdueVacationPeriods - $doubleVacationPeriods);
        $overdueVacation = $salaryBase->multiply($regularOverduePeriods)->add($salaryBase->multiply($doubleVacationPeriods * 2));
        $overdueVacationThird = $overdueVacation->divide(3);
        $proportionalVacation = $salaryBase->multiply($vacationMonths)->divide(12);
        $proportionalVacationThird = $proportionalVacation->divide(3);
        $proportionalThirteenthSalary = $salaryBase->multiply($thirteenthMonths)->divide(12);

        $remainingContractDays = $contractEndDate ? max(0, ((int) $terminationDate->diff($contractEndDate)->format('%a'))) : 0;
        $article479Indemnity = Money::zero($salaryBase->currency());
        if ($terminationType === TerminationType::EarlyContractEnd && $earlyTerminationInitiative === 'employer') {
            $article479Indemnity = $dailySalary->multiply($remainingContractDays)->divide(2);
        }

        $grossTotal = Money::zero($salaryBase->currency())
            ->add($salaryBalance)->add($overdueVacation)->add($overdueVacationThird)
            ->add($proportionalVacation)->add($proportionalVacationThird)->add($proportionalThirteenthSalary)
            ->add($noticePay)->add($article479Indemnity)->add($extraordinaryIndemnities);

        $taxCalculator = new PayrollTaxCalculator;
        $inssSalary = $taxCalculator->inss($salaryBalance);
        $inssThirteenth = $taxCalculator->inss($proportionalThirteenthSalary);
        $irrfSalary = $taxCalculator->irrf($salaryBalance, $inssSalary, $dependents);
        $irrfThirteenth = $taxCalculator->irrf($proportionalThirteenthSalary, $inssThirteenth, $dependents);
        $totalDiscounts = Money::zero($salaryBase->currency())
            ->add($noticeDiscount)->add($inssSalary)->add($inssThirteenth)
            ->add($irrfSalary)->add($irrfThirteenth)->add($otherDiscounts)->add($article480Discount);
        $netTotal = $grossTotal->subtract($totalDiscounts);

        $fgtsBase = $salaryBalance->add($proportionalThirteenthSalary)->add($noticePay);
        $fgtsTerminationDeposit = Money::fromMinor(intdiv(($fgtsBase->minorAmount() * 8) + 50, 100), $salaryBase->currency());
        $isDomestic = $contractType === 'domestic';
        $domesticCompensatoryDeposit = $isDomestic
            ? Money::fromMinor(intdiv(($fgtsBase->minorAmount() * 32) + 500, 1000), $salaryBase->currency())
            : Money::zero($salaryBase->currency());
        $fgtsPenaltyBase = $fgtsBalance->add($fgtsTerminationDeposit);

        if ($isDomestic) {
            $domesticReserveAvailable = in_array($terminationType, [TerminationType::DismissalWithoutCause, TerminationType::IndirectTermination], true);
            $fgtsPenalty = $domesticReserveAvailable
                ? $domesticIndemnityReserveBalance->add($domesticCompensatoryDeposit)
                : Money::zero($salaryBase->currency());
        } else {
            $penaltyRate = match ($terminationType) {
                TerminationType::DismissalWithoutCause, TerminationType::IndirectTermination => 40,
                TerminationType::MutualAgreement => 20,
                default => 0,
            };
            $fgtsPenalty = Money::fromMinor(intdiv(($fgtsPenaltyBase->minorAmount() * $penaltyRate) + 50, 100), $salaryBase->currency());
        }

        $withdrawalPercentage = match ($terminationType) {
            TerminationType::DismissalWithoutCause, TerminationType::IndirectTermination, TerminationType::ContractEnd => 100,
            TerminationType::MutualAgreement => 80,
            default => 0,
        };
        $estimatedFgtsAvailable = Money::fromMinor(intdiv(($fgtsPenaltyBase->minorAmount() * $withdrawalPercentage) + 50, 100), $salaryBase->currency());
        if ($isDomestic && in_array($terminationType, [TerminationType::DismissalWithoutCause, TerminationType::IndirectTermination], true)) {
            $estimatedFgtsAvailable = $estimatedFgtsAvailable->add($fgtsPenalty);
        }

        $warnings = [];
        if ($commissionAverage->minorAmount() + $overtimeAverage->minorAmount() + $recurringAdditions->minorAmount() > 0) {
            $warnings[] = 'A remuneração-base inclui médias e adicionais informados manualmente.';
        }
        if ($doubleVacationPeriods > 0) {
            $warnings[] = 'Férias em dobro dependem da confirmação de que o período concessivo foi ultrapassado.';
        }
        if ($article480Discount->minorAmount() > 0) {
            $warnings[] = 'O desconto do art. 480 foi informado manualmente e depende da comprovação de prejuízo, limitado ao valor equivalente do art. 479.';
        }
        if ($extraordinaryIndemnities->minorAmount() > 0) {
            $warnings[] = 'Indenizações extraordinárias foram adicionadas manualmente e não sofreram incidências automáticas.';
        }
        if ($isDomestic) {
            $warnings[] = 'No emprego doméstico, a indenização compensatória é formada pelos depósitos mensais de 3,2%, e não por multa de 40% calculada sobre o saldo do FGTS.';
        }
        if ($isDomestic && $terminationType === TerminationType::MutualAgreement) {
            $warnings[] = 'A destinação da reserva indenizatória doméstica em rescisão por acordo deve ser conferida no eSocial antes do pagamento.';
        }

        return new LaborTerminationResult(
            monthlySalary: $monthlySalary, salaryBase: $salaryBase, commissionAverage: $commissionAverage, overtimeAverage: $overtimeAverage,
            recurringAdditions: $recurringAdditions, dailySalary: $dailySalary, salaryBalance: $salaryBalance,
            overdueVacation: $overdueVacation, overdueVacationThird: $overdueVacationThird, proportionalVacation: $proportionalVacation,
            proportionalVacationThird: $proportionalVacationThird, proportionalThirteenthSalary: $proportionalThirteenthSalary,
            noticePay: $noticePay, article479Indemnity: $article479Indemnity, extraordinaryIndemnities: $extraordinaryIndemnities,
            noticeDiscount: $noticeDiscount, article480Discount: $article480Discount, grossTotal: $grossTotal,
            inssSalary: $inssSalary, inssThirteenth: $inssThirteenth, irrfSalary: $irrfSalary, irrfThirteenth: $irrfThirteenth,
            otherDiscounts: $otherDiscounts, totalDiscounts: $totalDiscounts, netTotal: $netTotal,
            fgtsBalance: $fgtsBalance, domesticIndemnityReserveBalance: $domesticIndemnityReserveBalance,
            domesticCompensatoryDeposit: $domesticCompensatoryDeposit, fgtsTerminationDeposit: $fgtsTerminationDeposit, fgtsPenalty: $fgtsPenalty,
            estimatedFgtsAvailable: $estimatedFgtsAvailable, fgtsWithdrawalPercentage: $withdrawalPercentage,
            daysWorkedInMonth: $daysWorkedInMonth, proportionalVacationMonths: $vacationMonths, proportionalThirteenthMonths: $thirteenthMonths,
            overdueVacationPeriods: $overdueVacationPeriods, doubleVacationPeriods: $doubleVacationPeriods, noticeDays: $noticeDays,
            remainingContractDays: $remainingContractDays, dependents: $dependents, projectedTerminationDate: $projectedTerminationDate,
            contractEndDate: $contractEndDate, terminationType: $terminationType, noticeType: $noticeType, contractType: $contractType,
            earlyTerminationInitiative: $earlyTerminationInitiative, warnings: $warnings,
            ruleVersion: self::RULE_VERSION, taxTableVersion: PayrollTaxCalculator::TABLE_VERSION,
        );
    }

    private function validate(Money $salary, Money $salaryBase, DateTimeImmutable $admission, DateTimeImmutable $termination, TerminationType $type, string $contractType, NoticeType $notice, int $days, int $overduePeriods, int $doublePeriods, ?DateTimeImmutable $contractEndDate, string $initiative, Money $article480): void
    {
        if ($salary->minorAmount() <= 0 || $salaryBase->minorAmount() <= 0) {
            throw new InvalidValue('O salário e a remuneração-base devem ser maiores que zero.');
        }
        if ($termination < $admission) {
            throw new InvalidValue('A data de desligamento não pode ser anterior à admissão.');
        }
        if ($days < 0 || $days > 31) {
            throw new InvalidValue('Os dias trabalhados devem estar entre 0 e 31.');
        }
        if ($overduePeriods < 0 || $overduePeriods > 3 || $doublePeriods < 0 || $doublePeriods > $overduePeriods) {
            throw new InvalidValue('Os períodos de férias informados são inválidos.');
        }
        if (in_array($contractType, ['fixed_term', 'experience'], true) && $notice !== NoticeType::NotApplicable) {
            throw new InvalidValue('Para contratos por prazo determinado ou de experiência, selecione aviso-prévio não aplicável.');
        }
        if (in_array($type, [TerminationType::DismissalWithCause, TerminationType::ContractEnd, TerminationType::EarlyContractEnd], true) && $notice !== NoticeType::NotApplicable) {
            throw new InvalidValue('O motivo de rescisão selecionado não admite aviso-prévio comum.');
        }
        if ($type->isEmployerInitiatedWithoutCause() && ! in_array($notice, [NoticeType::Worked, NoticeType::Indemnified], true)) {
            throw new InvalidValue('Na dispensa sem justa causa ou rescisão indireta, selecione aviso trabalhado ou indenizado.');
        }
        if ($type === TerminationType::Resignation && ! in_array($notice, [NoticeType::Worked, NoticeType::NotWorked], true)) {
            throw new InvalidValue('No pedido de demissão, selecione aviso trabalhado ou não cumprido.');
        }
        if ($type === TerminationType::MutualAgreement && ! in_array($notice, [NoticeType::Worked, NoticeType::Indemnified], true)) {
            throw new InvalidValue('Na rescisão por acordo, selecione aviso trabalhado ou indenizado.');
        }
        if ($type === TerminationType::EarlyContractEnd && ($contractEndDate === null || $contractEndDate <= $termination || ! in_array($initiative, ['employer', 'employee'], true))) {
            throw new InvalidValue('No término antecipado, informe a data final prevista e quem tomou a iniciativa.');
        }
        if ($type !== TerminationType::EarlyContractEnd && ($contractEndDate !== null || $initiative !== '' || $article480->minorAmount() > 0)) {
            throw new InvalidValue('Dados de término antecipado só podem ser usados nessa modalidade.');
        }
        if ($article480->minorAmount() > 0 && $initiative !== 'employee') {
            throw new InvalidValue('O desconto do art. 480 só pode ser informado quando o empregado antecipa o término.');
        }
        if ($contractEndDate && $article480->minorAmount() > $salaryBase->divide(30)->multiply((int) $termination->diff($contractEndDate)->format('%a'))->divide(2)->minorAmount()) {
            throw new InvalidValue('O desconto do art. 480 não pode superar o limite equivalente ao art. 479.');
        }
    }

    private function noticeDays(DateTimeImmutable $admission, DateTimeImmutable $termination, TerminationType $type, NoticeType $notice): int
    {
        if ($notice === NoticeType::NotApplicable) {
            return 0;
        } if ($type === TerminationType::Resignation) {
            return 30;
        }

        return min(90, 30 + ((int) $admission->diff($termination)->y * 3));
    }

    private function noticePay(Money $salary, TerminationType $type, NoticeType $notice, int $days): Money
    {
        if ($notice !== NoticeType::Indemnified) {
            return Money::zero($salary->currency());
        } $amount = $salary->multiply($days)->divide(30);

        return $type === TerminationType::MutualAgreement ? $amount->divide(2) : $amount;
    }

    private function noticeDiscount(Money $salary, TerminationType $type, NoticeType $notice): Money
    {
        return $type === TerminationType::Resignation && $notice === NoticeType::NotWorked ? $salary : Money::zero($salary->currency());
    }

    private function proportionalVacationMonths(DateTimeImmutable $admissionDate, DateTimeImmutable $terminationDate): int
    {
        $periodStart = $this->currentAcquisitionPeriodStart($admissionDate, $terminationDate);
        $months = 0;
        for ($cursor = $periodStart; $cursor <= $terminationDate && $months < 12; $cursor = $cursor->modify('+1 month')) {
            $monthEnd = $cursor->modify('+1 month')->modify('-1 day');
            $countedUntil = $monthEnd < $terminationDate ? $monthEnd : $terminationDate;
            if (((int) $cursor->diff($countedUntil)->format('%a')) + 1 >= 15) {
                $months++;
            }
        }

        return $months;
    }

    private function currentAcquisitionPeriodStart(DateTimeImmutable $admissionDate, DateTimeImmutable $terminationDate): DateTimeImmutable
    {
        $start = $admissionDate;
        while ($start->modify('+1 year') <= $terminationDate) {
            $start = $start->modify('+1 year');
        }

        return $start;
    }

    private function proportionalThirteenthMonths(DateTimeImmutable $admissionDate, DateTimeImmutable $terminationDate): int
    {
        $yearStart = new DateTimeImmutable($terminationDate->format('Y-01-01'));
        $employmentStart = $admissionDate > $yearStart ? $admissionDate : $yearStart;
        $months = 0;
        for ($month = (int) $employmentStart->format('n'); $month <= (int) $terminationDate->format('n'); $month++) {
            $monthStart = new DateTimeImmutable(sprintf('%s-%02d-01', $terminationDate->format('Y'), $month));
            $countedFrom = $employmentStart > $monthStart ? $employmentStart : $monthStart;
            $countedUntil = $terminationDate < $monthStart->modify('last day of this month') ? $terminationDate : $monthStart->modify('last day of this month');
            if ($countedFrom <= $countedUntil && ((int) $countedFrom->diff($countedUntil)->format('%a')) + 1 >= 15) {
                $months++;
            }
        }

        return $months;
    }
}
