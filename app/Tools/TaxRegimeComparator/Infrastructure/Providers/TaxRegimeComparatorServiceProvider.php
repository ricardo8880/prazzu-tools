<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Infrastructure\Providers;

use App\Core\Taxation\Contracts\TaxEstimateProviderRegistry;
use App\Tools\TaxRegimeComparator\Application\Taxation\ActualProfitTaxEstimateProvider;
use App\Tools\TaxRegimeComparator\Application\Taxation\PresumedProfitTaxEstimateProvider;
use Illuminate\Support\ServiceProvider;

final class TaxRegimeComparatorServiceProvider extends ServiceProvider
{
    public function boot(
        TaxEstimateProviderRegistry $registry,
        PresumedProfitTaxEstimateProvider $presumedProfitProvider,
        ActualProfitTaxEstimateProvider $actualProfitProvider,
    ): void {
        $registry->register($presumedProfitProvider);
        $registry->register($actualProfitProvider);
    }
}
