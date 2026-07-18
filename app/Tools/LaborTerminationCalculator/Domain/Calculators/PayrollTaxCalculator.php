<?php

declare(strict_types=1);

namespace App\Tools\LaborTerminationCalculator\Domain\Calculators;

use App\Core\Money\Money;

final class PayrollTaxCalculator
{
    public const TABLE_VERSION = '2026.1';

    public function inss(Money $base): Money
    {
        $amount = max(0, min($base->minorAmount(), 847555));
        $tax = 0;
        $previousLimit = 0;

        foreach ([[162100, 750], [290284, 900], [435427, 1200], [847555, 1400]] as [$limit, $rate]) {
            if ($amount <= $previousLimit) {
                break;
            }
            $taxable = min($amount, $limit) - $previousLimit;
            // O eSocial determina truncamento em centavos para cada faixa progressiva.
            $tax += intdiv($taxable * $rate, 10000);
            $previousLimit = $limit;
        }

        return Money::fromMinor($tax, $base->currency());
    }

    public function irrf(Money $taxableIncome, Money $inss, int $dependents): Money
    {
        $legalDeductions = $inss->minorAmount() + ($dependents * 18959);
        $deduction = max($legalDeductions, 60720);
        $base = max(0, $taxableIncome->minorAmount() - $deduction);

        [$rate, $fixedDeduction] = match (true) {
            $base <= 242880 => [0, 0],
            $base <= 282665 => [750, 18216],
            $base <= 375105 => [1500, 39416],
            $base <= 466468 => [2250, 67549],
            default => [2750, 90873],
        };

        $tax = max(0, intdiv(($base * $rate) + 5000, 10000) - $fixedDeduction);
        $income = $taxableIncome->minorAmount();
        $reduction = match (true) {
            $income <= 500000 => $tax,
            $income <= 735000 => max(0, 97862 - intdiv(($income * 133145) + 500000, 1000000)),
            default => 0,
        };

        return Money::fromMinor(max(0, $tax - min($tax, $reduction)), $taxableIncome->currency());
    }
}
