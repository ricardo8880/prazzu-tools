<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Data;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Core\Money\Percentage;

final readonly class TaxBracket
{
    public function __construct(
        public int $number,
        public Money $revenueFrom,
        public Money $revenueUntil,
        public Percentage $nominalRate,
        public Money $deduction,
    ) {
        if ($number < 1) {
            throw new InvalidValue('O número da faixa deve ser maior que zero.');
        }

        if ($revenueUntil->minorAmount() < $revenueFrom->minorAmount()) {
            throw new InvalidValue('O limite final da faixa não pode ser menor que o limite inicial.');
        }

        if ($nominalRate->millionthsOfPercent() < 0) {
            throw new InvalidValue('A alíquota nominal não pode ser negativa.');
        }

        if ($deduction->minorAmount() < 0) {
            throw new InvalidValue('A parcela a deduzir não pode ser negativa.');
        }
    }

    public function contains(Money $revenue): bool
    {
        return $revenue->minorAmount() >= $this->revenueFrom->minorAmount()
            && $revenue->minorAmount() <= $this->revenueUntil->minorAmount();
    }
}
