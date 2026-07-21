<?php

declare(strict_types=1);

namespace App\Tools\ProLaboreProfitDistributionCalculator\Tests\Unit;

use App\Core\Dates\Competence;
use App\Core\Money\Money;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Data\ProLaboreInput;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Enums\CompanyRegime;
use App\Tools\ProLaboreProfitDistributionCalculator\Domain\Services\ProLaboreCalculator;
use App\Tools\ProLaboreProfitDistributionCalculator\Tests\Fixtures\GoldenCases;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class GoldenCaseRegressionTest extends TestCase
{
    #[DataProvider('proLaboreCases')]
    public function test_approved_pro_labore_cases_are_reproducible(string $identifier): void
    {
        $case = collect(GoldenCases::suite()->cases)->firstWhere('identifier', $identifier);
        self::assertNotNull($case);

        $result = (new ProLaboreCalculator)->calculate(new ProLaboreInput(
            competence: Competence::fromString($case->input['competence']),
            companyRegime: CompanyRegime::from($case->input['company_regime']),
            grossAmount: Money::fromMinor($case->input['gross_minor']),
            dependents: $case->input['dependents'] ?? 0,
        ));

        $actual = [
            'social_security_minor' => $result->socialSecurityWithheld->minorAmount(),
            'irrf_minor' => $result->irrfWithheld->minorAmount(),
            'net_minor' => $result->netAmount->minorAmount(),
        ];

        foreach ($actual as $key => $value) {
            if (array_key_exists($key, $case->expected)) {
                self::assertSame($case->expected[$key], $value, $identifier.' / '.$key);
            }
        }
    }

    public static function proLaboreCases(): array
    {
        return [['typical'], ['boundary'], ['regression']];
    }
}
