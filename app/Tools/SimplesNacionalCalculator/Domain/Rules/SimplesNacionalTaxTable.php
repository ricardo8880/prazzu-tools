<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Domain\Rules;

use App\Core\Exceptions\InvalidValue;
use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SimplesNacionalCalculator\Domain\Data\TaxBracket;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;

final class SimplesNacionalTaxTable
{
    public const RULE_VERSION = 'LC123-2018-v1';

    public const VALID_FROM = '2018-01-01';

    /** @return list<TaxBracket> */
    public function bracketsFor(TaxAnnex $annex): array
    {
        return array_map(
            static fn (array $row): TaxBracket => new TaxBracket(
                number: $row[0],
                revenueFrom: Money::fromDecimal($row[1]),
                revenueUntil: Money::fromDecimal($row[2]),
                nominalRate: Percentage::fromString($row[3]),
                deduction: Money::fromDecimal($row[4]),
            ),
            $this->rows()[$annex->value],
        );
    }

    public function bracketFor(TaxAnnex $annex, Money $rbt12): TaxBracket
    {
        foreach ($this->bracketsFor($annex) as $bracket) {
            if ($bracket->contains($rbt12)) {
                return $bracket;
            }
        }

        throw new InvalidValue('O RBT12 deve estar entre R$ 0,01 e R$ 4.800.000,00.');
    }

    /**
     * @return array<string, list<array{int, string, string, string, string}>>
     */
    private function rows(): array
    {
        return [
            TaxAnnex::I->value => [
                [1, '0.01', '180000.00', '4.00', '0.00'],
                [2, '180000.01', '360000.00', '7.30', '5940.00'],
                [3, '360000.01', '720000.00', '9.50', '13860.00'],
                [4, '720000.01', '1800000.00', '10.70', '22500.00'],
                [5, '1800000.01', '3600000.00', '14.30', '87300.00'],
                [6, '3600000.01', '4800000.00', '19.00', '378000.00'],
            ],
            TaxAnnex::II->value => [
                [1, '0.01', '180000.00', '4.50', '0.00'],
                [2, '180000.01', '360000.00', '7.80', '5940.00'],
                [3, '360000.01', '720000.00', '10.00', '13860.00'],
                [4, '720000.01', '1800000.00', '11.20', '22500.00'],
                [5, '1800000.01', '3600000.00', '14.70', '85500.00'],
                [6, '3600000.01', '4800000.00', '30.00', '720000.00'],
            ],
            TaxAnnex::III->value => [
                [1, '0.01', '180000.00', '6.00', '0.00'],
                [2, '180000.01', '360000.00', '11.20', '9360.00'],
                [3, '360000.01', '720000.00', '13.50', '17640.00'],
                [4, '720000.01', '1800000.00', '16.00', '35640.00'],
                [5, '1800000.01', '3600000.00', '21.00', '125640.00'],
                [6, '3600000.01', '4800000.00', '33.00', '648000.00'],
            ],
            TaxAnnex::IV->value => [
                [1, '0.01', '180000.00', '4.50', '0.00'],
                [2, '180000.01', '360000.00', '9.00', '8100.00'],
                [3, '360000.01', '720000.00', '10.20', '12420.00'],
                [4, '720000.01', '1800000.00', '14.00', '39780.00'],
                [5, '1800000.01', '3600000.00', '22.00', '183780.00'],
                [6, '3600000.01', '4800000.00', '33.00', '828000.00'],
            ],
            TaxAnnex::V->value => [
                [1, '0.01', '180000.00', '15.50', '0.00'],
                [2, '180000.01', '360000.00', '18.00', '4500.00'],
                [3, '360000.01', '720000.00', '19.50', '9900.00'],
                [4, '720000.01', '1800000.00', '20.50', '17100.00'],
                [5, '1800000.01', '3600000.00', '23.00', '62100.00'],
                [6, '3600000.01', '4800000.00', '30.50', '540000.00'],
            ],
        ];
    }
}
