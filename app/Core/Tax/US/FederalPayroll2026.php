<?php

declare(strict_types=1);

namespace App\Core\Tax\US;

use App\Core\Money\Money;
use App\Core\Money\Percentage;

/**
 * Federal payroll constants for wages paid in calendar year 2026.
 *
 * Sources: IRS Publication 15/15-A (2026) and IRS Topic 751.
 * State unemployment, workers' compensation, local payroll taxes, and other
 * state/local employer costs are intentionally supplied by the user.
 */
final class FederalPayroll2026
{
    public const SOCIAL_SECURITY_WAGE_BASE_MINOR = 18_450_000; // $184,500.00

    public const FUTA_WAGE_BASE_MINOR = 700_000; // $7,000.00

    public const ADDITIONAL_MEDICARE_WITHHOLDING_THRESHOLD_MINOR = 20_000_000; // $200,000.00

    public static function socialSecurityRate(): Percentage
    {
        return Percentage::fromString('6.2');
    }

    public static function medicareRate(): Percentage
    {
        return Percentage::fromString('1.45');
    }

    public static function additionalMedicareRate(): Percentage
    {
        return Percentage::fromString('0.9');
    }

    /** Effective FUTA rate when the maximum 5.4% state credit applies. */
    public static function futaEffectiveRate(): Percentage
    {
        return Percentage::fromString('0.6');
    }

    public static function employerSocialSecurity(Money $annualWages): Money
    {
        $taxable = Money::fromMinor(min(max(0, $annualWages->minorAmount()), self::SOCIAL_SECURITY_WAGE_BASE_MINOR));

        return $taxable->percentage(self::socialSecurityRate());
    }

    public static function employerMedicare(Money $annualWages): Money
    {
        return Money::fromMinor(max(0, $annualWages->minorAmount()))->percentage(self::medicareRate());
    }

    public static function employerFuta(Money $annualWages): Money
    {
        $taxable = Money::fromMinor(min(max(0, $annualWages->minorAmount()), self::FUTA_WAGE_BASE_MINOR));

        return $taxable->percentage(self::futaEffectiveRate());
    }

    public static function employeeSocialSecurity(Money $annualWages): Money
    {
        return self::employerSocialSecurity($annualWages);
    }

    public static function employeeMedicare(Money $annualWages): Money
    {
        return self::employerMedicare($annualWages);
    }

    public static function additionalMedicareWithholding(Money $annualWages): Money
    {
        $excess = max(0, $annualWages->minorAmount() - self::ADDITIONAL_MEDICARE_WITHHOLDING_THRESHOLD_MINOR);

        return Money::fromMinor($excess)->percentage(self::additionalMedicareRate());
    }
}
