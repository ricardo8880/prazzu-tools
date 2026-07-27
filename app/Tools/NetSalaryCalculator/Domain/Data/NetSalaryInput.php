<?php

declare(strict_types=1);

namespace App\Tools\NetSalaryCalculator\Domain\Data;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use InvalidArgumentException;

final readonly class NetSalaryInput
{
    public function __construct(
        public Competence $competence,
        public Money $baseSalary,
        public Money $taxableAdditionalEarnings,
        public Money $nonTaxableEarnings,
        public int $dependents,
        public Money $judicialPension,
        public Money $transportDiscount,
        public Money $mealDiscount,
        public Money $healthPlanDiscount,
        public Money $otherDiscounts,
    ) {
        foreach ([
            'salário-base' => $this->baseSalary,
            'proventos tributáveis adicionais' => $this->taxableAdditionalEarnings,
            'proventos não tributáveis' => $this->nonTaxableEarnings,
            'pensão alimentícia judicial' => $this->judicialPension,
            'vale-transporte' => $this->transportDiscount,
            'vale-refeição/alimentação' => $this->mealDiscount,
            'plano de saúde' => $this->healthPlanDiscount,
            'outros descontos' => $this->otherDiscounts,
        ] as $label => $amount) {
            if ($amount->minorAmount() < 0) {
                throw new InvalidArgumentException("O valor de {$label} não pode ser negativo.");
            }
        }

        if ($this->baseSalary->minorAmount() <= 0) {
            throw new InvalidArgumentException('O salário-base deve ser maior que zero.');
        }

        if ($this->dependents < 0 || $this->dependents > 99) {
            throw new InvalidArgumentException('A quantidade de dependentes deve estar entre 0 e 99.');
        }
    }

    public function taxableGross(): Money
    {
        return $this->baseSalary->add($this->taxableAdditionalEarnings);
    }

    public function totalEarnings(): Money
    {
        return $this->taxableGross()->add($this->nonTaxableEarnings);
    }

    public function userDiscounts(): Money
    {
        return $this->judicialPension
            ->add($this->transportDiscount)
            ->add($this->mealDiscount)
            ->add($this->healthPlanDiscount)
            ->add($this->otherDiscounts);
    }
}
