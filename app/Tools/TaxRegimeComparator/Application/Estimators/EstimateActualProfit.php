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

final readonly class EstimateActualProfit
{
    public function __construct(private TaxEstimateProviderRegistry $providers) {}

    public function execute(TaxComparisonScenario $scenario): TaxRegimeEstimate
    {
        if ($scenario->indirectTaxRate === null || $scenario->monthlyPisCofinsCreditBase === null) {
            return new TaxRegimeEstimate(
                regime: TaxRegime::ActualProfit,
                status: EstimateStatus::InsufficientData,
                estimatedMonthlyTax: null,
                estimatedAnnualTax: null,
                warnings: ['Informe a alíquota efetiva de tributos indiretos e a base mensal elegível a créditos de PIS/Cofins para estimar o Lucro Real.'],
            );
        }

        $request = new TaxEstimateRequest(
            referenceDate: $scenario->referenceDate,
            activity: $scenario->businessActivity->value,
            monthlyRevenue: $scenario->monthlyRevenue,
            revenueLastTwelveMonths: $scenario->revenueLastTwelveMonths,
            payrollLastTwelveMonths: $scenario->payrollLastTwelveMonths,
            indirectTaxRate: $scenario->indirectTaxRate,
            monthlyOperatingCosts: $scenario->monthlyOperatingCosts,
            monthlyDeductibleExpenses: $scenario->monthlyDeductibleExpenses,
            monthlyPisCofinsCreditBase: $scenario->monthlyPisCofinsCreditBase,
        );

        $provider = $this->providers->resolve(TaxRegime::ActualProfit->value, $request);

        if ($provider === null) {
            return new TaxRegimeEstimate(
                regime: TaxRegime::ActualProfit,
                status: EstimateStatus::UnsupportedScenario,
                estimatedMonthlyTax: null,
                estimatedAnnualTax: null,
                warnings: ['O período, a atividade ou o cenário informado exige tratamento do Lucro Real ainda não suportado.'],
            );
        }

        $result = $provider->estimate($request);

        return new TaxRegimeEstimate(
            regime: TaxRegime::ActualProfit,
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
