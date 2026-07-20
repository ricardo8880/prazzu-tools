<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Application\Actions;

use App\Tools\TaxRegimeComparator\Application\Data\TaxComparisonInput;
use App\Tools\TaxRegimeComparator\Application\Estimators\EstimateActualProfit;
use App\Tools\TaxRegimeComparator\Application\Estimators\EstimatePresumedProfit;
use App\Tools\TaxRegimeComparator\Application\Estimators\EstimateSimplesNacional;
use App\Tools\TaxRegimeComparator\Domain\Data\TaxComparisonResult;
use App\Tools\TaxRegimeComparator\Domain\Services\TaxComparisonRanker;

final readonly class CompareTaxRegimes
{
    public function __construct(
        private EstimateSimplesNacional $estimateSimplesNacional,
        private EstimatePresumedProfit $estimatePresumedProfit,
        private EstimateActualProfit $estimateActualProfit,
        private TaxComparisonRanker $ranker,
    ) {}

    public function execute(TaxComparisonInput $input): TaxComparisonResult
    {
        $scenario = $input->toScenario();

        return $this->ranker->rank($scenario->referenceDate, [
            $this->estimateSimplesNacional->execute($scenario),
            $this->estimatePresumedProfit->execute($scenario),
            $this->estimateActualProfit->execute($scenario),
        ]);
    }
}
