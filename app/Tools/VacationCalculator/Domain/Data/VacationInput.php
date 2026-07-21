<?php

declare(strict_types=1);

namespace App\Tools\VacationCalculator\Domain\Data;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use DateTimeImmutable;

final readonly class VacationInput
{
    public function __construct(
        public Money $monthlySalary,
        public DateTimeImmutable $acquisitionStartDate,
        public DateTimeImmutable $vacationStartDate,
        public int $unjustifiedAbsences,
        public bool $convertOneThirdToCash,
        public Money $commissionAverage,
        public Money $overtimeAverage,
        public Money $recurringAdditions,
        public Money $otherDeductions,
    ) {
        foreach ([$this->monthlySalary, $this->commissionAverage, $this->overtimeAverage, $this->recurringAdditions, $this->otherDeductions] as $amount) {
            if ($amount->minorAmount() < 0) {
                throw new InvalidValue('Os valores da calculadora de férias não podem ser negativos.');
            }
        }

        if ($this->monthlySalary->minorAmount() === 0) {
            throw new InvalidValue('O salário mensal deve ser maior que zero.');
        }

        if ($this->unjustifiedAbsences < 0 || $this->unjustifiedAbsences > 365) {
            throw new InvalidValue('A quantidade de faltas injustificadas deve estar entre 0 e 365.');
        }

        if ($this->vacationStartDate < $this->acquisitionStartDate) {
            throw new InvalidValue('O início das férias não pode ser anterior ao início do período aquisitivo.');
        }
    }

    public function remunerationBase(): Money
    {
        return $this->monthlySalary
            ->add($this->commissionAverage)
            ->add($this->overtimeAverage)
            ->add($this->recurringAdditions);
    }
}
