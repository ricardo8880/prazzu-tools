<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Application\Estimators;

use App\Core\Taxation\Contracts\TaxEstimateProviderRegistry;
use App\Core\Taxation\Data\TaxEstimateRequest;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxComparisonScenario;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxItemEstimate;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxRegimeEstimate;
use App\Tools\TaxRegimeComparator\Domain\Enums\EstimateStatus;
use App\Tools\TaxRegimeComparator\Domain\Enums\TaxRegime;

final readonly class EstimateSimplesNacional
{
    public function __construct(private TaxEstimateProviderRegistry $providers) {}

    public function execute(TaxComparisonScenario $scenario): TaxRegimeEstimate
    {
        $request = new TaxEstimateRequest(
            referenceDate: $scenario->referenceDate,
            activity: $scenario->businessActivity->value,
            monthlyRevenue: $scenario->monthlyRevenue,
            revenueLastTwelveMonths: $scenario->revenueLastTwelveMonths,
            payrollLastTwelveMonths: $scenario->payrollLastTwelveMonths,
        );

        $provider = $this->providers->resolve(TaxRegime::SimplesNacional->value, $request);

        if ($provider === null) {
            return new TaxRegimeEstimate(
                regime: TaxRegime::SimplesNacional,
                status: EstimateStatus::UnsupportedScenario,
                estimatedMonthlyTax: null,
                estimatedAnnualTax: null,
                warnings: ['Não existe estimador compatível para o cenário informado.'],
            );
        }

        $result = $provider->estimate($request);

        return new TaxRegimeEstimate(
            regime: TaxRegime::SimplesNacional,
            status: EstimateStatus::Available,
            estimatedMonthlyTax: $result->monthlyTotal,
            estimatedAnnualTax: $result->annualTotal,
            taxes: array_map(
                static fn ($item): TaxItemEstimate => new TaxItemEstimate(
                    code: $item->code,
                    label: $item->label,
                    monthlyAmount: $item->monthlyAmount,
                    annualAmount: $item->annualAmount,
                    effectiveRate: $item->effectiveRate,
                ),
                $result->items,
            ),
            assumptions: $result->assumptions,
            warnings: $result->warnings,
        );
    }
}
