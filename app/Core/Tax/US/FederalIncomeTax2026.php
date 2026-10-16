<?php

declare(strict_types=1);

namespace App\Core\Tax\US;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use InvalidArgumentException;

/**
 * Simplified 2026 federal income-tax estimator using the annual standard
 * deduction and statutory marginal brackets. It is not a replacement for
 * Form W-4 withholding worksheets or an individual tax return.
 */
final class FederalIncomeTax2026
{
    /** @return array<string, int> amounts in cents */
    public static function standardDeductions(): array
    {
        return [
            'single' => 1_610_000,
            'married_joint' => 3_220_000,
            'married_separate' => 1_610_000,
            'head_household' => 2_415_000,
        ];
    }

    /** @return list<array{up_to:?int,rate:string}> amounts in cents */
    public static function brackets(string $filingStatus): array
    {
        return match ($filingStatus) {
            'single' => [
                ['up_to' => 1_240_000, 'rate' => '10'],
                ['up_to' => 5_040_000, 'rate' => '12'],
                ['up_to' => 10_570_000, 'rate' => '22'],
                ['up_to' => 20_177_500, 'rate' => '24'],
                ['up_to' => 25_622_500, 'rate' => '32'],
                ['up_to' => 64_060_000, 'rate' => '35'],
                ['up_to' => null, 'rate' => '37'],
            ],
            'married_joint' => [
                ['up_to' => 2_480_000, 'rate' => '10'],
                ['up_to' => 10_080_000, 'rate' => '12'],
                ['up_to' => 21_140_000, 'rate' => '22'],
                ['up_to' => 40_355_000, 'rate' => '24'],
                ['up_to' => 51_245_000, 'rate' => '32'],
                ['up_to' => 76_870_000, 'rate' => '35'],
                ['up_to' => null, 'rate' => '37'],
            ],
            'married_separate' => [
                ['up_to' => 1_240_000, 'rate' => '10'],
                ['up_to' => 5_040_000, 'rate' => '12'],
                ['up_to' => 10_570_000, 'rate' => '22'],
                ['up_to' => 20_177_500, 'rate' => '24'],
                ['up_to' => 25_622_500, 'rate' => '32'],
                ['up_to' => 38_435_000, 'rate' => '35'],
                ['up_to' => null, 'rate' => '37'],
            ],
            'head_household' => [
                ['up_to' => 1_770_000, 'rate' => '10'],
                ['up_to' => 6_745_000, 'rate' => '12'],
                ['up_to' => 10_570_000, 'rate' => '22'],
                ['up_to' => 20_175_000, 'rate' => '24'],
                ['up_to' => 25_620_000, 'rate' => '32'],
                ['up_to' => 64_060_000, 'rate' => '35'],
                ['up_to' => null, 'rate' => '37'],
            ],
            default => throw new InvalidArgumentException('Unsupported federal filing status.'),
        };
    }

    public static function estimatedAnnualTax(Money $annualTaxableIncome, string $filingStatus): Money
    {
        $deductions = self::standardDeductions();
        if (! isset($deductions[$filingStatus])) {
            throw new InvalidArgumentException('Unsupported federal filing status.');
        }

        $taxableMinor = max(0, $annualTaxableIncome->minorAmount() - $deductions[$filingStatus]);
        $tax = Money::zero();
        $lower = 0;

        foreach (self::brackets($filingStatus) as $bracket) {
            $upper = $bracket['up_to'] ?? $taxableMinor;
            $band = max(0, min($taxableMinor, $upper) - $lower);
            if ($band > 0) {
                $tax = $tax->add(Money::fromMinor($band)->percentage(Percentage::fromString($bracket['rate'])));
            }
            if ($taxableMinor <= $upper || $bracket['up_to'] === null) {
                break;
            }
            $lower = $upper;
        }

        return $tax;
    }
}
