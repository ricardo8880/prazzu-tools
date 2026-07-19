<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Application\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use App\Tools\SimplesNacionalCalculator\Domain\Services\SimplesNacionalAlertAnalyzer;

final readonly class AnalyzeSimplesNacionalAlerts
{
    public function __construct(private SimplesNacionalAlertAnalyzer $analyzer) {}

    /** @param array{annex:string,rbt12:string,monthly_revenue:string,payroll_12?:string|null,monthly_growth?:string|int|null} $input */
    /** @return array{summary:array<string,int>,alerts:list<array{level:string,title:string,message:string}>} */
    public function execute(array $input): array
    {
        $payroll = isset($input['payroll_12']) && trim((string) $input['payroll_12']) !== ''
            ? Money::fromDecimal((string) $input['payroll_12'])
            : null;
        $growth = isset($input['monthly_growth']) && trim((string) $input['monthly_growth']) !== ''
            ? Percentage::fromString((string) $input['monthly_growth'])
            : null;

        return $this->analyzer->analyze(
            TaxAnnex::from($input['annex']),
            Money::fromDecimal($input['rbt12']),
            Money::fromDecimal($input['monthly_revenue']),
            $payroll,
            $growth,
        )->toArray();
    }
}
