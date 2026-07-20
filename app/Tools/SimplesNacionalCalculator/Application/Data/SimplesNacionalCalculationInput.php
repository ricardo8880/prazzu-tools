<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Data;

use App\Core\Money\Money;
use App\Core\Tools\Contracts\ToolCalculationInput;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;

final readonly class SimplesNacionalCalculationInput implements ToolCalculationInput
{
    public function __construct(
        public TaxAnnex $annex,
        public Money $rbt12,
        public Money $monthlyRevenue,
    ) {}

    /** @param array{annex:string,rbt12:string,monthly_revenue:string} $input */
    public static function fromArray(array $input): self
    {
        return new self(
            annex: TaxAnnex::from($input['annex']),
            rbt12: Money::fromDecimal($input['rbt12']),
            monthlyRevenue: Money::fromDecimal($input['monthly_revenue']),
        );
    }

    /** @return array{annex:string,rbt12:string,monthly_revenue:string} */
    public function toArray(): array
    {
        return [
            'annex' => $this->annex->value,
            'rbt12' => self::moneyToDecimal($this->rbt12),
            'monthly_revenue' => self::moneyToDecimal($this->monthlyRevenue),
        ];
    }

    private static function moneyToDecimal(Money $money): string
    {
        $minorAmount = $money->minorAmount();
        $sign = $minorAmount < 0 ? '-' : '';
        $absolute = abs($minorAmount);

        return $sign.intdiv($absolute, 100).'.'.str_pad((string) ($absolute % 100), 2, '0', STR_PAD_LEFT);
    }
}
